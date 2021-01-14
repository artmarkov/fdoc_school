<?php

use main\acl\Resource;
use main\acl\ResourceType;
use main\acl\RoleAcl;
use main\acl\Rule;
use main\acl\UserAcl;
use main\eav\object\Search;
use main\eav\object\Sort;

/**
 * Class m200101_054527_post_oracle_cleanup
 */
class m200101_054527_post_oracle_cleanup extends \main\BaseMigration
{
    protected $registerRoutes = [
        'admin/dev',
        'admin/sessions',
        'calendar/index',
        'client',
        'client/index',
        'oksm',
        'oksm/index',
        'role/index',
        'site',
        'site/about',
        'site/help',
        'site/index',
        'support',
        'support/index',
        'user/index',
    ];
    protected $seqFixList = [
        'mail_queue',
        'guide_client_status',
        'guide_oksm',
        'guide_private_ip_list',
    ];

    /**
     * {@inheritdoc}
     * @throws Throwable
     */
    public function safeUp()
    {
        // 1 delete form fields and action resources
        $tForm = ResourceType::findOne('form');
        $tRoot = $tForm->findResource($tForm->root);
        $ids = $tForm->getResources()->where(['not',['pid'=>$tRoot->id]])->select('id')->column();
        Rule::deleteAll(['rsrc_id' => $ids]);

        // 1a delete public form
        $ids = $tForm->getResources()->where(['name'=>'public'])->select('id')->column();
        Rule::deleteAll(['rsrc_id' => $ids]);

        // 2 remove fake route resources
        $tRoute = ResourceType::findOne('route');
        $ids = $tRoute->getResources()->where(['like', 'name', '-fake-%', false])->select('id')->column();
        Rule::deleteAll(['rsrc_id' => $ids]);

        $ids = (new \yii\db\Query)
            ->select('r.id')
            ->from('acl_resource r')
            ->leftJoin('acl_rules t', 'r.id=t.rsrc_id')
            ->where(['t.rsrc_id'=>null])
            ->andWhere(['not',['type'=>'route','name'=>'pretrial']]) // у pretrial acl только на вкладки
            ->groupBy('r.id')
            ->column();

        UserAcl::deleteAll(['rsrc_id' => $ids]);
        RoleAcl::deleteAll(['rsrc_id' => $ids]);
        Resource::deleteAll(['id' => $ids]);

        // pre-registering routes
        foreach($this->registerRoutes as $route) {
            $tRoute->register($route);
        }

        // fix sequences
        foreach ($this->seqFixList as $v) {
            $this->db->createCommand()->resetSequence($v)->execute();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
