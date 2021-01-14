<?php

namespace main\forms\datasource;

use \main\eav\object\Base as object_Base;

/**
 * Класс источника данных для формы на основе объектов (класс object)
 *
 * @package form
 */
class DbObject implements DatasourceInterface
{

    /**
     * Разделитель в названии полей
     *
     * @var string
     */
    static protected $delimiter = '.';

    /**
     * Массив подчиненных источников данных
     *
     * @var array
     */
    protected $subDataSources = [];

    /**
     * Экземпляр объекта БД (класс object)
     *
     * @var object_Base
     */
    protected $obj;

    /**
     * Префикс названий полей
     *
     * @var string
     */
    protected $prefix;

    /**
     * Префикс названий полей с разделителем
     *
     * @var string
     */
    protected $delimitedPrefix;

    /**
     * Конструктор источника данных
     *
     * @param object_Base $obj (subclass of object_Base)
     * @param string $prefix
     */
    public function __construct($prefix = '', $obj = null)
    {
        $this->obj = $obj;
        $this->prefix = $prefix;
        $this->delimitedPrefix = ($prefix != '' ? $prefix . self::$delimiter : '');
    }

    /**
     * Возвращает признак нового объекта
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->getObjId() == 0;
    }

    /**
     * Возвращает объект БД
     * @return object_Base
     */
    public function getObj()
    {
        return $this->obj;
    }

    /**
     * Возвращает id объекта БД
     *
     * @return integer
     */
    public function getObjId()
    {
        return $this->obj->id;
    }

    /**
     * Возвращает тип объекта БД
     *
     * @return integer
     */
    public function getObjType()
    {
        return $this->obj->object_type;
    }

    /**
     * Действия перед началом сохранения данных
     *
     * @throws \yii\db\Exception
     */
    public function beforeSave()
    {
        if ($this->isNew()) {
            $o = \ObjectFactory::create($this->getObjType());
            $this->processNewObject($o);
            $this->linktoObject($o);
        }
    }

    /**
     * Действия после сохранения данных
     *
     * @throws \yii\db\Exception
     */
    public function afterSave()
    {
        $this->obj->updateHash();
    }

    /**
     * Настройка свойств созданного объекта
     * Проставление группы и пр.
     *
     * @param object_Base $obj (subclass of object)
     */
    function processNewObject(&$obj)
    {

    }

    /**
     * Прикрепляет к источнику данных новый объект БД
     *
     * @param object_Base $obj (subclass of object)
     */
    public function linktoObject($obj)
    {
        if (!($obj instanceof object_Base)) {
            throw new \RuntimeException('$obj is expecting to be a subclass of "object_Base", instead: ' . get_class($obj));
        }
        $this->obj = $obj;
        foreach ($this->subDataSources as $ds) {
            $ds->linktoObject($obj);
        }
    }

    /**
     * Возвращает значение поля
     *
     * @param string $field название поля
     * @param string $default значение по умолчанию
     * @return string значение поля
     */
    public function getValue($field, $default = null)
    {
        $field = $this->translateField($field);
        if ($this->obj->id != 0) {
            return $this->obj->getval($this->delimitedPrefix . $field, $default);
        } else {
            return $default;
        }
    }

    /**
     * Устанавливает значение поля
     *
     * @param string $field название поля
     * @param string $value значение поля
     * @throws \yii\db\Exception
     */
    public function setValue($field, $value)
    {
        $field = $this->translateField($field);
        if ($this->obj->id != 0) {
            $this->obj->setval($this->delimitedPrefix . $field, $value);
        } else {
            throw new \RuntimeException(
                'attempt to setValue(' . $this->delimitedPrefix . $field . ',' . $value . ') to object with id=0'
            );
        }
    }

    /**
     * Удаляет значение поля
     * @param string $field название поля
     * @throws \yii\db\Exception
     */
    public function delValue($field)
    {
        $field = $this->translateField($field);
        if ($this->obj->id != 0) {
            $this->obj->delval($this->delimitedPrefix . $field);
        } else {
            throw new \RuntimeException(
                'attempt to delValue(' . $this->delimitedPrefix . $field . ') to object with id=0');
        }
    }

    /**
     * Создает подчиненный источник данных и возвращает его экземпляр
     * @param string $prefix
     * @return Object
     */
    public function inherit($prefix)
    {
        if ($prefix == '') {
            return $this;
        } else {
            $class = get_class($this);
            $ds = new $class($this->delimitedPrefix . $prefix, $this->obj);
            $this->subDataSources[$prefix] = $ds;
            return $ds;
        }
    }

    /**
     * Сохраняет список значений
     *
     * @param string $name название поля
     * @param array $data список значений
     * @param string $keyFormat формат для ключа
     * @throws \yii\db\Exception
     */
    public function setValueList($name, $data, $keyFormat = '')
    {
        $keysOld = $this->obj->getdatakeys($this->delimitedPrefix . $name);
        $keys2Delete = array_diff(
            $keysOld,
            $keyFormat ?
                array_map(
                    function ($v) use ($keyFormat) {
                        return sprintf($keyFormat, $v);
                    },
                    array_keys($data)
                ) :
                array_keys($data)
        );
        foreach ($keys2Delete as $k) {
            $this->delValue($name . '.' . ($keyFormat ? sprintf($keyFormat, $k) : $k));
        }
        foreach ($data as $k => $value) {
            $this->setValue($name . '.' . ($keyFormat ? sprintf($keyFormat, $k) : $k), $value);
        }
    }

    /**
     * Возвращает список значений
     *
     * @param string $name название поля
     * @param array $data список значений
     * @return array
     */
    public function getValueList($name)
    {
        $result = [];
        $keys = !$this->isNew() ? $this->obj->getdatakeys($this->delimitedPrefix . $name) : [];
        sort($keys);
        foreach ($keys as $k) {
            $result[$k] = $this->getValue($name . '.' . $k);
        }
        return $result;
    }

    /**
     * Возвращает все данные источника данных в виде ассоциативного
     * массива (поле=>значение)
     *
     * @return array
     */
    public function getData()
    {
        if ($this->obj->id != 0) {
            return $this->obj->getdata($this->prefix);
        } else {
            throw new \RuntimeException('attempt to getData of empty object');
        }
    }

    /**
     * Удаляет все данные источника данных
     *
     * @throws \yii\db\Exception
     */
    public function delete()
    {
        if ($this->obj->id != 0) {
            $this->obj->deldata($this->prefix);
        } else {
            throw new \RuntimeException('attempt to delete data of empty object');
        }
    }

    /**
     * (Для множественных форм)
     * Возвращает массив ключей(id) групп данных
     *
     * @return array
     */
    public function getList()
    {
        if ($this->obj->id == 0) {
            return [];
        }
        $res = $this->obj->getdatakeys($this->prefix);
        foreach ($res as $v) {
            if (!is_numeric($v)) {
                throw new \RuntimeException(
                    'List of fieldsets must be a number set. Possible ' .
                    'invalid prefix(' . $this->prefix . '). Invalid index: ' . $v
                );
            }
        }
        return $res;
    }

    /**
     * (Для множественных форм)
     * Возвращает экземпляр источника данных созданный для новой
     * группы данных
     *
     * @return Object
     */
    public function inheritNew($id = null)
    {
        $id = is_null($id) ? $this->getNextId() : $id;
        return $this->inherit($id);
    }

    /**
     * (Для множественных форм)
     * Генерирует новый id для группы данных
     *
     * @return integer
     */
    protected function getNextId()
    {
        if ($this->obj->id != 0) {
            $res = $this->obj->getdatakeys($this->prefix);
            return count($res) > 0 ? max($res) + 1 : 1;
        } else {
            throw new \RuntimeException('attempt to getNextId() of empty object');
        }
    }

    /**
     * (Для истории значений)
     * Возвращает исторю значений поля
     *
     * @param string $field название поля
     * @return array
     */
    public function getHistory($field)
    {
        static $HIST_DATA = [];
        if (!is_null($field)) {
            $field = $this->translateField($field);
        }
        if ($this->obj->id != 0) {
            $id = $this->getObjId();
            if (!array_key_exists($id, $HIST_DATA)) {
                $HIST_DATA[$id] = [];
            }
            $mask = $this->delimitedPrefix . '%';
            if (!array_key_exists($mask, $HIST_DATA[$id])) {
                $HIST_DATA[$id][$mask] = $this->obj->historyByPrefix($mask);
            }
            $hist = [];
            foreach ($HIST_DATA[$id][$mask] as $e) {
                if ($e['o_field'] == $this->delimitedPrefix . $field || is_null($field)) {
                    $e['modifyuser_name'] = $this->getHistoryUserName($e['modifyuser']);
                    $hist[] = $e;
                }
            }
            return $hist;
        } else {
            throw new \RuntimeException('attempt to getHistory() of empty object');
        }
    }

    public function getFieldStorePath($field)
    {
        return $this->delimitedPrefix . $this->translateField($field);
    }

    /**
     * (Для истории значений)
     * Возвращает имя пользователя по его id
     *
     * @param integer $user_id
     * @return string
     */
    protected function getHistoryUserName($user_id)
    {
        static $USERS = ['0' => 'Администратор'];
        if (!array_key_exists($user_id, $USERS)) {
            $u = \main\models\User::findOne($user_id);
            $USERS[$user_id] = $u ? $u->name : '';
        }
        return $USERS[$user_id];
    }

    protected function translateField($field)
    {
        return str_replace(['9', '8'], ['-', '.'], $field);
    }

    public function saveFile($file_array)
    {
        $id_file = $this->obj->savefile($file_array);
        return $id_file;
    }

    public function loadFile($fileId)
    {
        return $this->obj->loadfile($fileId);
    }

    /**
     * Удаляет файл
     *
     */
    public function deleteFile($file_id)
    {
        $this->obj->deletefile($file_id);
    }

    public function restoreFile($file_id)
    {
        $this->obj->restoreFile($file_id);
    }

    public function getFileDeletePermission($id_file)
    {
        return \Yii::$app->user->can('delete@object', [$this->getObjType()]);
    }

    public function getUserInfo($userid)
    {
        throw new \RuntimeException('unimplemented');
        /*
        $u = ObjectFactory::user($userid);
        $groupid = $u->getval('groupid');
        $res['id'] = $u->id;
        $res['groupid'] = $groupid;
        $res['groupname'] = Group::find($groupid)->getName();
        $res['occupation'] = $u->getval('occupation');
        $res['intphone'] = $u->getval('intphone');
        $res['extphone'] = $u->getval('extphone');
        $res['simphone'] = $u->getval('simphone');
        $res['mobphone'] = $u->getval('mobphone');
        $res['email'] = $u->getval('email');
        return $res;*/
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getVersion()
    {
        return $this->obj->getVersion();
    }

    public function getVersionList()
    {
        $result = [];
        $data = $this->obj->historyByPrefix('version.id');
        foreach ($data as $v) {
            $result[$v['o_value']] = \DateTime::createFromFormat('d-m-Y H:i:s', $v['modifydate'])->getTimestamp();
        }
        return $result;
    }

}
