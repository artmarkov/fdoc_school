<?php

namespace main\forms\control;

/**
 * @property string $defaultValue
 * @property string $allowLoadSave
 * @property bool $required
 * @property string $hint
 * @property bool $loaded
 * @property bool $modifiable
 * @property string $label
 * @property string $msgRequiredField
 */
abstract class BaseControl extends AbstractControl
{

    // common
    protected $label;
    protected $defaultValue;

    /**
     * Признак загрузки значения поля:
     * null - не загружено (загрузки не было)
     * false/true - не загружено/загружено через POST(загрузка была)
     *
     * @var bool
     */
    protected $loaded = null;
    protected $modifiable = null;
    // validation
    protected $required = false;
    protected $hint = null;
    protected $validationError;
    protected $validationWarning;
    protected $allowLoadSave = true;

    protected $msgRequiredField = 'Обязательное поле';

    public function __construct($objFieldset, $name, $label, $options = [])
    {
        parent::__construct($objFieldset, $name, null, $options);
        $this->label = $label;
    }

    public function setModifiable($modifiable)
    {
        $this->modifiable = $modifiable;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'allowLoadSave':
                $this->allowLoadSave = $val;
                break;
            case 'defaultValue':
                $this->defaultValue = $val;
                break;
            case 'required':
                $this->required = $val;
                break;
            case 'label':
                $this->label = $val;
                break;
            case 'layout':
                $this->layout = $val;
                break;
            case 'loaded':
                $this->loaded = $val;
                break;
            case 'msgRequiredField':
                $this->msgRequiredField = $val;
                break;
            case 'hint':
                $this->hint = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'allowLoadSave':
                return $this->allowLoadSave;
                break;
            case 'defaultValue':
                return $this->defaultValue;
                break;
            case 'required':
                return $this->required;
                break;
            case 'label':
                return $this->label;
                break;
            case 'layout':
                return $this->layout;
                break;
            case 'loaded':
                return $this->loaded;
                break;
            case 'modifiable':
                return $this->modifiable;
                break;
            case 'msgRequiredField':
                return $this->msgRequiredField;
                break;
            case 'hint':
                return $this->hint;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function validate($force = false)
    {
        return $this->loaded || $force ? $this->doValidate() : true;
    }

    public function doValidate()
    {
        if ($this->required == true && '' == $this->value) {
            $this->validationError = $this->msgRequiredField;
            return false;
        }
        return true;
    }

    public function load($post = false, $forceDS = false)
    {
        $this->setModifiable($this->getRenderMode() == \main\forms\core\Form::MODE_WRITE);

        if (is_null($this->loaded) || $forceDS) {
            if ($post && $this->modifiable && !$forceDS) {
                if (!$this->loadPost()) {
                    $this->doLoad();
                    $this->loaded = false;
                } else {
                    $this->loaded = true;
                }
            } else {
                $this->doLoad();
                $this->loaded = false;
            }
        }
    }

    public function doLoad()
    {
        if ($this->allowLoadSave) {
            $renderMode = $this->getRenderMode();
            $def = \main\forms\core\Form::MODE_WRITE == $renderMode ? $this->defaultValue : '';
            $this->value = $this->unserializeValue($this->ds->getValue($this->name, $def));
        }
    }

    public function save($force = false)
    {
        if (($this->loaded && $this->modifiable) || $force) {
            $this->doSave();
        }
    }

    public function doSave()
    {
        if ($this->allowLoadSave) {
            $this->ds->setValue($this->name, $this->serializeValue($this->value));
        }
    }

    protected function serializeValue($val)
    {
        return $val;
    }

    protected function unserializeValue($val)
    {
        return $val;
    }

    public function asArray()
    {
        $data = parent::asArray();
        $data['required'] = $this->required;
        $data['hint'] = $this->hint;
        $data['hidden'] = false;
        $data['label'] = $this->label;
        $data['error'] = $this->validationError;
        $data['warning'] = $this->validationWarning;
        return $data;
    }

    public function getHistory()
    {
        if (\main\forms\core\Form::MODE_NONE == $this->getRenderMode()) {
            return [];
        }
        $histDB = $this->ds->getHistory($this->name);
        $hist = [];
        $counter = 0;
        foreach ($histDB as $e) {
            if ($e['o_value'] == $e['o_value_old']) {
                continue;  // пропускаем
            }
            $key = \DateTime::createFromFormat('d-m-Y H:i:s', $e['modifydate'])->getTimestamp() .
                '_' . $this->htmlControlName . sprintf('%04d', $counter++);
            $hist[$key] = [
                'form' => $this->objFieldset->getTitle(),
                'field' => $this->htmlControlName,
                'label' => $this->objFieldset->getFieldPath($this->label),
                'value' => $this->decodeValue($this->unserializeValue($e['o_value'])),
                'value_old' => $this->decodeValue($this->unserializeValue($e['o_value_old'])),
                'op' => $e['operation'],
                'mdate' => $e['modifydate'],
                'muserid' => $e['modifyuser'],
                'musername' => $e['modifyuser_name']
            ];
        }
        return $hist;
    }

    public function getHistoryValue($value, $valueOld)
    {
        return [
            'mainForm' => $this->objFieldset->getRootForm()->getTitle(),
            'form' => $this->objFieldset->getTitle(),
            'name' => $this->name,
            'label' => $this->objFieldset->getFieldPath($this->label),
            'value' => $this->decodeValue($this->unserializeValue($value)),
            'value_old' => $this->decodeValue($this->unserializeValue($valueOld))
        ];
    }

    /**
     * @param string $validationError
     */
    public function setValidationError($validationError)
    {
        $this->validationError = $validationError;
    }

    public function getValidationError()
    {
        return $this->validationError;
    }

}
