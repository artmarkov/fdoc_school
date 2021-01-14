<?php

class pagetab_DossierDeleteWizard extends pagetab_AbstractDeleteWizard
{

    /**
     * @param \fdoc\eav\object\Dossier $obj
     * @param string $url
     */
    public function __construct($obj, $url, $user)
    {
        parent::__construct($obj, $url);

        $this->addDependency(
            'resolution',
            'Связанные решения',
            manager_ResolutionByDossier::create(['dossier/resolution', 'id' => $this->object->id], $user)->setDossierId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::resolution($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('dossier_id', '0');
                }
            },
            'delete'
        );
        $this->addDependency(
            'order',
            'Связанные заявления',
            manager_OrderByDossier::create(['dossier/order', 'id' => $this->object->id], $user)->setDossierId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::order($id);
                $o->setval('dossier_id', '0');
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'document',
            'Связанные документы',
            manager_DocumentByDossier::create(['dossier/document', 'id' => $this->object->id], $user)->setDossierId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::document($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('dossier_id', '0');
                }
            },
            'delete'
        );
        $this->addDependency(
            'inspection',
            'Связанные проверки',
            manager_InspectionByDossier::create(['dossier/inspection', 'id' => $this->object->id], $user)->setDossierId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::inspection($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('dossier_id', '0');
                }
            },
            'delete'
        );
        $this->addDependency(
            'courtverdict',
            'Связанные решения суда',
            manager_CourtverdictByDossier::create(['dossier/courtverdict', 'id' => $this->object->id], $user)->setDossierId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::courtverdict($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('dossier_id', '0');
                }
            },
            'delete',
            ['delete']
        );
        $this->addDependency(
            'penalty',
            'Связанные административные наказания',
            manager_PenaltyByDossier::create(['dossier/penalty', 'id' => $this->object->id], $user)->setDossierId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::penalty($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('dossier_id', '0');
                }
            },
            'delete',
            ['delete']
        );
    }

    protected function getInfo()
    {
        $clientId = $this->object->getval('client_id');
        return [
            'id' => $this->object->id,
            'Контрагент' => $clientId ? ObjectFactory::client($clientId)->getname() : '- не указан -',
            'Вид лицензируемой деятельности' => RefBook::find('license-type')->getValue($this->object->getval('activity_type')),
            'Номер лицензии' => $this->object->getval('lic_number'),
            'Дата регистрации лицензии (дубликата)' => $this->object->getval('lic_date'),
            'Статус лицензии' => $this->object->getval('status') == '1' ? 'Действующая' : 'Недействующая',
            'Номер бланка лицензии' => $this->object->getval('lic_blank_number'),
            'Номер бланка приложения' => $this->object->getval('appendix_blank_number'),
        ];
    }

}