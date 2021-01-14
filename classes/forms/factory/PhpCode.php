<?php

namespace main\forms\factory;

/**
 * Генератор исходников форм из БД
 */
class PhpCode
{

    /**
     * Название класса представления, используемое при создании формы
     *
     * @var unknown_type
     */
    static $classRenderer = 'main\forms\core\Renderer';

    /**
     * Название класса источника данных, используемое при создании формы
     *
     * @var unknown_type
     */
    static $classDS = 'main\forms\datasource\DbObject';

    /**
     * Название класса авторизатора, используемое при создании формы
     *
     * @var unknown_type
     */
    static $classAuth = 'main\forms\auth\Acl';

    /**
     * Название базового класса формы
     * от которого должны наследоваться все остальные классы форм
     *
     * @var string
     */
    private static $baseFormClass = 'main\forms\core\Form';

    public static function create($formClass)
    {
        $c = new static();
        return $c->createNamedForm($formClass);
    }

    /**
     * Создает шаблон класса формы по строковому id
     *
     * @param string $formClass
     * @return string Class source
     */
    protected function createNamedForm($formClass)
    {
        $o = new sourcegen_Class('form_' . $formClass, self::$baseFormClass);

        $o->addDependence(self::$classDS);
        $o->addDependence(self::$classRenderer);
        $o->addDependence(self::$classAuth);
        $o->addDependence(self::$baseFormClass);

        $c = $o->addMethod('__construct', 'public');
        $c->addParam('$obj');
        $c->addParam('$url');

        $c->addBody('$objDS=new ' . self::$classDS . '(\'\',$obj);');
        $c->addBody('$objAuth=new ' . self::$classAuth . '(\'' . $formClass . '\');');

        $c->addBody('parent::__construct(\'form\',\'Заголовок\',$objDS,$objAuth);');
        $c->addBody('$this->setRenderer(new ' . self::$classRenderer . '(\'' . $formClass . '\'));');
        $c->addBody('$this->setUrl($url);');
        $c->addBody('');

        $c->addBody('$this->addField(\'main\forms\control\Text\',\'param\',\'Атрибут\',array(\'trim\'=>true,\'required\'=>\'1\'));');
        return $o->render();
    }

}
