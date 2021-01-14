<?php

namespace main\forms\client;

use main\eav\object\Revocamp;
use \main\forms\core\Dynamic as form_core_Dynamic;
use main\forms\auth\Acl as form_auth_Acl;

class ClientEditUL extends ClientEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'client_ClientEditUL', 'client/ClientEditUL.phtml', 'UL');

        $this->addField('form_control_TextFilter', 'name', 'Полное наименование', ['lengthMax' => 2000, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'briefname', 'Сокращенное наименование', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'firmname', 'Фирменное наименование', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $this->addField('form_control_TextareaFilter', 'address_legal', 'Юридический адрес', ['xsize' => '60', 'ysize' => '3', 'required' => '1']);
        $this->addField('form_control_TextareaFilter', 'address', 'Почтовый адрес', ['xsize' => '60', 'ysize' => '3', 'required' => '1']);
        $this->addField('form_control_Text', 'ogrn', 'ОГРН', ['lengthMax' => 13, 'lengthMin' => 13, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'inn', 'ИНН', ['lengthMax' => 10, 'lengthMin' => 10, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'phone', 'Телефон', ['trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'email', 'Электронная почти', ['trim' => true, 'required' => '1']);


        $this->addField('form_control_TextFilter', 'r_name', '', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'r_thirdname', '', ['lengthMax' => 200, 'trim' => true]);
        $this->addField('form_control_TextFilter', 'r_surname', '', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'l_name', '', ['lengthMax' => 200, 'trim' => true]);
        $this->addField('form_control_TextFilter', 'l_surname', '', ['lengthMax' => 200, 'trim' => true]);
        $this->addField('form_control_TextFilter', 'l_thirdname', '', ['lengthMax' => 200, 'trim' => true]);
        $this->addField('form_control_TextFilter', 'l_snils', '', ['lengthMax' => 200, 'trim' => true]);

        $this->addField('form_control_Select3', 'revocamp_type', 'Тип юридического лица', ['list' => []]);
    }

}