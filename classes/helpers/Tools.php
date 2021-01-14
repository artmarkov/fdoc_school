<?php

namespace main\helpers;

use DateTime;
use RefBook;
use Yii;
use yii\base\InvalidConfigException;

class Tools
{
    /**
     * Возвращает дату-время в формате приянтой в системе
     * @param int|string|DateTime $value время в одном из форматов
     * @param string $format формат строки для вывода
     * @return string
     */
    public static function asDateTime($value, $format = null)
    {
        try {
            return Yii::$app->formatter->asDateTime($value, $format);
        } catch (InvalidConfigException $e) {
            Yii::error('Ошибка форматирования даты: ' . $e->getMessage());
            Yii::error($e);
            return $value;
        }
    }

    /**
     * Возвращает дату в формате приянтой в системе
     * @param int|string|DateTime $value время в одном из форматов
     * @param string $format формат строки для вывода
     * @return string
     */
    public static function asDate($value, $format = null)
    {
        try {
            return Yii::$app->formatter->asDate($value, $format);
        } catch (InvalidConfigException $e) {
            Yii::error('Ошибка форматирования даты: ' . $e->getMessage());
            Yii::error($e);
            return $value;
        }
    }

    /**
     * Вовзращает unix-timestamp для даты
     * @param int|string|DateTime $value время в одном из форматов
     * @return string
     */
    public static function asTimestamp($value)
    {
        return Yii::$app->formatter->asTimestamp($value);
    }

    /**
     * Возвращает строку вид "X дней назад"
     * @param int $timestamp
     * @return string
     */
    public static function ago($timestamp)
    {
        $difference = time() - $timestamp;
        if ($difference < 0) {
            return '';
        }
        $periods = [
            ['секунду', 'секунды', 'секунд'],
            ['минуту', 'минуты', 'минут'],
            ['час', 'часа', 'часов'],
            ['день', 'дня', 'дней'],
            ['неделя', 'недели', 'недель'],
            ['месяц', 'месяца', 'месяцев'],
            ['год', 'года', 'лет']
        ];
        $lengths = ["60", "60", "24", "7", "4.35", "12", "9999"];
        for ($j = 0; $difference >= $lengths[$j]; $j++) {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        if ($difference >= 11 && $difference <= 19) {
            $idx = 2;
        } else {
            $i = $difference % 10;
            switch ($i) {
                case (1):
                    $idx = 0;
                    break;
                case (2):
                case (3):
                case (4):
                    $idx = 1;
                    break;
                default:
                    $idx = 2;
            }
        }
        $text = $difference . ' ' . $periods[$j][$idx] . ' назад';
        return $text;
    }

    /**
     * Возвращает строковое представление интервала времени
     * @param int $difference в секундах
     * @return string
     */
    public static function timeString($difference)
    {
        if ($difference < 0) {
            return '';
        }
        $periods = [
            ['секунду', 'секунды', 'секунд'],
            ['минуту', 'минуты', 'минут'],
            ['час', 'часа', 'часов'],
            ['день', 'дня', 'дней'],
            ['неделя', 'недели', 'недель'],
            ['месяц', 'месяца', 'месяцев'],
            ['год', 'года', 'лет']
        ];
        $lengths = ['60', '60', '24', '7', '4.35', '12'];
        for ($j = 0; $j < count($lengths) && $difference >= $lengths[$j]; $j++) {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        if ($difference >= 11 && $difference <= 19) {
            $idx = 2;
        } else {
            $i = $difference % 10;
            switch ($i) {
                case (1):
                    $idx = 0;
                    break;
                case (2):
                case (3):
                case (4):
                    $idx = 1;
                    break;
                default:
                    $idx = 2;
            }
        }
        return $difference . ' ' . $periods[$j][$idx];
    }

    /**
     * Возвращает первый элемент массива или false
     * @param array $array
     * @return mixed
     */
    public static function firstElement($array)
    {
        return reset($array);
    }

    /**
     * Возвращает последний элемент массива или false
     * @param array $array
     * @return mixed
     */
    public static function lastElement($array)
    {
        return end($array);
    }

    /**
     * Возвращает код региона по адресу
     * @param string $address Адрес
     * @return int|null
     */
    public static function guessRegionCode($address)
    {
        if ($address && $fiasId = Yii::$app->dadata->guessRegion($address)) {
            return (int)RefBook::find('region-id-by-fias')->getValue($fiasId);
        }
        return null;
    }

    /**
     * @param string $date дата
     * @param string $diff +1 month, -90 days etc
     * @param string $format
     * @return string
     */
    public static function dateModify($date, $diff, $format = 'd-m-Y')
    {
        $d = DateTime::createFromFormat($format, $date);
        $startDay = $d->format('j');
        $d->modify($diff);
        $endDay = $d->format('j');
        if ($startDay != $endDay && strpos($diff, 'month') !== false) {
            $d->modify('last day of last month');
        }
        return $d->format($format);
    }

    public static function arrayFilter($input, $callback = null)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = self::arrayFilter($value, $callback);
            } elseif (is_object($value)) {
                $value = self::arrayFilter((array)$value, $callback);
            }
        }
        return $callback ? array_filter($input, $callback) : array_filter($input);
    }
}