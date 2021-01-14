<?php

namespace main\forms\factory;

/**
 * Фабрика классов форм
 *
 * @package form
 */
class Form
{

    /**
     * Возвращает экземпляр формы по id
     *
     * @param integer $formId
     * @param string $url URL страницы
     * @param object $obj Объект БД связанный с формой (subclass of object)
     * @return main\form\core\Form
     */
    public static function load($formId, $url, $obj)
    {
        $className = is_numeric($formId) ? 'form_db_f' . $formId : 'form_' . $formId;
        $o = new $className($obj, $url);
        return $o;
    }

}
