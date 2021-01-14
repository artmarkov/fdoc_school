<?php

abstract class pagetab_AbstractMergeWizard extends pagetab_Abstract
{
    protected $data = false;
    protected $url;
    /**
     * @var \fdoc\eav\object\Base
     */
    protected $id;
    /**
     * @var \fdoc\models\User
     */
    protected $user;
    protected $objects;
    protected $exitUrl;
    protected $deps = [];
    protected $var = [];
    protected $disallowedFields = ['version.hash', 'version.id', 'createDate', 'createUser', 'modifyDate', 'modifyUser'];

    const AFFECTED_OBJ_TYPE = ['client'];

    /**
     * pagetab_AbstractMergeWizard constructor.
     * @param $id
     * @param $obj
     * @param $url
     * @param $user
     */
    public function __construct($id, $obj, $url, $user)
    {
        parent::__construct();
        $this->viewTmpl = 'MergeWizard.phtml';
        $this->objects = $obj;
        $this->id = $id;
        $this->url = $url;
        $this->user = $user;
    }

    protected function setViewParams($view)
    {
        $view->url = $this->url;
        $view->exitUrl = $this->exitUrl;
        $view->info = $this->getInfo();
        $view->default = $this->getDefault();
        $view->deps = $this->getDepsInfo();
        $view->links = $this->getLinks();
    }

    protected function processPost($req)
    {
        if ($req->post('merge')) {
            $data = $this->getMainData($req->post());
            $main_id = $req->post('main_id');
            $this->objects[$main_id]->setdata($data);
            $this->callActionHandler($main_id);
            unset($this->objects[$main_id]);
            $this->delete($main_id);
            return $main_id;
        }
        return false;
    }

    protected function getMainData($post)
    {
        $data = [];
        $values = $this->getValues();
        foreach ($post as $field => $key) {
            if (strpos($field, 'f:') !== false) {
                $field = str_replace('f:', '', $field);
                $field_rec = str_replace(':', '.', $field);
                isset($values[$field][$key]) ? $data[$field_rec] = $values[$field][$key] : null;
            }
        }
        return $data;
    }

    public function getExitUrl()
    {
        return $this->exitUrl;
    }

    public function setExitUrl($exitUrl)
    {
        $this->exitUrl = $exitUrl;
        return $this;
    }

    protected function getDefault()
    {
        return $this->id;
    }

    protected function getInfo()
    {
        return [
            'objects' => $this->objects,
            'data' => $this->makeData(),
        ];
    }

    protected function getValue($array)
    {
        foreach ($array as $k => $f) {
            $this->var[$f[0]] = $f[1];
        }
        return $this;
    }

    protected function addDependency($code, $name, $manager, $actionHandler)
    {
        $this->deps[$code] = [
            'name' => $name,
            'manager' => $manager,
            'handler' => $actionHandler,
        ];
        return $this;
    }

    protected function getDepsInfo()
    {
        $result = [];
        foreach ($this->deps as $name => $meta) {
            foreach ($this->callManagerHandler($meta) as $key => $m) {
                /* @var $m manager_Base */
                $data = $m->exportRowArray();
                $data['linkCallback'] = function ($id) use ($m) {
                    return $m->getEditUrl(['id' => $id]);
                };
                $result[$name]['name'] = $meta['name'];
                $result[$name]['content'][$key] = $data;
            }
        }
        return $result;
    }

    protected function callManagerHandler($meta)
    {
        $data = [];
        $manager = $meta['manager'];
        foreach ($this->objects as $key => $object) {
            $data[$object->id] = $manager($object->id, $this->user);
        }
        return $data;
    }

    protected function callActionHandler($main_id)
    {
        foreach ($this->deps as $name => $meta) {
            $handler = $meta['handler'];
            foreach ($this->callManagerHandler($meta) as $key => $m) {
                $data = $m->exportRowArray();
                foreach ($data['list'] as $v) {
                    $handler($v['id'], $main_id);
                }
            }
        }
    }

    protected function getLabel()
    {
        $label = [];
        $values = [];
        $values_label = array_keys($this->getValues());
        foreach ($values_label as $key => $field) {
                $values[$field] = $field;
        }
        foreach ($this->objects as $object) {
            $formClass = $object->getFormId();
            $f = new $formClass($object, null);
            foreach ($f->getFieldList() as $field) {
                $fl = str_replace('.', ':', $field);
                $label[$fl] = $f->getField($field)->label;
                if(in_array($fl, $values)) {
                    unset($values[$fl]);
                }
            }
            foreach ($f->getFieldsetList() as $k => $fieldset) {
                $fs = $f->getFieldset($fieldset);
                $label[$k] = ['fieldset' => $fieldset, 'label' => $fs->getTitle()];
                $fields = [];
                foreach ($fs->getFieldList() as $kk => $field) {
                    $fl = str_replace('.', ':', $field);
                    $fields[$fieldset . ':' . $fl] = $fs->getField($field)->label;
                    if(in_array($fieldset . ':' . $fl, $values)) {
                        unset($values[$fieldset . ':' . $fl]);
                    }
                }
                $label[$k] += ['fields' => $fields];
            }
        }
        return array_merge($label, $values);
    }

    protected function makeLabels()
    {
        $arr = [];
        $labels = $this->getLabel();
        foreach ($this->fields as $key => $f) {
            if (is_array($f)) {
                $arr[$key] = $f['clone'] ? $this->getCloneDataLabels($labels, $f) : $this->getDataLabels($labels, $f);
            } else {
                $key = str_replace('.', ':', $key);
                if (isset($labels[$key]) && in_array($labels[$key], $labels)) {
                    $arr[$key] = $f;
                    unset($labels[$key]);
                }
            }
        }
        return array_merge($labels, $arr);
    }

    protected function getDataLabels(&$labels, $f)
    {
        $ff = [];
        foreach ($f['fields'] as $field => $label) {
            $field = str_replace('.', ':', $field);
            if (isset($labels[$field]) && in_array($labels[$field], $labels)) {
                $ff[$field] = $label;
                unset($labels[$field]);
            }
        }
        $arr = [
            'fieldset' => $f['fieldset'],
            'label' => $f['label'],
            'fields' => $ff
        ];
        return $arr;
    }

    protected function getCloneDataLabels(&$labels, $f)
    {
        $i = 1;
        $ff = [];
        do {
            $continue = false;
            foreach ($f['fields'] as $field => $label) {
                $field = $f['fieldset'] . '.' . $i . '.' . $field;
                $field = str_replace('.', ':', $field);
                if (isset($labels[$field]) && in_array($labels[$field], $labels)) {
                    $ff[$field] = $label . '-' . $i;
                    unset($labels[$field]);
                    $continue = true;
                }
            }
            $i++;
        } while ($continue);
        $arr = [
            'fieldset' => $f['fieldset'],
            'label' => $f['label'],
            'fields' => $ff
        ];
        return $arr;
    }

    protected function makeData()
    {
        $values = $this->getValues(false);
        $labels = $this->makeLabels();
        $arr = [];
        foreach ($labels as $key => $f) {
            if (is_array($f)) {
                $ff = [];
                foreach ($f['fields'] as $field => $label) {
                    if (isset($values[$field]) && in_array($values[$field], $values)) {
                        $ff[$field] = [
                            'label' => $label,
                            'values' => $values[$field],
                        ];
                    }
                }
                if (!empty($ff)) {
                    $arr[] = [
                        'fieldset' => $f['fieldset'],
                        'label' => $f['label'],
                        'fields' => $ff
                    ];
                }
            } else {
                if (isset($values[$key]) && in_array($values[$key], $values)) {
                    $arr[$key] = [
                        'label' => $f,
                        'values' => $values[$key],
                    ];
                }
            }
        }
        return $arr;
    }

    protected function getValues($rec = true)
    {
        $value = [];
        foreach ($this->objects as $key => $object) {
            foreach ($this->getAllowedFields($object) as $k => $field) {
                $f = str_replace('.', ':', $field);
                $object->getVal($field) ? $value[$f][$key] = ((isset($this->var[$field]) && !$rec) ? $this->var[$field]($object->getVal($field)) : $object->getVal($field)) : null;
            }
        }
        return $value;
    }

    protected function delete($main_id)
    {
        foreach ($this->objects as $id => $object) {
            $object->setVal('merged_to', $main_id);
            $object->delete();
        }
    }

    /**
     * @param \fdoc\eav\object\Base $object
     * @return array
     */
    protected function getAllowedFields($object)
    {
        return array_diff($object->getFields(), $this->disallowedFields);
    }

    /**
     * @param string $objType тип объекта
     * @return bool
     */
    public static function isAffectedObject($objType)
    {
        return in_array($objType, self::AFFECTED_OBJ_TYPE);
    }

}