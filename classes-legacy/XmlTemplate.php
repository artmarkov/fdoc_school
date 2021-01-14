<?php

class XmlTemplate
{
    const PATH = '@app/views/xml/';
    protected $tmplName;

    private function __construct($tmplName)
    {
        $this->tmplName = $tmplName;
    }

    public static function get($tmplName)
    {
        return new self($tmplName);
    }

    /**
     * Возвращает готовый xml
     * @param array $data параметры для шаблона
     * @return string xml строка
     * @throws XmlTemplateException
     */
    public function render($data)
    {
        try {
            $xmlContent = Yii::$app->view->renderFile(self::PATH . $this->tmplName, $this->encode($data));
            return util_XmlTools::formatXml($xmlContent);
        } catch (Exception $ex) {
            Yii::error('Ошибка формирования xml [' . $this->tmplName . ']: ' . $ex->getMessage());
            Yii::error($ex);
            throw new XmlTemplateException('Ошибка формирования xml [' . $this->tmplName . ']: ' . $ex->getMessage());
        }
    }

    private static function encode($param)
    {
        if (is_array($param)) {
            $helper = [];
            foreach ($param as $k => $v) {
                $helper[$k] = is_array($v) || is_object($v) ? self::encode($v) : htmlspecialchars($v);
            }
            return $helper;
        } elseif (is_object($param)) {
            $helper = new stdClass();
            foreach (get_object_vars($param) as $k => $v) {
                $helper->{$k} = is_array($v) || is_object($v) ? self::encode($v) : htmlspecialchars($v);
            }
            return $helper;
        } else {
            return htmlspecialchars($param);
        }
    }

}

class XmlTemplateException extends RuntimeException
{
}
