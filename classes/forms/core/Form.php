<?php

namespace main\forms\core;

/**
 * Класс формы.
 *
 * @package form
 */

use main\forms\auth\Base as AuthBase;
use main\forms\datasource\DatasourceInterface;

class Form
{
    /**
     * преобразует psr0 имя класса контрола в
     * @param $class string
     * @param string $prefix
     * @param string $namespace
     * @return mixed
     * @deprecated
     */
    protected static function translatePsr0Class($class, $prefix = 'form_control_', $namespace = '\\main\\forms\\control\\')
    {
        return false !== strpos($class, '_') ?
            str_replace($prefix, $namespace, $class) :
            $class;
    }

    /**
     * Перечисление: Режим отображения формы
     *
     * Ничего не показывать
     */
    const MODE_NONE = 0;

    /**
     * только чтение (html элементы формы в disabled состоянии)
     */
    const MODE_READ = 1;

    /**
     * редактирование (html элементы формы)
     */
    const MODE_WRITE = 2;

    /**
     * отображение (Только гипертекст, без html элементов формы)
     */
    const MODE_DISPLAY = 9;

    /**
     * URL страницы формы
     * Только для формы c objParentForm==null
     *
     * @var string
     */
    protected $url;

    /**
     * html имя формы
     * Только для формы c objParentForm==null
     *
     * @var string
     */
    protected $formName;

    /**
     * Генератор html-кода формы
     * Только для формы c objParentForm==null
     *
     * @var object
     */
    protected $objRenderer;

    /**
     * Желаемый режим отображения формы
     *
     * @var int
     */
    protected $displayMode = Form::MODE_WRITE;

    /**
     * Фактический режим отображения формы (с учетом прав доступа к форме)
     *
     * @var int
     */
    protected $displayModeReal;
    protected $actionControlName = 'action';

    /**
     * Разделитель используемый в html именах элементов формы
     *
     * @var string
     */
    protected static $htmlNameDelimiter = ':';

    /**
     * Экземпляр источника данных для формы
     *
     * @var DatasourceInterface
     */
    protected $objDataSource;

    /**
     * Экземпляр формы верхнего уровня
     *
     * @var object
     */
    protected $objRootForm = null;

    /**
     * Экземпляр объекта авторизации
     *
     * @var object
     */
    protected $objAuth = null;

    /**
     * Имя макета представления
     *
     * @var string
     */
    protected $layoutName;

    /**
     * Параметры макета представления
     *
     * @var array
     */
    protected $layoutParams = [];

    /**
     * Заголовок формы
     *
     * @var string
     */
    protected $title;

    /**
     * Расширенный заголовок формы (с учетом иерархии)
     *
     * @var string
     */
    protected $fieldPath;

    /**
     * Префикс формы - псевдо-имя формы с учетом иерархии
     *
     * @var string
     */
    protected $prefix;

    /**
     * Коллекция полей формы
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Коллекция вложенных форм(подформ) формы
     *
     * @var array
     */
    protected $fieldsets = [];

    /**
     * Коллекция действий формы
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Уровень вложенности формы
     *
     * @var integer
     */
    protected $nestingLevel = 0;

    /**
     * Флаг загрузки данных из POST
     *
     * @var bool
     */
    protected $postLoaded = false;

    /**
     * Действие по умолчанию
     *
     * @var string
     */
    protected $defaultAction;
    protected $logger;
    protected $msgAccessActionName = 'Список доступа';

    /* пул адресов по клиенту для автозаполнения */
    protected $address;

    /**
     * @param string $prefix
     * @param string $title заголовок формы
     * @param object $objDataSource объект источника данных
     * @param object $objAuth объект авторизации
     * @param null $objRootForm
     */
    public function __construct($prefix, $title, $objDataSource, $objAuth, $objRootForm = null)
    {
        $this->logger = null; //Log::get(get_class($this));

        if (!is_null($objRootForm) && !($objRootForm instanceof Form)) {
            throw new FormException('$objRootForm is expected to be a subclass of form\core\Form');
        }
        $this->objRootForm = $objRootForm;

        $this->setDataSource($objDataSource);

        if (!($objAuth instanceof AuthBase)) {
            throw new FormException('objAuth is expected to be a subclass of forms\auth\Base');
        }
        $this->objAuth = $objAuth;

        $this->title = $title;
        $this->prefix = $prefix;

        if ($this->isRootForm()) { // Для Root формы prefix=formName
            $this->formName = $prefix ? $prefix : 'form';
        }
        $this->init();
    }

    protected function isRootForm()
    {
        return is_null($this->objRootForm);
    }

    public function getRootForm()
    {
        return $this->isRootForm() ? $this : $this->objRootForm;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setLayoutName($name)
    {
        $this->layoutName = $name;
    }

    public function getLayoutName()
    {
        return $this->layoutName;
    }

    public function setLayoutParams($param)
    {
        if (!is_array($param)) {
            throw new FormException('"param" is expected to be an array');
        }
        $this->layoutParams = $param;
    }

    public function getLayoutParams()
    {
        return $this->layoutParams;
    }

    public function setRenderer($objRenderer)
    {
        if (!$this->isRootForm()) {
            throw new FormException('Only root form has renderer');
        }
        $this->objRenderer = $objRenderer;
    }

    public function setDataSource($ds)
    {
        $this->objDataSource = $ds;
    }

    public function getDataSource($nullAllowed = false)
    {
        if (!$nullAllowed && is_null($this->objDataSource)) {
            throw new FormException('objDataSource hasn\'t set');
        }
        return $this->objDataSource;
    }

    public function setDisplayMode($mode)
    {
        $this->displayMode = $mode;
        foreach ($this->getFieldsetList() as $fsn) {
            $this->getFieldset($fsn)->setDisplayMode($mode);
        }
    }

    public function getDisplayMode()
    {
        return $this->displayMode;
    }

    /**
     * Возвращает заголовок формы
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setUrl($url)
    {
        if (!$this->isRootForm()) {
            throw new FormException('Only root form has url');
        }
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->isRootForm() ? $this->url : $this->objRootForm->getUrl();
    }

    /**
     * Возвращает html имя формы
     *
     * @return string
     */
    public function getFormName()
    {
        return $this->isRootForm() ? $this->formName : $this->objRootForm->getFormName();
    }

    /**
     * Возвращает имя action-поля
     *
     * @return string
     */
    public function getActionControlName()
    {
        return $this->getRootForm()->
        getField($this->actionControlName)->htmlControlName;
    }

    public function setPostLoaded($flag)
    {
        $this->postLoaded = $flag;
    }

    public function getPostLoaded()
    {
        return $this->postLoaded;
    }

    /**
     * Инициализирует класс формы.
     * Создание служебных полей, действий и т.п.
     *
     */
    protected function init()
    {
        if ($this->isRootForm()) {
            // Для root формы добавляем action
            $this->addField('main\forms\control\ActionHidden', $this->actionControlName, '');
            if (isset($_GET['printview'])) {
                $this->setDisplayMode(Form::MODE_DISPLAY);
            }
        }
    }

    /**
     * Возвращает результат проверки(validation) значений формы
     * @param bool $force
     * @return bool
     */
    protected function validate($force = false)
    {
        // Проверка своих полей
        $res = true;
        foreach ($this->getFieldList() as $fn) {
            $f = $this->getField($fn);
            $res &= $f->validate($force);
        }
        // Проверка дочерних форм
        foreach ($this->getFieldsetList() as $fset_name) {
            $fset = $this->getFieldset($fset_name);
            $res &= $fset->validate($force);
        }
        return $res;
    }

    /**
     * Добавляет действие к форме
     *
     * @param string $name Имя действия (идентификатор)
     * @param string $label Название действия (надпись на кнопке)
     * @param string $method Имя метода класса формы выполняющий действие
     * @param string $class Имя класса элемента интерфейса формы
     * @param array $options Параметры элемента формы
     * @param bool $helper Признак вспомогательного действия(отображается в заголовке формы)
     * @return object subclass of main\forms\control\Action
     */
    public function addActionControl(
        $name,
        $label,
        $method = 'action_save',
        $class = 'main\forms\control\Submit',
        $options = [],
        $helper = false
    ) {
        // проверки
        $actionCtrl = new $class($this, $name, $label, $options);
        $a = ['control' => $actionCtrl, 'method' => $method, 'helper' => $helper];
        $this->actions[$name] = $a;
        return $actionCtrl;
    }

    public function setActionDefault($name)
    {
        $a = $this->getActionControl($name);
        $this->defaultAction = $a->htmlControlName;
    }

    /**
     * Добавляет вспомогательное действие к форме wrapper к addActionControl()
     * @param string $name
     * @param string $label
     * @param string $method
     * @param string $class
     * @param array $options
     * @return object
     */
    protected function addActionHelperControl($name, $label, $method, $class = 'main\forms\control\LinkSubmit', $options = [])
    {
        return $this->addActionControl($name, $label, $method, $class, $options, true);
    }

    /**
     * Возвращает элемент Действия формы
     *
     * @param string $name
     * @return object \main\forms\control\Action
     */
    protected function getActionControl($name)
    {
        return $this->getAction($name, 'control');
    }

    /**
     * Возвращает массив информации о Действии
     *
     * @param string $name
     * @param string $field
     * @return \main\forms\control\BaseAction|array
     */
    protected function getAction($name, $field = '')
    {
        if (array_key_exists($name, $this->actions)) {
            return '' == $field ? $this->actions[$name] : $this->actions[$name][$field];
        } else {
            throw new FormException('action "' . $name . '" was not found');
        }
    }

    /**
     * Возвращает список Действий
     *
     * @return array
     */
    protected function getActionList()
    {
        return array_keys($this->actions);
    }

    /**
     * Добавляет поле к форме
     *
     * @param string $class Класс поля
     * @param string $name Имя поля
     * @param string $label Название поля
     * @param array $options Параметры поля
     * @return object subclass of main\forms\control\Control
     */
    public function addField($class, $name, $label, $options = [])
    {
        $class = self::translatePsr0Class($class);
        $field = new $class($this, $name, $label, $options);
        $this->fields[$field->name] = $field;
        return $field;
    }

    /**
     * Добавляет поле к форме
     *
     * @param \main\forms\control\BaseControl $objField
     */
    public function addObjField($objField)
    {
        if (!($objField instanceof \main\forms\control\BaseControl)) {
            throw new FormException('objField expected to be a subclass of "main\forms\control\BaseControl"');
        }
        $this->fields[$objField->name] = $objField;
    }

    /**
     * Возвращает объект поля формы по имени
     * @param string $name Имя поля
     * @return \main\forms\control\BaseControl
     */
    public function getField($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        } else {
            throw new FormException('field "' . $name . '" was not found');
        }
    }

    /**
     * Удаляет поле формы по имени
     * @param string $name Имя поля
     */
    public function delField($name)
    {
        if (array_key_exists($name, $this->fields)) {
            unset($this->fields[$name]);
        } else {
            throw new FormException('field "' . $name . '" was not found');
        }
    }

    /**
     * Возвращает список полей формы
     *
     * @return array
     */
    public function getFieldList()
    {
        return array_keys($this->fields);
    }

    /**
     * Добавляет подформу к форме
     *
     * @param string $class класс подформы
     * @param string $name имя формы
     * @param string $title заголовок подформы
     * @param object $objDataSource объект источника данных
     * @param object $objAuth объект авторизации
     * @return Form
     */
    public function addFieldset($class, $name, $title, $objDataSource, $objAuth)
    {
        $class = self::translatePsr0Class($class, 'form_core_', '\\main\\forms\\core\\');
        /* @var $fset \main\forms\core\Form */
        $fset = new $class($this->createFieldName($name), $title, $objDataSource, $objAuth, $this->getRootForm());
        $fset->inheritParent($this);
        $this->fieldsets[$name] = $fset;
        return $fset;
    }

    public function addObjFieldset($name, $fs)
    {
        if (!($fs instanceof Form)) {
            throw new FormException('objFieldset expected to be a subclass of "form\core\Form"');
        }
        $this->fieldsets[$name] = $fs;
    }

    /**
     * Удаляет подформу
     *
     * @param string $name Имя формы
     */
    public function delFieldset($name)
    {
        if (array_key_exists($name, $this->fieldsets)) {
            unset($this->fieldsets[$name]);
        } else {
            throw new FormException('fieldset "' . $name . '" was not found');
        }
    }

    /**
     * Возвращает объект подформы по имени
     *
     * @param string $name Имя формы
     * @return \main\forms\core\Form
     */
    public function getFieldset($name)
    {
        if (array_key_exists($name, $this->fieldsets)) {
            return $this->fieldsets[$name];
        } else {
            throw new FormException('fieldset "' . $name . '" was not found');
        }
    }

    /**
     * Возвращает список подформ
     *
     * @return array
     */
    public function getFieldsetList()
    {
        return array_keys($this->fieldsets);
    }

    /**
     * Инициализация подформы данными родительской подформы
     *
     * @param object $parentFieldset
     */
    public function inheritParent($parentFieldset)
    {
        $this->nestingLevel = $parentFieldset->nestingLevel;
        if ('' != $parentFieldset->getTitle()) {
            $this->nestingLevel++;
        }
        $this->fieldPath = $parentFieldset->getFieldPath($this->getTitle());
    }

    /**
     * Возвращает массив данных о форме, использующийся для создания
     * html представления формы
     *
     * @return array
     */
    protected function asArray()
    {
        $data = [
            'title' => $this->getTitle(),
            'address' => $this->address,
            'layout_name' => $this->getLayoutName(),
            'layout_params' => $this->getLayoutParams(),
            'auth' => $this->getFormDispMode(),
            'level' => $this->nestingLevel,
            'fields' => [], // поля
            'hidden_fields' => [], // скрытые поля
            'fieldsets' => [],
            'actions' => [],
            'helper_actions' => [],
            'post_loaded' => $this->getPostLoaded()
        ];
        if ($this->isRootForm()) {
            $data['url'] = $this->getUrl();
            $data['formName'] = $this->getFormName();
        }

        foreach ($this->getFieldList() as $fn) {
            $d = $this->getField($fn)->asArray();
            if (!$d['hidden']) {
                $data['fields'][$fn] = $d;
            } else {
                $data['hidden_fields'][$fn] = $d;
            }
        }
        foreach ($this->getFieldsetList() as $fs_name) {
            $data['fieldsets'][$fs_name] = $this->getFieldset($fs_name)->asArray();
        }
        foreach ($this->getActionList() as $an) {
            $a = $this->getAction($an);
            if ($a['helper']) {
                $data['helper_actions'][$an] = $a['control']->asArray();
            } else {
                $data['actions'][$an] = $a['control']->asArray();
            }
        }
        return $data;
    }

    /**
     * Формирует html имя элемента формы
     *
     * @param string $field имя поля
     * @return string
     */
    public function createFieldName($field)
    {
        return ($this->prefix ? $this->prefix . self::$htmlNameDelimiter : '') . str_replace('.', ':', $field);
    }

    public function setFieldPath($fieldPath)
    {
        $this->fieldPath = $fieldPath;
        return $this;
    }

    /**
     * Возвращает полное имя поля с учетом иерархии
     *
     * @param string $fieldLabel название поля
     * @return string
     */
    public function getFieldPath($fieldLabel)
    {
        return $fieldLabel == '' ? $this->fieldPath : ($this->fieldPath ? $this->fieldPath . ' : ' : '') . $fieldLabel;
    }

    /**
     * Возвращает фактический режим отображения формы
     *
     * @return int
     */
    protected function getFormDispMode()
    {
        return $this->displayModeReal;
    }

    /**
     * Возвращает режим отображения поля формы на основе прав доступа
     * @param string $fieldName
     * @return int
     */
    protected function getFieldDispMode($fieldName)
    {
        return $this->objAuth->getRefineAuthField($this->getFormDispMode(), $fieldName);
    }

    /**
     * Возвращает режим отображения действия формы на основе прав доступа
     * @param string $actionName
     * @return int
     */
    protected function getActionDispMode($actionName)
    {
        return $this->objAuth->getRefineAuthAction($this->getFormDispMode(), $actionName);
    }

    /**
     * Загружает значения полей формы
     * @param bool $post признак источника загрузки (true - POST, false - источник данных)
     * @param bool $forceDS
     */
    protected function loadValues($post = false, $forceDS = false)
    {
        $this->setPostLoaded($post);
        foreach ($this->getFieldList() as $fn) {
            $this->getField($fn)->load($post, $forceDS);
        }
        foreach ($this->getFieldsetList() as $fs_name) {
            $this->getFieldset($fs_name)->loadValues($post, $forceDS);
        }
        $this->onAfterLoad();
    }

    /**
     * Сохраняет значения полей в источник данных
     * @param bool $force
     */
    protected function saveValues($force = false)
    {
        foreach ($this->getFieldList() as $fn) {
            $this->getField($fn)->save($force);
        }
        foreach ($this->getFieldsetList() as $fs_name) {
            $this->getFieldset($fs_name)->saveValues($force);
        }
    }

    /**
     * Ищет класс-хозяин для действия и передает его ему на выполнение
     *
     * @param string $actionName имя действия на текущем уровне иерархии
     * @param string $fullActionName полное имя действия
     */
    protected function searchAction($actionName, $fullActionName)
    {
        $d = explode(self::$htmlNameDelimiter, $actionName);
        if (1 == count($d)) {
            $d = ['dummy', $actionName];
        }
        $actionOrFieldsetName = $d[1];
        if (count($d) == 2) { //formname:field
            if (false !== array_search($actionOrFieldsetName, $this->getActionList())) {
                $this->fireAction($this->getAction($actionOrFieldsetName), $fullActionName);
            } else {
                throw new FormException('failed to find action(' . $actionName . ')');
            }
        } elseif (count($d) > 2) { //formname:subformname:field
            unset($d[0]); // убираем префикс fieldset'a
            $subActionName = implode(self::$htmlNameDelimiter, $d);
            if (false !== array_search($actionOrFieldsetName, $this->getFieldsetList())) {
                $fs = $this->getFieldset($actionOrFieldsetName);
                $fs->searchAction($subActionName, $fullActionName);
            } else {
                throw new FormException('failed to find fieldset for action(' . $actionName . ')');
            }
        } else {
            throw new FormException('invalid actionName(' . $actionName . ')');
        }
    }

    /**
     * Запускает действие формы.
     * (Пересылает действие в форму верхнего уровня)
     * @param array $actionData
     * @param string $fullActionName
     * @param null|string $param
     * @return mixed
     */
    protected function fireAction($actionData, $fullActionName, $param = null)
    {
        if ($actionData['control']->getRenderMode() != Form::MODE_WRITE) {
            throw new FormException('Access denied for execution action "' . $fullActionName . '"');
        }
        $method = $actionData['method'];
        if (method_exists($this, $method)) {
            return $this->$method($param);
        } else {
            throw new FormException('Method(' . $method . ') fo actionName(' . $fullActionName . ') does not exist');
        }
    }

    protected function onAfterLoad()
    {

    }

    protected function onAfterFireActions($actionHappened)
    {
        foreach ($this->getFieldsetList() as $fs_name) {
            $this->getFieldset($fs_name)->onAfterFireActions($actionHappened);
        }
    }

    /**
     * Возвращает html представление формы.
     * Представление формируется посредством объекта renderer'а,
     * прикрепленного к форме
     *
     * @return string
     */
    public function render()
    {
        if (!$this->isRootForm()) {
            throw new FormException(
                'Only root form can call "render"'
            );
        }
        if (is_null($this->objRenderer)) {
            throw new FormException(
                'objRenderer hasn\'t set'
            );
        }
        $this->getField($this->actionControlName)->value = null;
        return $this->objRenderer->render($this->asArray());
    }

    /**
     * Возвращает флаг отправки формы
     *
     * @param bool $GET флаг поиска в GET, а не POST
     * @return bool
     */
    public function isPosted($GET = false)
    {
        //$this->logger->debug('isPosted('.($GET?'get':'post').')');
        if (!$this->isRootForm()) {
            throw new FormException(
                'Only root form can call "isPosted"'
            );
        }
        return $this->getField($this->actionControlName)->loadPost($GET);
    }

    /**
     * Загружает поля формы и подформ значениями
     * @param bool $forceDS
     */
    public function load($forceDS = false)
    {
        if (!$this->isRootForm()) {
            throw new FormException(
                'Only root form can call "load"'
            );
        }
        $this->loadValues($this->isPosted(), $forceDS);
    }

    /**
     * Сохраняет поля формы в источнике данных
     * @param bool $force
     */
    public function save($force = false)
    {
        if (!$this->isRootForm()) {
            throw new FormException(
                'Only root form can call "save"'
            );
        }
        $this->getDataSource()->beforeSave();
        $this->saveValues($force);
        $this->getDataSource()->afterSave();
    }

    /**
     * Обработчик формы. Основной метод класса.
     * Загружает данные, выполняет действия формы, возвращает html
     * представление формы
     *
     * @return string
     */
    public function handle()
    {
        $this->process();
        return $this->render();
    }

    public function process()
    {
        if (!$this->isRootForm()) {
            throw new FormException(
                'Only root form can call "handle"'
            );
        }
        $this->applyAuth();
        $this->load();
        $actionHappened = false;
        if ($this->isPosted()) {
            $actionName = $this->getField($this->actionControlName)->value;
            if ('' == $actionName) {
                $actionName = $this->defaultAction;
            }
            $this->searchAction($actionName, $actionName);
            $actionHappened = true;
        } elseif ($this->isPosted(true)) { // GET action
            $actionName = $this->getField($this->actionControlName)->value;
            $this->searchAction($actionName, $actionName);
            $actionHappened = true;
        }
        $this->onAfterFireActions($actionHappened);
        return $actionHappened;
    }

    /**
     * Учет прав доступа к режиму отображения формы
     *
     */
    protected function applyAuth()
    {
        $this->displayModeReal = $this->objAuth->getRefineAuthForm($this->getDisplayMode());
        foreach ($this->getFieldList() as $fn) {
            $this->getField($fn)->applyAuth($this->getFieldDispMode($fn));
        }
        foreach ($this->getActionList() as $an) {
            $this->getActionControl($an)->applyAuth($this->getActionDispMode($an));
        }
        foreach ($this->getFieldsetList() as $fs_name) {
            $this->getFieldset($fs_name)->applyAuth();
        }
    }

    /**
     * Возвращает ассоциативный массив, содержащий историю значений
     * полей формы и ее подформ. Формат строки:
     * TimestampHtmlcontrolnameCounter => array(
     * form - перефикс формы,
     * field - html название поля
     * label - название поля с учетом вложенных форм
     * value_old - старое значение поля
     * value - новое значение поля
     * op - операция (I-вставка,U-обновление,D-удаление)
     * mdate - дата изменения в формате DD-MM-YYYY HH:mm:SS
     * muserid - id пользователя, изменившего поле
     * musername - имя пользователя, изменившего поле
     * )
     *
     * @return array
     */
    public function getHistory()
    {
        $h = [];
        // свои поля
        foreach ($this->getFieldList() as $fn) {
            $h = $h + $this->getField($fn)->getHistory();
        }
        // подчиненные формы
        foreach ($this->getFieldsetList() as $fs_name) {
            $h = $h + $this->getFieldset($fs_name)->getHistory();
        }
        krsort($h);
        return $h;
    }

    public function lookupField($storeFieldName, $value, $valueOld)
    {
        foreach ($this->getFieldList() as $fn) {
            $f = $this->getField($fn);
            if ($storeFieldName == $this->getDataSource()->getFieldStorePath($f->name)) {
                return $f->getHistoryValue($value, $valueOld);
            }
        }
        // подчиненные формы
        foreach ($this->getFieldsetList() as $fs_name) {
            $result = $this->getFieldset($fs_name)->lookupField($storeFieldName, $value, $valueOld);
            if ($result) {
                return $result;
            }
        }
        return false;
    }

    /**
     * Выполняет http redirect на указанный адрес/адрес формы
     * @param string $url
     * @throws \yii\base\ExitException
     */
    public function resetForm($url = null)
    {
        \Yii::$app->response->redirect($url ? $url : $this->getUrl())->send();
        \Yii::$app->end();
    }

    /**
     * Добавляет параметр в переданный url
     *
     * @param string $url исходный url
     * @param string $param имя параметра
     * @param string $value значение параметра
     * @return string измененный url
     */
    public function modifyUrl($url, $param, $value)
    {
        $u = parse_url($url);
        $paramList = [];
        if (isset($u['query'])) {
            parse_str($u['query'], $paramList);
        }
        $paramList[$param] = $value;
        $newurl = (isset($u['path']) ? $u['path'] : '') .
            (count($paramList) > 0 ? '?' . http_build_query($paramList) : '') .
            (isset($u['fragment']) ? '#' . $u['fragment'] : '');
        return $newurl;
    }

}
