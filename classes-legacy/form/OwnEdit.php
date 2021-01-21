<?php

use main\eav\object\Own;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\DispMode as form_dispMode;
use main\forms\core\Form;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;

class form_OwnEdit extends \main\forms\ObjEdit
{
    protected $employeesType;
    protected $timestamp;

    /**
     * OwnEdit constructor.
     * @param $obj \main\eav\object\Base
     * @param $url string
     * @param $aclName string
     * @param $tmplName string
     * @throws \main\forms\core\FormException
     */
    public function __construct($obj, $url)
    {
        $objDS = new form_datasource_Object('', $obj);
        $objAuth = new form_auth_Acl('form_OwnEdit');
        parent::__construct('', 'Основные сведения', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight('OwnEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_TextFilter', 'name', 'Наименование учреждения', ['lengthMax' => 1000, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'shortname', 'Сокращенное наименование учреждения', ['lengthMax' => 500, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'address', 'Почтовый адрес учреждения', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'email', 'E-mail учреждения', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'head', 'Руководитель учреждения', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'chief_accountant', 'Главный бухгалтер', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);

        $fInvoices = $this->addFieldset('form_core_Dynamic', 'invoices', 'Банковские реквизиты', $this->getDataSource()->inherit('invoices'), new form_auth_Acl('public'));
        $fInvoices->addField('form_control_TextFilter', 'name', 'Назначение', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $fInvoices->addField('form_control_TextFilter', 'recipient', 'Наименование получателя', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $fInvoices->addField('form_control_TextFilter', 'inn', 'ИНН', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $fInvoices->addField('form_control_TextFilter', 'kpp', 'КПП', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $fInvoices->addField('form_control_TextFilter', 'payment_account', 'Расчетный счет', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $fInvoices->addField('form_control_TextFilter', 'corr_account', 'Кореспондентский счет', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $fInvoices->addField('form_control_TextFilter', 'personal_account', 'Лицевой счет', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $fInvoices->addField('form_control_TextFilter', 'bank_name', 'Наименование банка получателя', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $fInvoices->addField('form_control_TextFilter', 'bik', 'БИК', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $fInvoices->addField('form_control_TextFilter', 'oktmo', 'ОКТМО', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $fInvoices->addField('form_control_TextFilter', 'kbk', 'КБК', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);

    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();

            $this->getActionControl('exit')->setRenderMode(Form::MODE_NONE);
            $this->getActionControl('saveexit')->setRenderMode(Form::MODE_NONE);
    }
}
