<?php

namespace main\eav\object;

use main\models\File;
use main\models\User;
use main\util\DotArray;
use ObjectFactory;
use RuntimeException;
use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

abstract class Base
{
    /**
     * @var string
     */
    public $object_type;
    /**
     * @var int
     */
    public $id;
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Восстанавливает удаленную запись
     * @param int $id
     * @return static
     * @throws Exception
     */
    public static function recover($id)
    {
        try {
            ObjectFactory::load(static::typeName(), $id);
            throw new RuntimeException('can\'t recover [' . static::typeName() . ',' . $id . ']: object exists');
        } catch (BaseNotFoundException $e) {
            // запись не найдена
        }

        $targetDate = (new Query)
            ->select(new Expression('to_char(max(modifydate - interval \'1 minute\'),\'DD-MM-YYYY HH24:MI:SS\') as targetDate'))
            ->from(static::typeName() . '_data_h')
            ->where([
                'operation' => 'D',
                'o_field' => 'createUser',
                'o_id' => $id
            ])
            ->scalar();
        if (!$targetDate) {
            throw new RuntimeException('can\'t recover [' . static::typeName() . ',' . $id . ']: empty delete date');
        }

        $data = self::snapshot($id, $targetDate);
        $rows = array_map(function ($field) use ($data, $id) {
            return [$id, $field, $data[$field]];
        }, array_keys($data));

        Yii::$app->db->createCommand()->batchInsert(static::typeName() . '_data', ['o_id', 'o_field', 'o_value'], $rows)->execute();

        $o = ObjectFactory::load(static::typeName(), $id);
        Search::rebuild($o);
        Sort::rebuild($o);

        return $o;
    }

    /**
     * Возвращает атрибуты записи на определенную дату
     * @param int $id
     * @param string $date формат d-m-Y H:i:s
     * @return array
     * @throws Exception
     */
    public static function snapshot($id, $date = '01-01-2100 00:00:00')
    {
        /** @noinspection SqlResolve */
        $rows = Yii::$app->db->createCommand('
            select o_field,o_value
              from (
                 select odh.*,
                        dense_rank() over (partition by odh.o_field order by modifydate desc) rn
                   from ' . self::typeName() . '_data_h odh,
                        (select distinct o_field from ' . self::typeName() . '_data_h where o_id=:id) odh2
                  where o_id=:id
                    and odh.o_field=odh2.o_field
                    and modifydate <= to_timestamp(:date,\'DD-MM-YYYY HH24:MI:SS\')
                 ) t
               where rn=1
              and operation!=\'D\'
        ')->bindParam(':id', $id)
            ->bindParam(':date', $date)
            ->queryAll();
        return ArrayHelper::map($rows, 'o_field', 'o_value');
    }

    /**
     * @param bool $skipEmptyNames признак пропуска пустых имен
     * @return array
     */
    public static function columnRules($skipEmptyNames = false)
    {
        $rules = [];
        foreach (static::rules() as $v) {
            $fieldRegexp = is_array($v[0]) ? $v[0][0] : $v[0];
            $pattern = is_array($v[0]) ? $v[0][1] : $v[0];
            $name = is_array($v['name']) ? $v['name'][1] : $v['name'];
            if (null === $name) {
                continue;
            }
            if ($skipEmptyNames && '' === $name) {
                continue;
            }
            $callback = array_reduce($v, function ($result, $item) {
                if (is_callable($item)) {
                    $result = $item;
                }
                return $result;
            }, false);
            $rules[] = [
                'regexp' => $fieldRegexp,
                'columnName' => $name,
                'column' => $pattern,
                'callback' => $callback
            ];
        }
        return $rules;
    }

    /**
     * @param bool $skipEmptyNames признак пропуска пустых имен
     * @return array
     */
    public static function searchRules($skipEmptyNames = false)
    {
        $rules = [];
        foreach (static::rules() as $v) {
            $fieldMask = is_array($v[0]) ? $v[0][0] : $v[0];
            $pattern = is_array($v[0]) ? $v[0][1] : $v[0];
            $name = is_array($v['name']) ? $v['name'][0] : $v['name'];
            if (null === $name) {
                continue;
            }
            if ($skipEmptyNames && '' === $name) {
                continue;
            }
            $callback = array_reduce($v, function ($result, $item) {
                if (is_callable($item)) {
                    $result = $item;
                }
                return $result;
            }, false);
            $rules[] = [
                'fieldMask' => $fieldMask,
                'searchName' => $name,
                'pattern' => $pattern,
                'callback' => $callback,
                'group' => $v['search_group'] ?? null,
                'type' => $v['type'] ?? 'string'
            ];
        }
        return $rules;
    }

    /**
     * Возвращает описание атрибутов объекта
     * Формат array(
     * field_regexp string|array(field_regexp,pattern) - field_regexp - маска атрибута (регулярное выражение), pattern - объединяющее имя атрибута(название колонки)
     * name string|array(searchName,columnName) - имя атрибута (имя атрибута в поиске/имя колонки), имя=null - не включать в поиск, не обновлять sort-колонку, имя='' скрывать в списках поисковых атрибутов и колонок
     * callback function(stdClass(obj,pattern,field,value,valueNum,groupName,groupKey)) {} - функция обработки записи
     * )
     *
     *
     *
     * column missing|bool|null - не указано - есть колонка, false - признак отсутствия колонки в *_sort, null - колонка есть( не обновлять *_sort (колонка o_id)
     * type missing|array - не указано - атрибут типа string, array - спецификация типа (пока только дата - ['date', 'dd-mm-yyyy hh24:mi:ss'])
     * @return array
     */
    public static function rules()
    {
        return [
            [['createDate', 'o_id'], 'name' => 'id', 'column' => null, function ($v) {
                $v->valueNum = $v->obj->id;
                $v->value = sprintf('%06d', $v->obj->id);
                $v->field = 'o_id';
            }],
            ['createDate', 'name' => 'Дата создания', 'type' => ['date', 'dd-mm-yyyy hh24:mi:ss']],
            ['createUser', 'name' => 'Создал', function ($v) {
                $v->valueNum = $v->value;
                $v->value = $v->value ? User::findOne($v->value)->name : null;
            }],
            ['modifyDate', 'name' => [null, 'Дата изменения'],'type' => ['date', 'dd-mm-yyyy hh24:mi:ss']],
            ['modifyUser', 'name' => [null, 'Изменил'], function ($v) {
                $v->valueNum = $v->value;
                $v->value = $v->value ? User::findOne($v->value)->name : null;
            }],
            /*['groupid', 'name' => 'Группа', function ($v) {
                $g = \main\models\Group::findOne($v->value);
                $v->valueNum = $v->value;
                $v->value = $g ? $g->name : null;
            }],*/
        ];
    }

    public static function typeName()
    {
        return strtolower(substr(get_called_class(), strrpos(get_called_class(), '\\') + 1));
    }

    /**
     * Создание объекта
     * @return Base
     * @throws Exception
     */
    public static function create()
    {
        $id = ((new Query)->select('nextval(\'seq_' . self::typeName() . '\')')->one())['nextval'];
        (new Query)->createCommand()->batchInsert(self::typeName() . '_data', ['o_id', 'o_field', 'o_value'], [
            [$id, 'createDate', date('d-m-Y H:i:s')],
            [$id, 'createUser', Yii::$app->user->id],
        ])->execute();

        $o = new static($id);
        Search::rebuild($o);
        Sort::rebuild($o);
        $o->onCreate();
        return $o;
    }

    /**
     * Возвращает признак существования объекта
     * @param int $id
     * @return boolean
     */
    public static function exists($id)
    {
        try {
            new static($id);
            return true;
        } catch (BaseNotFoundException $ex) {
        }
        return false;
    }

    /**
     * Base constructor.
     * @param int $id
     * @throws BaseNotFoundException
     */
    public function __construct($id = 0)
    {
        $this->id = $id;
        $this->object_type = self::typeName();
        if ($id) {
            $this->load();
        }
    }

    public function onCreate()
    {
    }

    /**
     * @param int $otherId
     * @throws BaseNotFoundException
     */
    protected function load($otherId = 0)
    {
        if ($otherId) {
            $this->id = $otherId;
        }
        $rows = (new Query)
            ->select(['o_field',new Expression('coalesce(o_value,\'\') o_value')])->orderBy('o_field')
            ->from(self::typeName() . '_data')
            ->where(['o_id' => $this->id])
            ->indexBy('o_field')
            ->all();
        $this->data = array_map(function($v) { return $v['o_value']; }, $rows);
        if (0 == count($this->data)) {
            throw new BaseNotFoundException(self::typeName(), $this->id);
        }
    }

    /**
     * sets attribute for object
     * @param string $field
     * @param string $val
     * @throws Exception
     */
    public function setval($field, $val)
    {
        $valueOld = array_key_exists($field, $this->data) ? $this->data[$field] : null;
        if ((string)$val !== $valueOld) {
            (new Query)->createCommand()->upsert(self::typeName() . '_data',
                ['o_id' => $this->id, 'o_field' => $field, 'o_value' => $val], ['o_value' => $val])->execute();
            $this->data[$field] = $val;
            Search::update($this, $field, $val, $valueOld);
            Sort::update($this, $field, $val);
            $this->onFieldChange($field, $val, $valueOld);
        }
        $this->updateModify();
    }

    public function onFieldChange($field, $value, $valueOld)
    {
    }

    /**
     * @throws Exception
     */
    public function updateModify()
    {
        $data = [
            'modifyDate' => date('d-m-Y H:i:s', time()),
            'modifyUser' => Yii::$app->user->id
        ];
        foreach ($data as $field => $val) {
            (new Query)->createCommand()->upsert(self::typeName() . '_data',
                ['o_id' => $this->id, 'o_field' => $field, 'o_value' => $val],
                ['o_value' => $val])->execute();
            Search::update($this, $field, $val);
            Sort::update($this, $field, $val);
            $this->data[$field] = $val;
        }
    }

    public function getFields()
    {
        return array_keys($this->data);
    }

    public function getval($field, $default = null)
    {
        return array_key_exists($field, $this->data) ?
            $this->data[$field] :
            $default;
    }

    /**
     * @param string $field
     * @throws Exception
     */
    public function delval($field)
    {
        (new Query)->createCommand()->delete(self::typeName() . '_data',
            ['o_id' => $this->id, 'o_field' => $field])->execute();
        Search::update($this, $field, null, $this->data[$field] ?? null);
        Sort::update($this, $field, null);
        $this->onFieldChange($field, null, $this->data[$field] ?? null);
        unset($this->data[$field]);
    }

    /**
     * @return int[]
     */
    public function getFiles()
    {
        $rows = File::find()->select('id')->where([
            'object_type' => self::typeName(),
            'object_id' => $this->id
        ])->all();
        return ArrayHelper::getColumn($rows, 'id');
    }

    /**
     * Удаляет объект
     * @throws Exception
     */
    public function delete()
    {
        // Удаляем файлы
        foreach ($this->getFiles() as $id) {
            File::markDeleted($id);
        }
        $data = $this->data;
        $this->data = []; // очищаем атрибуты
        // Удаляем информацию объекта + поисковую информацию
        foreach ($data as $name => $value) {
            $this->onFieldChange($name, null, $value);
        }
        Search::remove($this);
        Sort::remove($this);
        (new Query)->createCommand()->delete(self::typeName() . '_data', ['o_id' => $this->id])->execute();
    }

    /**
     * @param $data
     * @param string $prefix
     * @throws Exception
     */
    public function setdata($data, $prefix = '')
    {
        $flat = DotArray::encode($data, $prefix);
        foreach ($flat as $field => $val) {
            $this->setval($field, $val);
        }
    }

    /**
     * @param $prefix
     * @return array
     */
    public function getdata($prefix)
    {
        return DotArray::decode($this->data, $prefix);
    }

    /**
     * @param $prefix
     * @throws Exception
     */
    public function deldata($prefix)
    {
        if ($prefix != '' && '.' != substr($prefix, -1)) {
            $prefix .= '.';
        }
        foreach ($this->data as $key => $value) {
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $this->delval($key);
            }
        }
    }

    /**
     * @param $prefix
     * @return array
     */
    public function getdatakeys($prefix)
    {
        return array_keys($this->getdata($prefix));
    }

    /**
     * @param $prefix
     * @param string $type
     * @return int|mixed
     */
    public function getdatacount($prefix, $type = 'count')
    {
        $keys = $this->getdatakeys($prefix);
        foreach ($keys as $k) {
            if (!is_numeric($k)) { // ошибка
                return -1;
            }
        }
        if ($type == 'count') {
            return count($keys);
        } else {
            sort($keys);
            return count($keys) > 0 ? array_pop($keys) + 1 : 1;
        }
    }

    /**
     * @param string $default
     * @return string
     */
    public function getName($default = null)
    {
        return $this->getval('name', $default ?? sprintf('#%06d', $this->id));
    }

    /**
     *
     * @param int $timestamp
     * @return Snapshot
     * @throws Exception
     */
    public function getSnapshot($timestamp)
    {
        return new Snapshot($this, $timestamp);
    }


    public function historyByPrefix($prefix = '')
    {
        return (new Query)->from($this->object_type . '_data_h t')
            ->select([
                'o_field',
                'lag(o_value) over (partition by o_field order by modifydate) o_value_old',
                'o_value',
                'operation',
                'to_char(modifydate,\'DD-MM-YYYY HH24:MI:SS\') modifydate',
                'modifyuser'
            ])
            ->andWhere(['o_id' => $this->id])
            ->andFilterWhere(['like', 'o_field', $prefix && $prefix !== '%' ? $prefix : null, false])
            ->orderBy('t.modifydate desc')
            ->all();
    }

    /**
     * @param string $date
     * @return array
     * @throws Exception
     */
    public function historySnapshot($date = '01-01-2100 00:00:00')
    {
        return self::snapshot($this->id, $date);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function updateHash()
    {
        $hash = $this->calcHash();
        if ($hash !== $this->getHash()) {
            $this->setval('version.hash', $hash);
            $this->setval('version.id', $this->getVersion() + 1);
            return true;
        }
        return false;
    }

    /**
     * Дата создания объекта в формате d-m-Y H:i:s
     * @return string
     */
    public function getCreateDate()
    {
        return $this->getval('createDate');
    }

    /**
     * Возвращает пользователя создавшего объект
     * @return User
     */
    public function getCreateUser()
    {
        $u = User::findOne($this->getval('createUser'));
        return $u ?: new User();
    }

    public function getHash()
    {
        return $this->getval('version.hash');
    }

    public function getVersion()
    {
        return $this->getval('version.id', 0);
    }

    protected function calcHash()
    {
        $data = $this->data;
        unset($data['modifyUser']);
        unset($data['modifyDate']);
        unset($data['version.id']);
        unset($data['version.hash']);
        ksort($data);
        return md5(json_encode($data));
    }

}
