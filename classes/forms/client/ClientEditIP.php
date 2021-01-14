<?php

namespace main\forms\client;

use \main\forms\core\DispMode as form_dispMode;

class ClientEditIP extends ClientEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'client_ClientEditIP', 'client/ClientEditIP.phtml', 'IP');

        $this->addField('form_control_TextFilter', 'name', 'ФИО');
        $this->addField('form_control_TextFilter', 'last_name', 'Фамилия', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'first_name', 'Имя', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'middle_name', 'Отчество', ['lengthMax' => 200, 'trim' => true]);
        $this->addField('form_control_TextareaFilter', 'address_legal', 'Юридический адрес', ['xsize' => '60', 'ysize' => '3', 'required' => '1']);
        $this->addField('form_control_TextareaFilter', 'address', 'Почтовый адрес', ['xsize' => '60', 'ysize' => '3', 'required' => '1']);
        $this->addField('form_control_Text', 'ogrn', 'ОГРН ИП', ['lengthMax' => 15, 'lengthMin' => 15, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'inn', 'ИНН', ['lengthMax' => 12, 'lengthMin' => 12, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'snils', 'СНИЛС', ['lengthMax' => 12, 'lengthMin' => 12, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'phone', 'Телефон', ['trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'email', 'Электронная почта', ['trim' => true, 'required' => '1']);
    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();
        $this->getField('name')->setRenderMode(form_dispMode::Read);
    }

    public function save($force = false)
    {
        // Вычисляем полное ФИО
        $this->getField('name')->value = trim($this->getField('last_name')->value . ' ' . $this->getField('first_name')->value . ' ' . $this->getField('middle_name')->value);
        parent::save($force);
    }

}
