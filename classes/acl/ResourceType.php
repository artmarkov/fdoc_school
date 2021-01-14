<?php

namespace main\acl;

use RuntimeException;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use Yii\db\Connection;

/**
 * This is the model class for table "acl_resource_type".
 *
 * @property string $type
 * @property string $class
 * @property string $root
 * @property bool $auto_register
 * @property string $actions
 *
 * @property Resource[] $resources
 */
class ResourceType extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acl_resource_type';
    }

    public static function instantiate($row)
    {
        return $row['class'] ? new $row['class'] : new self;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'class', 'root', 'actions'], 'required'],
            [['auto_register'], 'boolean'],
            [['type'], 'string', 'max' => 16],
            [['class', 'root', 'actions'], 'string', 'max' => 30],
            [['type'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Type',
            'class' => 'Class',
            'root' => 'Root',
            'auto_register' => 'Auto Register',
            'actions' => 'Actions',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getResources()
    {
        return $this->hasMany(Resource::class, ['type' => 'type']);
    }

    /**
     * @param string $name
     * @return Resource|null
     */
    public function findResource($name)
    {
        return Resource::findOne(['type' => $this->type, 'name' => $name]);
    }

    /**
     * @param string $name
     * @param string $parent
     * @return Resource
     */
    public function createResource($name, $parent = null)
    {
        $m = new Resource([
            'type' => $this->type,
            'name' => $name,
            'pid' => $parent ? $this->findResource($parent)->id : null
        ]);
        if (!$m->save()) {
            throw new RuntimeException('Can\'t create resource: ' . implode(',', $m->getErrorSummary(true)));
        }
        return $m;
    }

    /**
     * @param string $type
     * @return ResourceType
     */
    public static function typeList($type)
    {
        static $result = null;
        if (null === $result) {
            /* @var $result ResourceType[] */
            $result = self::find()->indexBy('type')->all();
            //var_dump($result);exit;
        }
        if ($type && !array_key_exists($type, $result)) {
            throw new RuntimeException('Unknown type: ' . $type);
        }
        return $result[$type];
    }

    public function getParent($name, $delimiter = ':')
    {
        $x = explode($delimiter, $name);
        array_pop($x);
        return count($x) > 0 ? implode($delimiter, $x) : null;
    }

    public function getParentList($name)
    {
        $list = [];
        do {
            $parentName = $this->getParent($name);
            $list[$name] = is_null($parentName) ? $this->root : $parentName;
            $name = $parentName;
        } while ($parentName != null);
        return array_reverse($list, true);
    }

    /**
     * @param array|string $params
     * @return string
     */
    public function makeName($params)
    {
        return is_array($params) ? $params[0] : $params;
    }

    /**
     * @param string $action
     * @return int
     */
    public function getActionBit($action)
    {
        $bit = array_search($action, explode(',', $this->actions));
        if (false === $bit) {
            throw new RuntimeException('action=' . $action . ' is not valid for this resource type=' . $this->type);
        }
        return $bit;
    }

    /**
     * @param string $name
     * @throws \Throwable
     */
    public function register($name)
    {
        $t = $this;
        $parentList = $this->getParentList($name);
        Yii::$app->db->transaction(function ($db) use ($parentList, $t) {
            /* @var $db Connection */
            $db->createCommand(
                'select class from acl_resource_type where type=:type for update',
                ['type' => $t->type]
            )->queryOne();
            foreach ($parentList as $name => $parent) {
                if (null !== $t->findResource($name)) {
                    continue;
                }
                $m = $t->createResource($name, $parent);
                $m->rebuildAcl();
            }
        });
    }

}
