<?php

class EpguDocx
{
    const TEMPLATE_PATH = '@app/views/docs/pgu-order';

    public static function templatePath($name)
    {
        return Yii::getAlias(self::TEMPLATE_PATH . '/' . $name);
    }

    public static function exists($orderTypeId)
    {
        return file_exists(self::templatePath('document_type' . $orderTypeId . 'e.docx'));
    }

    public static function docx2txt($docxContents)
    {
        $docxName = tempnam(sys_get_temp_dir(), 'doc2txt' . time());
        file_put_contents($docxName, $docxContents);

        $content = '';

        $zip = zip_open($docxName);
        if (!$zip || is_numeric($zip)) {
            return false;
        }
        while ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry) == false) {
                continue;
            }
            if (zip_entry_name($zip_entry) != "word/document.xml") {
                continue;
            }
            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
        }// end while
        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        unlink($docxName);

        return trim($striped_content);
    }

    /**
     *
     * @param $orderType ordertype_EpguBase
     * @return string
     */
    public static function makeDocument($orderType)
    {
        $TBS = self::prepareDocument($orderType);
        $TBS->Show(OPENTBS_STRING);
        return $TBS->Source;
    }

    /**
     * @param $orderType ordertype_EpguBase
     * @param $fileName
     */
    public static function sendDocument($orderType, $fileName)
    {
        $TBS = self::prepareDocument($orderType);
        $TBS->Show(OPENTBS_DOWNLOAD, $fileName);
        exit;
    }

    /**
     *
     * @param $orderType ordertype_EpguBase
     * @return clsTinyButStrong
     */
    public static function prepareDocument($orderType)
    {
        if ($orderType instanceof ordertype_Epgu3Base) {
            return $orderType->prepareDocument();
        }
        $typedoc = $orderType->getTypeId();

        $xmlContent = $orderType->getPguRequestXml();
        $xml = new SimpleXMLElement($xmlContent);
        $xml->registerXPathNamespace('ns', 'http://smev.gosuslugi.ru/request/fed/rev120528');
        [$r2] = $xml->xpath('/ns:Data/ns:FormData');

        if ($r2['ResultDoc'] == '1') {
            $resultdoc = 'Путем личного вручения заявителю, представителю заявителя';
        } elseif ($r2['ResultDoc'] == '2') {
            $resultdoc = 'Почтовым отправлением';
        }

        if (($typedoc >= 123) and ($typedoc <= 126)) {
            $activity = $r2->WorkInfo;
            foreach ($activity as $value) {
                //Адрес
                $temp['addr'] = $value->PlaceOfActivityAddress;
                $srlvd = $value->SRLDVocItemKind;
                $temp['type'] = '';
                foreach ($srlvd as $val) {
                    //Тип
                    $temp['type'] .= $val->SRLDVocItem . "; ";
                    $air = $val->AircraftKind->Aircraft;
                    $temp['name'] = '';
                    foreach ($air as $v) {
                        //Наименование
                        $temp['name'] .= $v . "; ";
                    }
                }
                $act[] = ['rank' => 'activity_avia', 'addr' => $temp['addr'], 'type' => $temp['type'], 'name' => $temp['name']];
            }
        }

        new clsOpenTBS; // autoload trigger
        $TBS = new clsTinyButStrong;
        $TBS->SetOption('noerr', true);
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
        //Название типа документа
        $tmp = PguOrderHtmlInfo::render($orderType->getOrder(), '');
        $tmp = explode("</h4><p>", $tmp);
        $typename = explode("</p>", $tmp[1]);
        $typename[0] .= " " . $orderType->getOrder()->getCategory();
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xmlContent, $vals, $index);
        xml_parser_free($p);
        $i = 0;
        $doc = [];
        $ActivityAddress = '';
        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and (($value['tag'] == 'AIRCRAFT') or ($value['tag'] == 'KindOfActivity1') or ($value['tag'] == 'KINDOFACTIVITY') or ($value['tag'] == 'KindOfActivity2') or ($value['tag'] == 'PlaceofActivityAddress'))) {
                $value['tag'] .= $i;
                $i++;
            }
            $doc[$value['tag']] = !isset($value['value']) ? '' : $value['value'];
        }
        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and (stristr($value['tag'], 'PlaceofActivityAddress') == true)) {
                $phone = isset($vals[$key + 2]['value']) ? $vals[$key + 2]['value'] : '';
                $PlaceofActivityAddress[] = ['rank' => 'activity_place', 'name' => $value['value'] . ", " . $phone . "; "];
                $ActivityAddress .= $value['value'] . ";";
            }
        }
        foreach ($vals as $value) {
            if (isset($doc[$value['tag']]) and ((stristr($value['tag'], 'KindOfActivity1') == true) or (stristr($value['tag'], 'KINDOFACTIVITY') == true) or (stristr($value['tag'],
                            'KindOfActivity2') == true))) {
                if (isset($value['attributes']['TYPE']) and $value['attributes']['TYPE'] != '') {
                    $KindOfActivity[] = ['rank' => 'activity_type', 'name' => $value['attributes']['TYPE'] . ";"];
                }
                if (isset($value['value']) and ($typedoc != '127')) {
                    $KindOfActivity[] = ['rank' => 'activity_type', 'name' => $value['value'] . ";"];
                }
            }
        }

        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and ((stristr($value['tag'], 'KINDOFPRODUCTION') == true))) {
                if (isset($value['value']) and $value['value'] != '') {
                    $temp = '';
                    if ($vals[$key + 2]['tag'] == 'OKP') {
                        $temp = 'ОКП ' . $vals[$key + 2]['value'];
                    }
                    if ($vals[$key + 2]['tag'] == 'OKVED') {
                        $temp = 'ОКВЭД ' . $vals[$key + 2]['value'];
                    }
                    $KindOfProduction[] = ['rank' => 'product_type', 'name' => $value['value'] . ' ' . $temp . ";"];
                }
            }
        }
        if (!isset($KindOfActivity) or empty($KindOfActivity)) {
            $KindOfActivity[] = ['rank' => 'activity_type', 'name' => ""];
        }
        if (!isset($KindOfProduction) or empty($KindOfProduction)) {
            $KindOfProduction[] = ['rank' => 'product_type', 'name' => ""];
        }

        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and ((stristr($value['tag'], 'DOCSERIES') == true))) {
                if (isset($vals[$key - 2]['tag'])) {
                    $docinfo[$vals[$key - 2]['tag']][$value['tag']] = isset($value['value']) ? $value['value'] : '';
                    $docinfo[$vals[$key - 2]['tag']][$vals[$key + 2]['tag']] = isset($vals[$key + 2]['value']) ? $vals[$key + 2]['value'] : '';
                    $docinfo[$vals[$key - 2]['tag']][$vals[$key + 4]['tag']] = isset($vals[$key + 4]['value']) ? $vals[$key + 4]['value'] : '';
                    $docinfo[$vals[$key - 2]['tag']][$vals[$key + 6]['tag']] = isset($vals[$key + 6]['value']) ? $vals[$key + 6]['value'] : '';
                    $docinfo[$vals[$key - 2]['tag']][$vals[$key + 8]['tag']] = isset($vals[$key + 8]['value']) ? $vals[$key + 8]['value'] : '';
                }
            }
        }
        $masstypes = [
            'INDUSTRIALSECURITYDOC' => 'Реквизиты документов о наличии заключения экспертизы промышленной безопасности в соответствии со статьей 13 Федерального закона «О промышленной безопасности опасных производственных объектов»',
            'MONITORINGDOCUMENT' => 'Реквизиты документов об осуществлении мониторинга в части состояния и загрязнения окружающей среды в соответствии со статьей 12 Федерального закона «Об уничтожении химического оружия»',
            'FIREPREVENTION' => 'Реквизиты документов о проверке состояния средств противопожарной защиты (пожарной сигнализации и пожаротушения), противопожарного водоснабжения и расчетного запаса специальных средств огнетушения, необходимого для ликвидации пожара',
            'BUILDINGDOCUMENT' => 'Реквизиты документов о соответствии специально предназначенных для осуществления лицензируемого вида деятельности помещений, зданий, сооружений и иных объектов требованиям, установленным Федеральным законом «О санитарно-эпидемиологическом благополучии населения»',
            'SECRETINFODOC' => 'Реквизиты документов о наличии допуска к сведениям, составляющим государственную тайну, в соответствии с Законом Российской Федерации «О государственной тайне»',
            'SECRETWORKDOC' => 'Реквизиты документов о наличии допуска к проведению работ (оказанию услуг) с использованием сведений, составляющих государственную тайну, в соответствии с Законом Российской Федерации «О государственной тайне»'
        ];
        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and ((stristr($value['tag'], 'ISSUEDATE') == true) or (stristr($value['tag'], 'LICISSUEDATE') == true))) {
                if (isset($vals[$key - 1]['tag']) and (($vals[$key - 1]['tag'] == 'PAYMENTCONFIRMATION') or ($vals[$key - 1]['tag'] == 'PROPERTYCONFIRMATION')
                        or ($vals[$key - 1]['tag'] == 'INDUSTRIALSECURITYDOC') or ($vals[$key - 1]['tag'] == 'MONITORINGDOCUMENT')
                        or ($vals[$key - 1]['tag'] == 'FIREPREVENTION') or ($vals[$key - 1]['tag'] == 'BUILDINGDOCUMENT')
                        or ($vals[$key - 1]['tag'] == 'SECRETINFODOC') or ($vals[$key - 1]['tag'] == 'SECRETWORKDOC')
                        or ($vals[$key - 1]['tag'] == 'REGISTRATIONINFO') or ($vals[$key - 1]['tag'] == 'ISSUEDLICINFO') or ($vals[$key - 1]['tag'] == 'PERMANENTUSEINFO'))) {
                    $docinfo[$vals[$key - 1]['tag']][$value['tag']] = isset($value['value']) ? $value['value'] : '';
                    $docinfo[$vals[$key - 1]['tag']][$vals[$key - 2]['tag']] = isset($vals[$key - 2]['value']) ? $vals[$key - 2]['value'] : '';
                    $docinfo[$vals[$key - 1]['tag']][$vals[$key + 2]['tag']] = isset($vals[$key + 2]['value']) ? $vals[$key + 2]['value'] : '';
                    $docinfo[$vals[$key - 1]['tag']][$vals[$key + 4]['tag']] = isset($vals[$key + 4]['value']) ? $vals[$key + 4]['value'] : '';
                    $docinfo[$vals[$key - 1]['tag']]['name'] = isset($masstypes[$vals[$key - 1]['tag']]) ? $masstypes[$vals[$key - 1]['tag']] : ' ';
                }
            }

            if ($value['tag'] == 'INN') {
                $docinfo['inn']['NUMBER'] = isset($value['value']) ? $value['value'] : '';
                $docinfo['inn'][$vals[$key + 3]['tag']] = isset($vals[$key + 3]['value']) ? $vals[$key + 3]['value'] : '';
                $docinfo['inn'][$vals[$key + 5]['tag']] = isset($vals[$key + 5]['value']) ? $vals[$key + 5]['value'] : '';
                $docinfo['inn'][$vals[$key + 7]['tag']] = isset($vals[$key + 7]['value']) ? $vals[$key + 7]['value'] : '';
                $docinfo['inn'][$vals[$key + 9]['tag']] = isset($vals[$key + 9]['value']) ? $vals[$key + 9]['value'] : '';
            }
        }

        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and ((stristr($value['tag'], 'STRINGCODE') == true))) {
                if (isset($vals[$key - 1]['tag']) and (($vals[$key - 1]['tag'] == 'ABILITYINFO'))) {
                    $d['StringCode'] = isset($vals[$key]['value']) ? $vals[$key]['value'] : '';
                    $d['Name'] = isset($vals[$key + 2]['value']) ? $vals[$key + 2]['value'] : '';
                    $d['Measure'] = isset($vals[$key + 4]['value']) ? $vals[$key + 4]['value'] : '';
                    $d['Q1'] = isset($vals[$key + 6]['value']) ? $vals[$key + 6]['value'] : '';
                    $d['Q2'] = isset($vals[$key + 8]['value']) ? $vals[$key + 8]['value'] : '';
                    $d['Q3'] = isset($vals[$key + 10]['value']) ? $vals[$key + 10]['value'] : '';
                    $d['Q4'] = isset($vals[$key + 12]['value']) ? $vals[$key + 12]['value'] : '';
                    $d['Sum'] = isset($vals[$key + 14]['value']) ? $vals[$key + 14]['value'] : '';
                    $ability[] = [
                        'rank' => 'ab',
                        'stringcode' => $d['StringCode'],
                        'name' => $d['Name'],
                        'm' => $d['Measure'],
                        'q1' => $d['Q1'],
                        'q2' => $d['Q2'],
                        'q3' => $d['Q3'],
                        'q4' => $d['Q4'],
                        's' => $d['Sum']
                    ];
                }
            }
        }
        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and ((stristr($value['tag'], 'NUMBER') == true))) {
                if (isset($vals[$key - 1]['tag']) and (($vals[$key - 1]['tag'] == 'CERTIFICATE'))) {
                    $numberold = isset($value['value']) ? $value['value'] : '';
                    $dateold = isset($vals[$key + 2]['value']) ? $vals[$key + 2]['value'] : '';
                    if ($dateold != '') {
                        $tobeornot = 'была';
                        $oldlic = 'Выдано свидетельство № ' . $numberold . ' от ' . $dateold . ' г.';
                    }
                }
            }
        }
        foreach ($vals as $key => $value) {
            if (isset($doc[$value['tag']]) and ((($value['tag'] == 'DESCRIPTION')))) {
                $d = [];

                $d['what'] = isset($vals[$key - 2]['tag']) ? $vals[$key - 2]['tag'] : '';
                switch ($d['what']) {
                    case 'NUCLEARFACILITY':
                        $d['cat'] = 'Деятельность, связанная с ядерной установкой';
                        break;
                    case 'RADIATIONSOURCE':
                        $d['cat'] = 'Деятельность, связанная с радиационными источниками';
                        break;
                    case 'STORAGEFACILITY':
                        $d['cat'] = 'Деятельность, связанная с пунктом хранения';
                        break;
                    case 'NUCLEARMATERIAL':
                        $d['cat'] = 'Деятельность, связанная с ядерными материалами';
                        break;
                    case 'RADIOACTIVESUBSTANCES':
                        $d['cat'] = 'Деятельность, связанная с радиоактивными веществами';
                        break;
                }
                $d['description'] = isset($vals[$key]['value']) ? $vals[$key]['value'] : '';
                $d['DecommissioningDate'] = isset($vals[$key + 2]['value']) ? $vals[$key + 2]['value'] : '';
                if ($vals[$key + 6]['tag'] == 'ACTIVITY') {
                    $d[$vals[$key + 6]['tag']] = isset($vals[$key + 6]['value']) ? $vals[$key + 6]['value'] : '';
                }

                $test = '';
                if ($vals[$key + 6]['tag'] == 'ACTIVITYLIST') {
                    $temp = '';
                    switch ($vals[$key + 7]['tag']) {
                        case 'LOCATION' :
                            $temp = 'Размещение' . ';';
                            break;
                        case 'BUILDING' :
                            $temp = 'Сооружение' . ';';
                            break;
                        case 'ENDING' :
                            $temp = 'Вывод из эксплуатации' . ';';
                            break;
                    }
                    switch ($vals[$key + 9]['tag']) {
                        case 'LOCATION' :
                            $temp .= 'Размещение' . ';';
                            break;
                        case 'BUILDING' :
                            $temp .= 'Сооружение' . ';';
                            break;
                        case 'ENDING' :
                            $temp .= 'Вывод из эксплуатации' . ';';
                            break;
                    }
                    switch ($vals[$key + 11]['tag']) {
                        case 'LOCATION' :
                            $temp .= 'Размещение' . ';';
                            break;
                        case 'BUILDING' :
                            $temp .= 'Сооружение' . ';';
                            break;
                        case 'ENDING' :
                            $temp .= 'Вывод из эксплуатации' . ';';
                            break;
                    }
                }
                if ((isset($temp)) and (!isset($d['ACTIVITY']) or empty($d['ACTIVITY']))) {
                    $test = $temp;
                }
                if ($d['description'] != '' and $d['DecommissioningDate'] != '') {
                    $radioact[] = [
                        'rank' => 'ra',
                        'what' => $d['what'],
                        'cat' => $d['cat'],
                        'desc' => $d['description'],
                        'ddate' => $d['DecommissioningDate'],
                        'act' => isset($d['ACTIVITY']) ? $d['ACTIVITY'] : '',
                        'actlst' => $test
                    ];
                }
            }
        }

        if (isset($doc['DOCINFO'])) {
            $doc['DOCINFO'] = str_replace('Копия', 'копию', str_replace('Выписка', 'выписку', $doc['DOCINFO']));
        }
        if (isset($radioact)) {
            foreach ($radioact as $key => $value) {
                if ($key < sizeof($radioact) - 1) {
                    if (($radioact[$key]['actlst'] == '') and ($radioact[$key]['what'] == $radioact[$key + 1]['what']) and ($radioact[$key]['act'] == '')) {
                        $radioact[$key]['actlst'] = $radioact[$key + 1]['actlst'];
                    }
                }
            }
        }
        $doc['ISSUEDATE'] = $r2->PaymentConfirmation->IssueDate;
        $doc['NUMBER'] = $r2->PaymentConfirmation->Number;
        $doc['SUM'] = $r2->PaymentConfirmation->Sum;
        if (!isset($doc['OGRN'])) {
            if (isset($doc['OGRNIP'])) {
                $doc['OGRN'] = $doc['OGRNIP'];
            }
        }
        $fio = isset($doc['LASTNAME']) ? $doc['LASTNAME'] . " " : '';
        $fio .= isset($doc['FIRSTNAME']) ? $doc['FIRSTNAME'] . " " : '';
        $fio .= isset($doc['MIDDLENAME']) ? $doc['MIDDLENAME'] : '';
        if (((isset($doc['ISSUEDATE'])) or (isset($doc['SUBMITREASON']))) and (((isset($doc['SERVICEAIM'])) and ($doc['SERVICEAIM'] == 2)) or (isset($doc['SERVICEAIM']) && $doc['SERVICEAIM'] == 'Дубликат лицензии'))) {
            $dubcopy = 'дубликата';
            $dubcopy1 = 'дубликат';
            $rekv = 'Приложение: № ' . $doc['NUMBER'] . ' от ' . $doc['ISSUEDATE'] . ' на сумму ' . $doc['SUM'] . ' р.';
        } else {
            $dubcopy = 'копии';
            $dubcopy1 = 'копию';
            $rekv = 'Приложение: № ' . $doc['NUMBER'] . ' от ' . $doc['ISSUEDATE'] . ' на сумму ' . $doc['SUM'] . ' р.';
        }


        if (isset($doc['SERVICEAIM']) and (($doc['SERVICEAIM'] == 1) or ($doc['SERVICEAIM'] == 'Копия лицензии')) and ($doc['NUMBER'] != '')) {
            $dubcopy = 'копии';
            $dubcopy1 = 'копию';
            $rekv = 'Приложение: № ' . $doc['NUMBER'] . ' от ' . $doc['ISSUEDATE'] . ' на сумму ' . $doc['SUM'] . ' р.';
        }

        if (isset($doc['OGRNIP'])) {
            $nameIP = $fio;
        }

        if (file_exists(self::templatePath('document_type' . $typedoc . 'e.docx'))) {
            $template = self::templatePath('document_type' . $typedoc . 'e.docx');
            if (($typedoc == 140) and (!isset($docinfo))) {
                $template = self::templatePath('document_type' . $typedoc . 'e_without_inn-ogrn.docx');
            }
            $TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
            $TBS->PlugIn(OPENTBS_DELETE_COMMENTS);
            $data[] = [
                'rank' => 'doc',
                'type' => $typename[0],
                'fullurname' => isset($doc['LEGALFULLNAME']) ? $doc['LEGALFULLNAME'] : '',
                'activity_address' => isset($ActivityAddress) ? $ActivityAddress : '',
                'shorturname' => isset($doc['LEGALSHORTNAME']) ? '(' . $doc['LEGALSHORTNAME'] . ')' : '',
                'occup' => isset($doc['APPLICANTPOST']) ? $doc['APPLICANTPOST'] : '',
                'regnum' => isset($doc['LICNUMBER']) ? $doc['LICNUMBER'] : '',
                'dubcopy' => $dubcopy,
                'dubcopy1' => $dubcopy1,
                'getdate' => isset($doc['LICISSUEDATE']) ? $doc['LICISSUEDATE'] : '',
                'licnumber_d' => isset($r2->DocumentsInfo->LicenseInfo->LicNumber) ? $r2->DocumentsInfo->LicenseInfo->LicNumber : '',
                'licissuedate_d' => isset($r2->DocumentsInfo->LicenseInfo->LicIssueDate) ? $r2->DocumentsInfo->LicenseInfo->LicIssueDate : '',
                'licvalidity_d' => isset($r2->DocumentsInfo->LicenseInfo->LicValidity) ? $r2->DocumentsInfo->LicenseInfo->LicValidity : '',
                'uaddress' => isset($doc['LEGALADDRESS']) ? $doc['LEGALADDRESS'] : '',
                'baddress' => isset($doc['BUSINESSMANADDRESS']) ? $doc['BUSINESSMANADDRESS'] : '',
                'docinfo' => isset($doc['DOCINFO']) ? $doc['DOCINFO'] : '',
                'licenseefullname' => isset($doc['LICENSEEFULLNAME']) ? ' "' . $doc['LICENSEEFULLNAME'] . '"' : '',
                'paddress' => isset($doc['POSTADDRESS']) ? $doc['POSTADDRESS'] : '',
                'inn' => isset($doc['INN']) ? $doc['INN'] : '',
                'ogrn' => isset($doc['OGRN']) ? $doc['OGRN'] : '',
                'ogrnip' => isset($doc['OGRNIP']) ? $doc['OGRNIP'] : '',
                'okpo' => isset($doc['OKPO']) ? $doc['OKPO'] : '',
                'okp' => isset($doc['OKP']) ? $doc['OKP'] : '',
                'okved' => isset($doc['OKVED']) ? $doc['OKVED'] : '',
                'issuedate' => isset($doc['ISSUEDATE']) ? $doc['ISSUEDATE'] : '',
                'licenddate' => isset($doc['LICENDDATE']) ? $doc['LICENDDATE'] : '',
                'number' => isset($doc['NUMBER']) ? $doc['NUMBER'] : '',
                'sum' => isset($doc['SUM']) ? $doc['SUM'] : '',
                'sreason' => isset($doc['SUBMITREASON']) ? $doc['SUBMITREASON'] : '',
                'rekv' => $rekv,
                //
                'addr' => isset($doc['addr']) ? $doc['addr'] : '',
                'pagecount' => isset($doc['PAGECOUNT']) ? $doc['PAGECOUNT'] : '',
                'requestedinfo' => isset($doc['REQUESTEDINFO']) ? $doc['REQUESTEDINFO'] : '',
                'nameip' => isset($nameIP) ? $nameIP : '',
                //
                'egrulorg' => isset($doc['egrulorg']) ? $doc['egrulorg'] : '',
                'egruldate' => isset($doc['egruldate']) ? $doc['egruldate'] : '',
                'egrulser' => isset($doc['egrulser']) ? $doc['egrulser'] : '',
                'egrulnum' => isset($doc['egrulnum']) ? $doc['egrulnum'] : '',
                //Выписка взрывч веществ ж/д
                'railway' => isset($doc['RAILWAYNAME']) ? $doc['RAILWAYNAME'] : '',
                'stationname' => isset($doc['STATIONNAME']) ? $doc['STATIONNAME'] : '',
                'stationcode' => isset($doc['STATIONCODE']) ? $doc['STATIONCODE'] : '',
                'enterprisecode' => isset($doc['ENTERPRISECODE']) ? $doc['ENTERPRISECODE'] : '',
                //Выписка взрывч веществ год выписки
                'year' => isset($doc['YEAR']) ? $doc['YEAR'] : '',
                //Место происх товара
                'placeoforigin' => isset($doc['PLACEOFORIGIN']) ? $doc['PLACEOFORIGIN'] : '',
                'productdiscription' => isset($doc['PRODUCTDISCRIPTION']) ? $doc['PRODUCTDISCRIPTION'] : '',
                'productnotation' => isset($doc['PRODUCTNOTATION']) ? $doc['PRODUCTNOTATION'] : '',
                'specificfeatures' => isset($doc['SPECIFICFEATURES']) ? $doc['SPECIFICFEATURES'] : '',
                'regnumber' => isset($doc['REGNUMBER']) ? $doc['REGNUMBER'] : '',
                'geographicalconditions' => isset($doc['GEOGRAPHICALCONDITIONS']) ? $doc['GEOGRAPHICALCONDITIONS'] : '',
                //Ядерная
                'numold' => isset($numberold) ? $numberold : '',
                'dateold' => isset($dateold) ? $dateold : '',
                'tobeornot' => isset($tobeornot) ? $tobeornot : 'не была',
                'oldlic' => isset($oldlic) ? $oldlic : '',
                'phone' => isset($doc['PHONE']) ? $doc['PHONE'] : '',
                'fax' => isset($doc['FAX']) ? $doc['FAX'] : '',
                'email' => isset($doc['EMAIL']) ? $doc['EMAIL'] : '',
                'resultdoc' => isset($resultdoc) ? $resultdoc : '',
                'fio' => $fio
            ];

            if (isset($PlaceofActivityAddress) and !empty($PlaceofActivityAddress)) {
                $TBS->MergeBlock('activity_address', $PlaceofActivityAddress);
            }
            if (isset($KindOfActivity) and !empty($KindOfActivity)) {
                $TBS->MergeBlock('activity_type', $KindOfActivity);
            }
            if (isset($act) and !empty($act)) {
                $TBS->MergeBlock('activity_avia', $act);
                $TBS->MergeBlock('activity_avia1', $act);
            }

            if (isset($ability) and !empty($ability)) {
                $TBS->MergeBlock('ab', $ability);
            }
            if (isset($radioact) and !empty($radioact)) {
                $TBS->MergeBlock('ra', $radioact);
                $TBS->MergeBlock('ra1', $radioact);
            }

            $TBS->MergeBlock('product_type', $KindOfProduction);
            $TBS->MergeBlock('doc', $data);

            if (isset($docinfo)) {
                foreach ($docinfo as $key => $value) {
                    /* @var $value array */
                    $key = strtolower($key);
                    if (($key == 'ogrn') and (!isset($value['ISSUEDATE']))) {
                        $value = isset($docinfo['OGRNIP']) ? $docinfo['OGRNIP'] : '';
                    }
                    $dat[] = [
                        'rank' => $key,
                        'issuedate' => isset($value['ISSUEDATE']) ? $value['ISSUEDATE'] : '',
                        'docseries' => isset($value['DOCSERIES']) ? $value['DOCSERIES'] : '',
                        'number' => isset($value['NUMBER']) ? $value['NUMBER'] : '',
                        'distr' => isset($value['DISTRIBUTER']) ? $value['DISTRIBUTER'] : '',
                        'distrad' => isset($value['DISTRIBUTERADDRESS']) ? $value['DISTRIBUTERADDRESS'] : '',
                        'propdoc' => isset($value['PROPERTYDOCOTHER']) ? $value['PROPERTYDOCOTHER'] : '',
                        'sum' => isset($value['SUM']) ? $value['SUM'] : '',
                        'name' => isset($value['name']) ? $value['name'] : '',
                        ////
                        'licnumber' => isset($value['LICNUMBER']) ? $value['LICNUMBER'] : '',
                        'licissuedate' => isset($value['LICISSUEDATE']) ? $value['LICISSUEDATE'] : '',
                        'licvalidity' => isset($value['LICVALIDITY']) ? $value['LICVALIDITY'] : '',
                        'licbegindate' => isset($value['LICBEGINDATE']) ? $value['LICBEGINDATE'] : '',
                        'licenddate' => isset($value['LICENDDATE']) ? $value['LICENDDATE'] : ''
                    ];
                    $TBS->MergeBlock($key, $dat);
                }
            }
            if (!isset($doc['ogrn']['issuedate'])) { // useless?
                if (isset($doc['ogrnip'])) {
                    $doc['ogrn'] = $doc['ogrnip'];
                }
            }
        } else {
            $template = self::templatePath('doc_notemplate.docx');
            $TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
            $TBS->PlugIn(OPENTBS_DELETE_COMMENTS);
            $data[] = [
                'rank' => 'doc',
                'type' => $typename[0]
            ];
            $TBS->MergeBlock('doc', $data);
        }
        return $TBS;
    }

}
