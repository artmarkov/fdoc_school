<?php

namespace main\forms\client;

use \main\forms\core\DispMode as form_dispMode;

class ClientEditFL extends ClientEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'client_ClientEditFL', 'client/ClientEditFL.phtml', 'FL');

        $this->addField('form_control_TextFilter', 'name', 'ФИО');
        $this->addField('form_control_TextFilter', 'surname', 'Фамилия', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'firstname', 'Имя', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'thirdname', 'Отчество', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'snils', 'СНИЛС', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $this->addField('form_control_TextareaFilter', 'address', 'Почтовый адрес', ['xsize' => '60', 'ysize' => '3', 'required' => '1']);
        $this->addField('form_control_Text', 'phone', 'Телефон', ['trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'email', 'Электронная почта', ['trim' => true, 'required' => '1']);
    }

   protected function onAfterLoad() {
      parent::onAfterLoad();
      $this->getField('name')->setRenderMode(form_dispMode::Read);
   }

   public function save($force=false) {
      // Вычисляем полное ФИО
      $this->getField('name')->value=trim($this->getField('surname')->value.' '.$this->getField('firstname')->value.' '.$this->getField('thirdname')->value);
      parent::save($force);
   }
}
