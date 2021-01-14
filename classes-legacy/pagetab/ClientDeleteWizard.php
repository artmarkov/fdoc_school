<?php

class pagetab_ClientDeleteWizard extends pagetab_AbstractDeleteWizard
{

    /**
     * @param \fdoc\eav\object\Client $obj
     * @param string $url
     */
    public function __construct($obj, $url, $user)
    {
        parent::__construct($obj, $url);

        $this->addDependency(
            'dossier',
            'Связанные лицензии',
            manager_DossierByClient::create(['client/dossier', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::dossier($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'resolution',
            'Связанные решения',
            manager_ResolutionByClient::create(['client/resolution', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::resolution($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'delete'
        );
        $this->addDependency(
            'order',
            'Связанные заявления',
            manager_OrderByClient::create(['client/order', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::order($id);
                $o->setval('client_id', \fdoc\ordertype\Factory::get($o)->getDefaultClientId());
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'document',
            'Связанные документы',
            manager_DocumentByClient::create(['client/document', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::document($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'delete'
        );
        $this->addDependency(
            'inspection',
            'Связанные проверки',
            manager_InspectionByClient::create(['client/inspection', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::inspection($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'delete'
        );
        $this->addDependency(
            'courtverdict',
            'Связанные решения суда',
            manager_CourtverdictByClient::create(['client/courtverdict', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::courtverdict($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'delete',
            ['delete']
        );
        $this->addDependency(
            'penalty',
            'Связанные административные наказания',
            manager_PenaltyByClient::create(['client/penalty', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::penalty($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'delete',
            ['delete']
        );
        $this->addDependency(
            'gmp',
            'Связанные заключения gmp',
            manager_GmpByClient::create(['client/conclusions', 'id' => $this->object->id, 'type' => 'gmp'], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::gmp($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'medlocal',
            'Связанные заключения "Локализация СП"',
            manager_MedlocalByClient::create(['client/conclusions', 'id' => $this->object->id, 'type' => 'medlocal'], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::medlocal($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'medicament',
            'Связанные заключения "Документы CPP"',
            manager_MedicamentByClient::create(['client/conclusions', 'id' => $this->object->id, 'type' => 'medicament'], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::medicament($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'taxfree',
            'Связанные заявления "Tax Free"',
            manager_TaxfreeByClient::create(['client/taxfree', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::taxfree($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'gosincome',
            'Связанные сведения "Доходы от ГУ"',
            manager_GosincomeByClient::create(['client/gosincome', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::gosincome($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'delete',
            ['delete']
        );
        $this->addDependency(
            'vehiclemaker',
            'Связанные сведения по изготовителям ТС',
            manager_VehicleMakerByClient::create(['client/vehiclemaker', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::vehiclemaker($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('client_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'craft_owner',
            'Связанные сведения по собственникам ЭВС',
            manager_ExpaircraftByOwner::create(['client/expaircraft', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::expaircraft($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('owner_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            'craft_operator',
            'Связанные сведения по эксплуатантам ЭВС',
            manager_ExpaircraftByOperator::create(['client/expaircraft', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::expaircraft($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('operator_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '3766_applicant',
            'Связи ПЦН Мед. приказ №3766 по заявителям',
            manager_Letters3766ByApplicant::create(['client/letters3766', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters3766($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '3766_recipient',
            'Связи ПЦН Мед. приказ №3766 по получателям',
            manager_Letters3766ByRecipient::create(['client/letters3766', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters3766($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '2341_applicant',
            'Связи ПЦН Мед. приказ №2341 по заявителям',
            manager_Letters2341ByApplicant::create(['client/letters2341', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters2341($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '2341_recipient',
            'Связи ПЦН Мед. приказ №2341 по получателям',
            manager_Letters2341ByRecipient::create(['client/letters2341', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters2341($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '4008_applicant',
            'Связи ПЦН Мед. приказ №4008 по заявителям',
            manager_Letters4008ByApplicant::create(['client/letters4008', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters4008($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '4008_recipient',
            'Связи ПЦН Мед. приказ №4008 по получателям',
            manager_Letters4008ByRecipient::create(['client/letters4008', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters4008($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '462_applicant',
            'Связи ПЦН Мед. приказ №462 по заявителям',
            manager_Letters462ByApplicant::create(['client/letters462', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters462($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '462_recipient',
            'Связи ПЦН Мед. приказ №462 по получателям',
            manager_Letters462ByRecipient::create(['client/letters462', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters462($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '715avia_applicant',
            'Связи ПЦН АВИА приказ №715 по заявителям',
            manager_Letters715aviaByApplicant::create(['client/letters715avia', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters715avia($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '715avia_recipient',
            'Связи ПЦН АВИА приказ №715 по получателям',
            manager_Letters715aviaByRecipient::create(['client/letters715avia', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters715avia($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '1319mash_applicant',
            'Связи ПЦН ИНВ.МАШ. приказ №1319 по заявителям',
            manager_Letters1319mashByApplicant::create(['client/letters1319mash', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters1319mash($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '1319mash_recipient',
            'Связи ПЦН ИНВ.МАШ. приказ №1319 по получателям',
            manager_Letters1319mashByRecipient::create(['client/letters1319mash', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters1319mash($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '4707lp_applicant',
            'Связи ПЦН ЛЕГ.ПРОМ приказ №4707 по заявителям',
            manager_Letters4707lpByApplicant::create(['client/letters4707lp', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters4707lp($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '4707lp_recipient',
            'Связи ПЦН ЛЕГ.ПРОМ приказ №4707 по получателям',
            manager_Letters4707lpByRecipient::create(['client/letters4707lp', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters4707lp($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '138lp_applicant',
            'Связи ПЦН ЛЕГ.ПРОМ приказ №138 по заявителям',
            manager_Letters138lpByApplicant::create(['client/letters138lp', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters138lp($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '138lp_recipient',
            'Связи ПЦН ЛЕГ.ПРОМ приказ №138 по получателям',
            manager_Letters138lpByRecipient::create(['client/letters138lp', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters138lp($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '476lp_applicant',
            'Связи ПЦН ЛЕГ.ПРОМ приказ №476 по заявителям',
            manager_Letters476lpByApplicant::create(['client/letters476lp', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters476lp($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '476lp_recipient',
            'Связи ПЦН ЛЕГ.ПРОМ приказ №476 по получателям',
            manager_Letters476lpByRecipient::create(['client/letters476lp', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters476lp($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '328radio_applicant',
            'Связи ПЦН РАДИО приказ №328 по заявителям',
            manager_Letters328radioByApplicant::create(['client/letters328radio', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters328radio($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '328radio_recipient',
            'Связи ПЦН РАДИО приказ №328 по получателям',
            manager_Letters328radioByRecipient::create(['client/letters328radio', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters328radio($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '2742met_applicant',
            'Связи ПЦН МЕТАЛ приказ №2742 по заявителям',
            manager_Letters2742metByApplicant::create(['client/letters2742met', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters2742met($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '2742met_recipient',
            'Связи ПЦН МЕТАЛ приказ №2742 по получателям',
            manager_Letters2742metByRecipient::create(['client/letters2742met', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters2742met($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '2845met_applicant',
            'Связи ПЦН МЕТАЛ приказ №2845 по заявителям',
            manager_Letters2845metByApplicant::create(['client/letters2845met', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters2845met($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '2845met_recipient',
            'Связи ПЦН МЕТАЛ приказ №2845 по получателям',
            manager_Letters2845metByRecipient::create(['client/letters2845met', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters2845met($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '1918mash_applicant',
            'Связи ПЦН ИНВ.МАШ. приказ №1918 по заявителям',
            manager_Letters1918mashByApplicant::create(['client/letters1918mash', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters1918mash($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('applicant_id', '0');
                }
            },
            'detach',
            ['detach']
        );
        $this->addDependency(
            '1918mash_recipient',
            'Связи ПЦН ИНВ.МАШ. приказ №1918 по получателям',
            manager_Letters1918mashByRecipient::create(['client/letters1918mash', 'id' => $this->object->id], $user)->setClientId($this->object->id),
            function ($id, $action) {
                $o = ObjectFactory::letters1918mash($id);
                if ('delete' == $action) {
                    $o->delete();
                } else {
                    $o->setval('recipient_id', '0');
                }
            },
            'detach',
            ['detach']
        );
    }

    protected function getInfo()
    {
        return [
            'id' => $this->object->id,
            'Тип' => $this->object->getTypeName(),
            'Полное наименование' => $this->object->getval('name'),
            'Сокращенное наименование' => $this->object->getval('briefname'),
            'Адрес места нахождения' => $this->object->getval('address'),
            'ОГРН' => $this->object->getval('ogrn'),
            'ИНН' => $this->object->getval('inn')
        ];
    }

}