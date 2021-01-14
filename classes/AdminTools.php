<?php

namespace main;

use main\models\DynamicModel;
use main\ordertype\Factory;

class AdminTools
{

    /**
     * Перевод заявления в статус "Черновик"
     * Находит по указанному id заявление и устанавливает ему статус "Черновик".
     * Опционально очищает историю смены статусов заявления
     * @param int $orderId id заявления
     * @param bool $clearChgstat очистить историю смены статусов
     * @throws \yii\db\Exception
     */
    public function orderToStatus0($orderId, $clearChgstat = true)
    {
        $o = \ObjectFactory::order($orderId);
        $ot = Factory::get($o);

        $o->setStatus(0);
        if ($clearChgstat) {
            $o->deldata('chgstat');
        }

        echo "Текущий статус заявления <a href='{$ot->getOrderUrl()}'>{$o->getName()}</a>: {$o->getStatusName()}\n";
        echo "Кол-во записей о смене статуса: " . count($o->getStatusHistory());
    }

    /**
     * Перевод заявления в указанный статус
     * Находит по указанному id заявление и устанавливает ему статус с записью в историю изменения статусов,
     * бизнес-логика маршрутов не выполняются (отправка уведомлений, взаимодействие с СЭДО или что0то такое
     * 300    Поступило заявление и документы
     * 301    Поступило исправленное заявление
     * 302    Поступило исправленное заявление и документы
     * 310    Уведомление о необходимости устранения выявленных нарушений вручено/направлено заявителю
     * 320    Заявление зарегистрировано, назначен ответственный исполнитель
     * 330    Отправлен запрос СМЭВ
     * 331    Экспертная комиссия
     * 332    Документарная/выездная проверка на соответствие лицензионным требованиям
     * 333    Опись с отметкой о дате приема документов вручена/направлена заявителю
     * 334    Приложенные документы не соответствуют описи
     * 335    Заявление и документы возвращены заявителю
     * 340    Результат услуги отправлен/готов к выдаче
     * 341    Направлено уведомление об отказе в предоставлении услуги
     * 342    Принято решение о предоставлении лицензии
     * 343    Принято решение о переоформлении лицензии
     * 344    Принято решение о предоставлении копии/дубликата лицензии
     * 345    Оформлена выписка из реестра лицензий/справка об отсутствии сведений
     * 346    Принято решение о прекращении лицензии
     * @param int $orderId id заявления
     * @param int $statusId id статуса
     * @throws \yii\db\Exception
     */
    public function orderToStatusN($orderId, $statusId)
    {
        $o = \ObjectFactory::order($orderId);
        $ot = Factory::get($o);

        $allowedStatusList = [];
        foreach (\OrderWorkflow::find($o)->getData() as $v) {
            $allowedStatusList[] = $v['start_status_id'];
            $allowedStatusList[] = $v['end_status_id'];
        }
        if (!in_array($statusId, $allowedStatusList)) {
            throw new \RuntimeException('Статуса нет в маршруте заявления: ' . $statusId);
        }
        $o->setStatus($statusId); // изменение статуса
        $o->addStatusHistory(\Yii::$app->user->id, ''); // запись в историю изменения статуса

        echo "Текущий статус заявления <a href='{$ot->getOrderUrl()}'>{$o->getName()}</a>: {$o->getStatusName()}";
    }

    /**
     * Изменение №, даты входящего и ответственного для заявлений по госуслугам
     * Если не указывать значение, поле не будет изменено
     * @param int $orderId id заявления
     * @param string $number № входящего
     * @param string $date Дата входящего, формат dd-mm-yyyy
     * @param int $userId id пользователя
     * @throws \yii\db\Exception
     */
    public function orderUpdateIndoc($orderId, $number = null, $date = null, $userId = null)
    {
        $o = \ObjectFactory::order($orderId);
        $ot = Factory::get($o);

        if ($o->getval('indoc.doc_num') === null || $o->getval('indoc.doc_date') === null) {
            throw new \RuntimeException('Заявление не содержит полей "№ и дата вх.", не тот тип заявления?');
        }

        if ($number != null) {
            $o->setval('indoc.doc_num', $number);
        }
        if ($date != null) {
            $o->setval('indoc.doc_date', $date);
        }
        if ($userId != null) {
            $o->setval('indoc.responsibleid', $userId);
        }

        $user = \main\models\User::findOne($o->getval('indoc.responsibleid'));

        echo "Текущий статус заявления <a href='{$ot->getOrderUrl()}'>{$o->getName()}</a>: {$o->getStatusName()}\n";
        echo "№ вх.: {$o->getval('indoc.doc_num')}\n";
        echo "Дата вх.: {$o->getval('indoc.doc_date')}\n";
        echo "Ответственный: " . ($user->name ?? '- не указан -');
    }

    /**
     * @return DynamicModel[]
     */
    public static function list()
    {
        try {
            $r = new \ReflectionClass(new self);
        } catch (\ReflectionException $e) {
            return [];
        }
        $models = [];
        foreach ($r->getMethods() as $m) {
            if ($m->isStatic()) {
                continue;
            }
            if (!$m->getDocComment()) {
                throw new \RuntimeException('нет phpdoc описания для метода: ' . $m->getName());
            }
            $title = null;
            $descr = [];
            $paramTypes = [];
            $paramLabels = [];
            foreach (explode("\n", substr($m->getDocComment(), 3, -2)) as $k => $v) {
                if (!preg_match('/^\s+\*\s*(.*)$/', $v, $matches)) {
                    continue;
                }
                if ($k === 1) {
                    $title = $matches[1];
                } elseif (preg_match('/^@param\s+(float|int|string|bool)\s+\$([a-zA-Z0-9]+)\s+(.*)$/', $matches[1], $mp)) {
                    $paramTypes[$mp[2]] = $mp[1];
                    $paramLabels[$mp[2]] = $mp[3];
                } elseif (!preg_match('/^@/', $matches[1])) {
                    $descr[] = $matches[1];
                }
            }

            $model = new DynamicModel();
            $model->title = $title;
            $model->description = implode("\n", $descr);
            $model->method = $m->getName();
            $model->attributeTypes = $paramTypes;
            $model->attributeLabels = $paramLabels;
            foreach ($m->getParameters() as $p) {
                $name = $p->getName();
                if (!isset($paramTypes[$name])) {
                    throw new \RuntimeException('нет phpdoc описания для параметра ' . $name . ' метода ' . $m->getName());
                }
                $model->defineAttribute($p->getName());
                if ($p->isDefaultValueAvailable()) {
                    try {
                        if ($p->getDefaultValue() !== null) {
                            $model->addRule($name, 'required');
                        }
                        $model->{$name} = $p->getDefaultValue();
                    } catch (\ReflectionException $e) {
                        throw new \RuntimeException('не удалось получить default value для параметра ' . $name . ' метода ' . $m->getName());
                    }
                } else {
                    $model->addRule($name, 'required');
                }
                switch ($paramTypes[$name]) {
                    case 'int':
                        $model->addRule($name, 'integer');
                        break;
                    case 'string':
                        $model->addRule($name, 'string', ['min' => 1, 'max' => 255]);
                        break;
                    default:
                        $model->addRule($name, 'safe');
                        break;
                }
            }

            $models[$m->getName()] = $model;
        }
        return $models;
    }

    /**
     * Удаление заявления
     * Находит по указанному id заявление и удаляет его
     * @param int $orderId id заявления
     * @throws \yii\db\Exception
     */
    public function orderDelete($orderId)
    {
        $o = \ObjectFactory::order($orderId);
        $o->delete();

        echo "Заявление удалено: " . $orderId;
    }

    /**
     * Восстановление удаленной eav-записи
     * По указанному типу и id восстановливает удаленную eav-запись, без учета зависимостей, типы записей
     * resolution - решение
     * inspection - проверка
     * document - документ
     * client - контрагент
     * order - заявление (не поддерживается - не восстанавливает маршрут)
     * dossier - лицензия (не поддерживается)
     * courtverdict - решения суда
     * penalty - административные наказания
     * commission - комиссии
     * gmp - запись реестра "Заключения GMP"
     * medicament - запись реестра "Лекартсвенные препараты"
     * medlocal - запись реестра "Локализация СП"
     * taxfree - taxfree-заявление
     * tfprotocol - taxfree-протокол
     * tforder - taxfree-приказ
     * vehiclemaker - запись реестра "Реестр ТС"
     * lettersXXXX - запись реестра "ПЦН"
     * @param string $objectType тип записи
     * @param int $objectId id записи
     * @throws \yii\db\Exception
     */
    public function eavObjectRecover($objectType,$objectId)
    {
        $fqcName = \ObjectFactory::fqcName(trim($objectType));
        /* @var $fqcName \main\eav\object\Base */
        $o = $fqcName::recover($objectId);

        $url = \yii\helpers\Url::to(['/'.trim($objectType).'/'.$objectId]);
        echo "Запись восстановлена: <a href='{$url}'>{$o->getName()}</a>";
    }

    /**
     * Выгрузка данных eav-записи
     * По указанному типу и id выгружает данные в json-формате
     * resolution - решение
     * inspection - проверка
     * document - документ
     * client - контрагент
     * order - заявление (не поддерживается - не восстанавливает маршрут)
     * dossier - лицензия (не поддерживается)
     * courtverdict - решения суда
     * penalty - административные наказания
     * commission - комиссии
     * gmp - запись реестра "Заключения GMP"
     * medicament - запись реестра "Лекартсвенные препараты"
     * medlocal - запись реестра "Локализация СП"
     * taxfree - taxfree-заявление
     * tfprotocol - taxfree-протокол
     * tforder - taxfree-приказ
     * vehiclemaker - запись реестра "Реестр ТС"
     * lettersXXXX - запись реестра "ПЦН"
     * @param string $objectType тип записи
     * @param int $objectId id записи
     * @throws \yii\db\Exception
     */
    public function eavObjectExport($objectType,$objectId)
    {
        $o = \ObjectFactory::load($objectType,$objectId);
        $json = json_encode($o->getdata(''), JSON_UNESCAPED_UNICODE);
        \Yii::$app->response
            ->sendContentAsFile($json, sprintf('%s_%06d.json', $objectType, $objectId), ['mimeType' => 'application/json'])
            ->send();
        exit;
    }

    /**
     * Загрузка данных eav-записи
     * По указанному типу и id загружает данные в json-формате
     * resolution - решение
     * inspection - проверка
     * document - документ
     * client - контрагент
     * order - заявление (не поддерживается - не восстанавливает маршрут)
     * dossier - лицензия (не поддерживается)
     * courtverdict - решения суда
     * penalty - административные наказания
     * commission - комиссии
     * gmp - запись реестра "Заключения GMP"
     * medicament - запись реестра "Лекартсвенные препараты"
     * medlocal - запись реестра "Локализация СП"
     * taxfree - taxfree-заявление
     * tfprotocol - taxfree-протокол
     * tforder - taxfree-приказ
     * vehiclemaker - запись реестра "Реестр ТС"
     * lettersXXXX - запись реестра "ПЦН"
     * @param string $objectType тип записи
     * @param int $objectId id записи
     * @param string $text json-данные
     */
    public function eavObjectImport($objectType,$objectId,$text)
    {
        $o = \ObjectFactory::load($objectType, $objectId);
        $data = json_decode($text,true);
        $o->setdata($data);

        echo "Данные загружены в запись " . $o->getName();
    }
}