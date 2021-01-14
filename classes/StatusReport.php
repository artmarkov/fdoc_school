<?php

namespace main;

use yii\db\Expression;
use yii\db\Query;

class StatusReport
{

    public static function get()
    {
        $o = new self();
        return $o->getData();
    }

    public function getData()
    {
        return [
            'cron' => $this->getCronStats(),
            'counts' => $this->getCounts(),
            'last_items' => $this->getLasts(),
        ];
    }

    protected function getCronStats()
    {
        return (new Query)
            ->from('tasks')
            ->select('command,schedule,last_run')
            ->where(['disabled'=>'0'])
            ->orderBy('command')
            ->all();
    }

    protected function getCounts()
    {
        $result = [
            'files' => 0,
            'users' => 0,
            'clients' => 0,
            'orders' => 0,
            'pretrials' => 0,
            'mail' => 0,
            'request' => 0,
        ];
        $result['files'] = (new Query)
            ->from('files')
            ->select(new Expression('count(id)'))
            ->where(['deleted_at'=>null])
            ->scalar();

        $result['users'] = (new Query)
            ->from('users')
            ->select(new Expression('count(id)'))
            ->scalar();

        foreach (['client'] as $v) {
        $result[$v . 's'] = (new Query)
            ->from($v . '_sort')
            ->select(new Expression('count(o_id)'))
            ->scalar();
        }

        $result['mail'] = (new Query)
            ->from('mail_queue')
            ->select(new Expression('count(id)'))
            ->scalar();

        $result['request'] = (new Query)
            ->from('requests')
            ->select(new Expression('count(id)'))
            ->scalar();

        return $result;
    }

    protected function getLasts()
    {
        $result = [
            'files' => [],
            'users' => [],
            'clients' => [],
            'mail' => [],
            'request' => [],
        ];

        $result['files'] = (new Query)
            ->from('files')
            ->select('id,created_at,name,size')
            ->where(['deleted_at'=>null])
            ->orderBy('id desc')
            ->limit(10)
            ->all();

        $result['mail'] = (new Query)
            ->from('mail_queue')
            ->select('id,created_at,subject')
            ->orderBy('id desc')
            ->limit(10)
            ->all();

        $result['request'] = (new Query)
            ->from('requests')
            ->select('id,created_at,url,time,user_id')
            ->orderBy('id desc')
            ->limit(10)
            ->all();

        return $result;
    }

}