<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Client extends Base
{
    const TYPE_LIST = [
        'UL' => 'Юридическое лицо',
        'IP' => 'Индивидуальный предприниматель',
        'FL' => 'Физическое лицо'
    ];
    protected $formList = [
        'UL' => '\main\forms\client\ClientEditUL',
        'IP' => '\main\forms\client\ClientEditIP',
        'FL' => '\main\forms\client\ClientEditFL'
    ];
    protected $typeList = [
        'UL' => 'Юридическое лицо',
        'IP' => 'Индивидуальный предприниматель',
        'FL' => 'Физическое лицо'
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['name', 'name' => 'Наименование'],
            ['briefname', 'name' => ['Сокращенное наименование', 'Сокр. наименование']],
            ['firmname', 'name' => 'Фирменное наименование'],
            ['type', 'name' => 'Тип контрагента'],
            ['address', 'name' => 'Адрес места нахождения'],
            ['post_address', 'name' => ['Почтовый адрес', null]],
            ['email', 'name' => 'Email'],
            ['phone', 'name' => 'Телефон'],
            ['inn', 'name' => 'ИНН'],
            ['ogrn', 'name' => 'ОГРН'],
            ['kpp', 'name' => ['КПП', null]],
        ]);
    }

    /**
     * @throws \yii\db\Exception
     */
    function onCreate()
    {
        $this->setval('status', 0);
    }

    public function getFormId($type = null)
    {
        if (!$type) {
            $type = $this->getType();
        }
        return array_key_exists($type, $this->formList) ? $this->formList[$type] : $this->formList['UL'];
    }

    public function getType()
    {
        return $this->getval('type', 'UL');
    }

    public function getTypeName($type = '')
    {
        return $this->typeList[!$type ? $this->getType() : $type];
    }

    public function getAddress()
    {
        return $this->getval('address');
    }

    public function getBriefname()
    {
        return $this->getval('briefname');
    }

    public function getInn()
    {
        return $this->getval('inn');
    }

    public function getOgrn()
    {
        return $this->getval('ogrn');
    }

    /**
     * Обновляет ссылку на документ
     * @param Document $doc
     * @throws \yii\db\Exception
     */
    public function updateDocumentLink($doc)
    {
        $this->setval('documents.' . $doc->id, $doc->getVersion());
    }

    /**
     * Удаляет ссылку на документ
     * @param Document $doc
     * @throws \yii\db\Exception
     */
    public function removeDocumentLink($doc)
    {
        $this->delval('documents.' . $doc->id);
    }
}
