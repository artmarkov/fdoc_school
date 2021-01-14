<?php

class pagetab_ClientMergeWizard extends pagetab_AbstractMergeWizard
{
    protected $fields = [
        [
            'fieldset' => 'head',
            'clone' => false,
            'label' => 'Руководитель',
            'fields' => [
                'head.position' => 'Наименование должности',
                'head.last_name' => 'Фамилия',
                'head.first_name' => 'Имя',
                'head.middle_name' => 'Отчество',
                'head.phone' => 'Контактный телефон',
                'head.inn' => 'ИНН'
            ],
        ],
        [
            'fieldset' => 'capital',
            'clone' => false,
            'label' => 'Сведения об уставном капитале',
            'fields' => [
                'capital.type' => 'Вид',
                'capital.sum' => 'Размер (в рублях)',
            ],
        ],
        [
            'fieldset' => 'okved',
            'clone' => false,
            'label' => 'Сведения о видах экономической деятельности по ОКВЕД',
            'fields' => [
                'okved.primary' => 'Коды основного вида деятельности',
                'okved.secondary' => 'Коды дополнительных видов деятельности',
            ],
        ],
        [
            'fieldset' => 'address_lookup',
            'clone' => false,
            'label' => 'Характеристики адреса',
            'fields' => [
                'address_lookup.lat' => 'Широта',
                'address_lookup.lon' => 'Долгота',
                'address_lookup.name' => 'Адрес',
                'address_lookup.type' => 'Тип адреса'
            ],
        ],
        [
            'fieldset' => 'egrul_license',
            'clone' => true, // Пример поля: egrul_license.1.activity
            'label' => 'Сведения о лицензиях (из ЕГРЮЛ)',
            'fields' => [
                'num' => 'Номер',
                'date' => 'Дата',
                'date_start' => 'Дата начала действия',
                'activity' => 'Виды деятельности',
            ],
        ],
    ];

    /**
     * pagetab_ClientMergeWizard constructor.
     * @param $id
     * @param $obj
     * @param $url
     * @param $user
     */
    public function __construct($id, $obj, $url, $user)
    {
        parent::__construct($id, $obj, $url, $user);

        $this->getValue([
            [
                'country',
                function ($v) {
                    return RefBook::find('oksm-alpha')->getValue($v) ?? $v;
                }
            ],
            [
                'type',
                function ($v) {
                    return \fdoc\eav\object\Client::TYPE_LIST[$v] ?? $v;
                }
            ],
            [
                'legal_type',
                function ($v) {
                    return RefBook::find('okopf-code')->getValue($v) ?? $v;
                }
            ],
        ]);

        $this->addDependency(
            'dossier',
            'Связанные лицензии',
            function ($id, $user) {
                return manager_DossierByClient::create(['client/dossier', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::dossier($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'resolution',
            'Связанные решения',
            function ($id, $user) {
                return manager_ResolutionByClient::create(['client/resolution', 'id' => $id], $user)->setClientId($id);

            },
            function ($id, $client_id) {
                $o = ObjectFactory::resolution($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'order',
            'Связанные заявления',
            function ($id, $user) {
                return manager_OrderByClient::create(['client/order', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::order($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'document',
            'Связанные документы',
            function ($id, $user) {
                return manager_DocumentByClient::create(['client/document', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::document($id);
                $o->setval('client_id', $client_id);

            }
        );
        $this->addDependency(
            'inspection',
            'Связанные проверки',
            function ($id, $user) {
                return manager_InspectionByClient::create(['client/inspection', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::inspection($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'courtverdict',
            'Связанные решения суда',
            function ($id, $user) {
                return manager_CourtverdictByClient::create(['client/courtverdict', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::courtverdict($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'penalty',
            'Связанные административные наказания',
            function ($id, $user) {
                return manager_PenaltyByClient::create(['client/penalty', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::penalty($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'gmp',
            'Связанные заключения gmp',
            function ($id, $user) {
                return manager_GmpByClient::create(['client/conclusions', 'id' => $id, 'type' => 'gmp'], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::gmp($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'medlocal',
            'Связанные заключения "Локализация СП"',
            function ($id, $user) {
                return manager_MedlocalByClient::create(['client/conclusions', 'id' => $id, 'type' => 'medlocal'], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::medlocal($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'medicament',
            'Связанные заключения "Документы CPP"',
            function ($id, $user) {
                return manager_MedicamentByClient::create(['client/conclusions', 'id' => $id, 'type' => 'medicament'], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::medicament($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'taxfree',
            'Связанные заявления "Tax Free"',
            function ($id, $user) {
                return manager_TaxfreeByClient::create(['client/taxfree', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::taxfree($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'gosincome',
            'Связанные сведения "Доходы от ГУ"',
            function ($id, $user) {
                return manager_GosincomeByClient::create(['client/gosincome', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::gosincome($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'vehiclemaker',
            'Связанные сведения по изготовителям ТС',
            function ($id, $user) {
                return manager_VehicleMakerByClient::create(['client/vehiclemaker', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::vehiclemaker($id);
                $o->setval('client_id', $client_id);
            }
        );
        $this->addDependency(
            'craft_owner',
            'Связанные сведения по собственникам ЭВС',
            function ($id, $user) {
                return manager_ExpaircraftByOwner::create(['client/expaircraft', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::expaircraft($id);
                $o->setval('owner_id', $client_id);
            }
        );
        $this->addDependency(
            'craft_operator',
            'Связанные сведения по эксплуатантам ЭВС',
            function ($id, $user) {
                return manager_ExpaircraftByOperator::create(['client/expaircraft', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::expaircraft($id);
                $o->setval('operator_id', $client_id);
            }
        );
        $this->addDependency(
            '3766_applicant',
            'Связанные сведения ПЦН Мед. приказ №3766 по заявителям',
            function ($id, $user) {
                return manager_Letters3766ByApplicant::create(['client/letters3766', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters3766($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '3766_recipient',
            'Связанные сведения ПЦН Мед. приказ №3766 по получателям',
            function ($id, $user) {
                return manager_Letters3766ByRecipient::create(['client/letters3766', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters3766($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '2341_applicant',
            'Связанные сведения ПЦН Мед. приказ №2341 по заявителям',
            function ($id, $user) {
                return manager_Letters2341ByApplicant::create(['client/letters2341', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters2341($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '2341_recipient',
            'Связанные сведения ПЦН Мед. приказ №2341 по получателям',
            function ($id, $user) {
                return manager_Letters2341ByRecipient::create(['client/letters2341', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters2341($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '4008_applicant',
            'Связанные сведения ПЦН Мед. приказ №4008 по заявителям',
            function ($id, $user) {
                return manager_Letters4008ByApplicant::create(['client/letters4008', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters4008($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '4008_recipient',
            'Связанные сведения ПЦН Мед. приказ №4008 по получателям',
            function ($id, $user) {
                return manager_Letters4008ByRecipient::create(['client/letters4008', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters4008($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '462_applicant',
            'Связанные сведения ПЦН Мед. приказ №462 по заявителям',
            function ($id, $user) {
                return manager_Letters462ByApplicant::create(['client/letters462', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters462($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '462_recipient',
            'Связанные сведения ПЦН Мед. приказ №462 по получателям',
            function ($id, $user) {
                return manager_Letters462ByRecipient::create(['client/letters462', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters462($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '715avia_applicant',
            'Связанные сведения ПЦН АВИА приказ №715 по заявителям',
            function ($id, $user) {
                return manager_Letters715aviaByApplicant::create(['client/letters715avia', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters715avia($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '715avia_recipient',
            'Связанные сведения ПЦН АВИА приказ №715 по получателям',
            function ($id, $user) {
                return manager_Letters715aviaByRecipient::create(['client/letters715avia', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters715avia($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '1319mash_applicant',
            'Связанные сведения ПЦН ИНВ.МАШ. приказ №1319 по заявителям',
            function ($id, $user) {
                return manager_Letters1319mashByApplicant::create(['client/letters1319mash', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters1319mash($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '1319mash_recipient',
            'Связанные сведения ПЦН ИНВ.МАШ. приказ №1319 по получателям',
            function ($id, $user) {
                return manager_Letters1319mashByRecipient::create(['client/letters1319mash', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters1319mash($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '4707lp_applicant',
            'Связанные сведения ПЦН ЛЕГ.ПРОМ приказ №4707 по заявителям',
            function ($id, $user) {
                return manager_Letters4707lpByApplicant::create(['client/letters4707lp', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters4707lp($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '4707lp_recipient',
            'Связанные сведения ПЦН ЛЕГ.ПРОМ приказ №4707 по получателям',
            function ($id, $user) {
                return manager_Letters4707lpByRecipient::create(['client/letters4707lp', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters4707lp($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '138lp_applicant',
            'Связанные сведения ПЦН ЛЕГ.ПРОМ приказ №138 по заявителям',
            function ($id, $user) {
                return manager_Letters138lpByApplicant::create(['client/letters138lp', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters138lp($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '138lp_recipient',
            'Связанные сведения ПЦН ЛЕГ.ПРОМ приказ №138 по получателям',
            function ($id, $user) {
                return manager_Letters138lpByRecipient::create(['client/letters138lp', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters138lp($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '476lp_applicant',
            'Связанные сведения ПЦН ЛЕГ.ПРОМ приказ №476 по заявителям',
            function ($id, $user) {
                return manager_Letters476lpByApplicant::create(['client/letters476lp', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters476lp($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '476lp_recipient',
            'Связанные сведения ПЦН ЛЕГ.ПРОМ приказ №476 по получателям',
            function ($id, $user) {
                return manager_Letters476lpByRecipient::create(['client/letters476lp', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters476lp($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '328radio_applicant',
            'Связанные сведения ПЦН РАДИО приказ №328 по заявителям',
            function ($id, $user) {
                return manager_Letters328radioByApplicant::create(['client/letters328radio', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters328radio($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '328radio_recipient',
            'Связанные сведения ПЦН РАДИО приказ №328 по получателям',
            function ($id, $user) {
                return manager_Letters328radioByRecipient::create(['client/letters328radio', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters328radio($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '2742met_applicant',
            'Связанные сведения ПЦН МЕТАЛ приказ №2742 по заявителям',
            function ($id, $user) {
                return manager_Letters2742metByApplicant::create(['client/letters2742met', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters2742met($id);
                elete();
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '2742met_recipient',
            'Связанные сведения ПЦН МЕТАЛ приказ №2742 по получателям',
            function ($id, $user) {
                return manager_Letters2742metByRecipient::create(['client/letters2742met', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters2742met($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '2845met_applicant',
            'Связанные сведения ПЦН МЕТАЛ приказ №2845 по заявителям',
            function ($id, $user) {
                return manager_Letters2845metByApplicant::create(['client/letters2845met', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters2845met($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '2845met_recipient',
            'Связанные сведения ПЦН МЕТАЛ приказ №2845 по получателям',
            function ($id, $user) {
                return manager_Letters2845metByRecipient::create(['client/letters2845met', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters2845met($id);
                $o->setval('recipient_id', $client_id);
            }
        );
        $this->addDependency(
            '1918mash_applicant',
            'Связанные сведения ПЦН ИНВ.МАШ. приказ №1918 по заявителям',
            function ($id, $user) {
                return manager_Letters1918mashByApplicant::create(['client/letters1918mash', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters1918mash($id);
                $o->setval('applicant_id', $client_id);
            }
        );
        $this->addDependency(
            '1918mash_recipient',
            'Связанные сведения ПЦН ИНВ.МАШ. приказ №1918 по получателям',
            function ($id, $user) {
                return manager_Letters1918mashByRecipient::create(['client/letters1918mash', 'id' => $id], $user)->setClientId($id);
            },
            function ($id, $client_id) {
                $o = ObjectFactory::letters1918mash($id);
                $o->setval('recipient_id', $client_id);
            }
        );
    }

    /**
     * @return array
     */
    protected function getLinks()
    {
        $data = [];
        foreach ($this->objects as $key => $object) {
            $data[$object->id] = \yii\helpers\Url::to(['client/edit', 'id' => $object->id]);
        }
        return $data;
    }

    protected function getAllowedFields($object)
    {
        return array_filter(parent::getAllowedFields($object), function ($item) {
            if (preg_match('/^(documents|resolutions|inspections).\d+$/', $item)) {
                return false; // ссылки на связанные записи
            }
            if (preg_match('/^egrul_check_hist/', $item)) {
                return false; // история ЕГРЮЛ сверок
            }
            if (preg_match('/^hist_/', $item)) {
                return false; // исторические записи по атрибутам
            }
            return true;
        });
    }

}