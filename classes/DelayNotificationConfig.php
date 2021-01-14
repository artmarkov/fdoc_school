<?php

namespace main;

class DelayNotificationConfig
{
    const ROLE = 'role';
    const ROLE_PLUS_HEAD = 'role!';
    /**
     * @var array
     */
    public $data;

    public function __construct($data = [])
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('not an array');
        }
        $this->data = [];
        foreach ($data as $day => $userIds) {
            foreach ($userIds as $id) {
                $this->data[$id][] = ltrim($day, 'd');
            }
        }
    }

    /**
     * Добавляет получателя в оповещения
     * @param int|string $rcpt id пользователя или строковый код
     * @param array $days список дней
     * @return $this
     */
    public function add($rcpt, $days)
    {
        $this->data[$rcpt] = $days;
        return $this;
    }

    /**
     * Удаляет получателя из оповещений
     * @param int|string $rcpt
     * @return $this
     */
    public function remove($rcpt)
    {
        unset($this->data[$rcpt]);
        return $this;
    }

    /**
     * Возвращает список id пользовтелей для оповещения в этот день
     * @param $day
     * @return array
     */
    public function findByDay($day)
    {
        return array_keys(array_filter($this->data, function ($v) use ($day) {
            return in_array($day, $v);
        }));
    }

    public function export()
    {
        $result = [];
        foreach ($this->data as $userId => $days) {
            foreach ($days as $d) {
                $result['d' . $d][] = $userId;
            }
        }
        return $result;
    }

}
