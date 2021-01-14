<?php

namespace main\forms\core;

class Dynamic extends Form
{
    protected $msgAddActionName = 'Добавить';
    protected $msgDelActionName = 'Удалить';
    protected $instances = [];
    protected $requireOneElement = true;

    protected function loadInstances($forceDS)
    {
        $list = $this->getInstanceIds($forceDS);
        foreach ($list as $id) {
            $fs = $this->addInstance($id);
            $fs->setDisplayMode($this->getDisplayMode());
            $fs->applyAuth();
        }
    }

    protected function applyAuth()
    {
        foreach ($this->getFieldList() as $fn) {
            $this->getField($fn)->applyAuth(Form::MODE_WRITE);
        }
        foreach ($this->getActionList() as $an) {
            $this->getActionControl($an)->applyAuth(Form::MODE_WRITE);
        }
    }

    /**
     * Загружает подформы
     *
     * @param bool $post признак источника загрузки POST/GET
     * @param bool $forceDS флаг загрузки из datasource
     */
    protected function loadValues($post = false, $forceDS = false)
    {
        $this->loadInstances($forceDS);
        foreach ($this->getInstanceList() as $id) {
            $this->getInstance($id)->loadValues($post, $forceDS);
        }
        $this->onAfterLoad();
    }

    protected function getInstanceIds($forceDS = false)
    {
        if ($this->getRootForm()->isPosted() && !$forceDS) { // Получаем список из POST
            $list = [];
            foreach (array_keys($_POST) as $k) {
                if (preg_match('/^' . $this->prefix . self::$htmlNameDelimiter . '(-?\d+)/', $k, $matches)) {
                    $list[$matches[1]] = 1;
                }
            }
            $list = array_keys($list);
        } else { // Получаем список от DataSource
            $list = $this->getDataSource()->getList();
            if (0 == count($list)) {
                $list = $this->requireOneElement ? [1] : [];
            }
        }
        sort($list);
        return $list;
    }

    public function addInstance($id)
    {
        $ds = $this->getDataSource()->inherit($id);
        $title = $this->title;

        $i = new Form(
            $this->createFieldName($id),
            $title,
            $ds,
            $this->objAuth,
            $this->getRootForm()
        );
        $i->setFieldPath($this->fieldPath . ' [' . $id . ']');
        $this->instances[$id] = $i;

        foreach ($this->getFieldList() as $fn) {
            $f = $this->getField($fn);
            $new_f = clone $f;
            $new_f->objFieldset = $i;
            $i->addObjField($new_f);
        }

        foreach ($this->getFieldsetList() as $fs) {
            $f = $this->getFieldset($fs);
            $new_f = clone $f;
            $new_f->setDataSource($ds->inherit($fs));
            $new_f->setPrefix($this->prefix . ':' . $id . ':' . $fs);
            $new_f->setFieldPath($this->fieldPath . ' [' . $id . '] : ' . $new_f->getTitle());
            $i->addObjFieldset($fs, $new_f);
        }
        return $i;
    }

    public function delInstance($id)
    {
        if (array_key_exists($id, $this->instances)) {
            unset($this->instances[$id]);
        }
    }

    protected function saveValues($force = false)
    {
        $ds_list = $this->getInstanceIds(true); // список подформ из бд
        foreach ($this->getInstanceList() as $id) {
            $fs = $this->getInstance($id);
            if (null == $fs->getDataSource(true)) {
                $fs->setDataSource($this->getDataSource()->inheritNew());
            }
            $fs->saveValues($force);
            $key = array_search($id, $ds_list);
            if (false !== $key) {
                unset($ds_list[$key]);
            }
        }
        foreach ($ds_list as $id) {
            $this->getDataSource()->inherit($id)->delete();
        }
    }

    protected function asArray()
    {
        $data = parent::asArray();
        $data['fields'] = [];
        $data['instances'] = [];
        foreach ($this->getInstanceList() as $id) {
            $data['instances'][$id] = $this->getInstance($id)->asArray();
        }
        $data['template'] = $this->getTemplate()->asArray();
        return $data;
    }

    public function getTemplate()
    {
        $t = new Form(
            $this->createFieldName('{{id}}'),
            $this->title . '[{{id}}]',
            null,
            $this->objAuth,
            $this->getRootForm()
        );

        foreach ($this->getFieldList() as $fn) {
            $f = $this->getField($fn);
            $new_f = clone $f;
            $new_f->objFieldset = $t;
            $t->addObjField($new_f);
        }

        $prefix = str_replace('{{parentId}}', '{{pparentId}}', $this->prefix); // ugly fix for 3-level deep dyn forms
        foreach ($this->getFieldsetList() as $fs) {
            $f = $this->getFieldset($fs);
            $new_f = clone $f;
            $new_f->setPrefix($prefix . ':{{parentId}}:' . $fs);
            $t->addObjFieldset($fs, $new_f);
        }
        return $t;
    }

    public function getInstance($id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        } else {
            throw new FormException('instance "' . $id . '" was not found');
        }
    }

    public function getInstanceList()
    {
        return array_keys($this->instances);
    }

    protected function validate($force = false)
    {
        $res = true;
        // свои поля и fieldsets не проверяем - это шаблон для копий (instnces)
        // Проверка экземпляров
        foreach ($this->getInstanceList() as $id) {
            $fset = $this->getInstance($id);
            if ($fset->getDisplayMode() == \main\forms\core\Form::MODE_WRITE) { // проверяем только форму доступные на запись
                $res &= $fset->validate($force);
            }
        }
        return $res;
    }

    public function getHistory()
    {
        $h = [];
        $ids = range(1, 10);
        $instanceList = $this->getInstanceList();
        foreach ($ids as $id) {
            if (in_array($id, $instanceList)) {
                foreach ($this->getInstanceList() as $id) {
                    $h = $h + $this->getInstance($id)->getHistory();
                }
            } else {
                $this->addInstance($id);
                $h = $h + $this->getInstance($id)->getHistory();
                $this->delInstance($id);
            }
        }
        krsort($h);
        return $h;
    }

    public function lookupField($storeFieldName, $value, $valueOld)
    {
        $instanceList = $this->getInstanceList();
        if (preg_match('/^' . $this->getDataSource()->getPrefix() . '\.([\d+])\./', $storeFieldName, $m)) {
            $id = $m[1];
            if (in_array($id, $instanceList)) {
                $result = $this->getInstance($id)->lookupField($storeFieldName, $value, $valueOld);
                if ($result) {
                    return $result;
                }
            } else {
                $this->addInstance($id);
                $result = $this->getInstance($id)->lookupField($storeFieldName, $value, $valueOld);
                if ($result) {
                    return $result;
                }
                $this->delInstance($id);
            }
        }
        return false;
    }

    public function setRequireOneElement($flag)
    {
        $this->requireOneElement = $flag;
        return $this;
    }

}

