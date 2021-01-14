<?php

namespace main\controllers;

use main\AdminTools;
use main\models\User;
use main\StatusReport;
use main\ui\Notice;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use main\models\Session;

class AdminController extends BaseController
{

    /**
     * Список сеансов пользователей
     *
     * @return string
     */
    public function actionSessions()
    {
        $this->view->params['tabMenu'] = $this->getMenu();
        return $this->render('sessions', [
            'sessions' => Session::getList()
        ]);
    }

    public function actionLogins()
    {
        $userId = Yii::$app->request->get('user_id');
        $query = (new \yii\db\Query)
            ->select([
                'e.p1 user_id',
                'e.created_at',
                'u.name',
                new Expression('(select (regexp_matches(descr, \'ip=([0-9\.]+)\', \'g\'))[1]) ip'),
                new Expression('(select (regexp_matches(descr, \'agent=(.*) ip=\', \'g\'))[1]) user_agent')
            ])
            ->from('events e')
            ->leftJoin('users u', 'e.p1=u.id')
            ->where(['class' => 'event_UserLogin'])
            ->andFilterWhere(['e.p1' => $userId])
            ->orderBy('e.id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        $this->view->params['tabMenu'] = $this->getMenu();
        return $this->render('logins', [
            'userId' => $userId,
            'dataProvider' => $dataProvider,
            'userList' => ArrayHelper::map(User::find()->select('id,name')->orderBy('name')->asArray()->all(), 'id', 'name')
        ]);
    }

    public function actionJournal()
    {
        $userId = Yii::$app->request->get('user_id');
        $query = (new \yii\db\Query)
            ->select([
                'r.user_id',
                'r.created_at',
                'r.url',
                'r.post',
                'r.time',
                'r.mem_usage_mb',
                'r.http_status',
                'u.name'
            ])
            ->from('requests r')
            ->leftJoin('users u', 'r.user_id=u.id')
            ->andFilterWhere(['r.user_id' => $userId])
            ->orderBy('r.id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        $this->view->params['tabMenu'] = $this->getMenu();
        return $this->render('journal', [
            'userId' => $userId,
            'dataProvider' => $dataProvider,
            'userList' => ArrayHelper::map(User::find()->select('id,name')->orderBy('name')->asArray()->all(), 'id', 'name')
        ]);
    }

    public function actionPhp()
    {
        return $this->render('dev', [
            'phpinfoUrl' => Url::to(['admin/php-info'])
        ]);
    }

    public function actionTools()
    {
        $this->view->title = 'Инструменты админа';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $list = AdminTools::list();
        foreach ($list as $method => $model) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                try {
                    ob_start();
                    call_user_func_array([new AdminTools, $method], $model->getAttributes());
                    Notice::registerSuccess(nl2br(ob_get_clean()), 'Успешное выполнение <strong>' . $model->title . '</strong>');
                } catch (\Exception $e) {
                    Notice::registerError(nl2br($e), 'Ошибка выполнения <strong>' . $model->title . '</strong>');
                }
                return $this->refresh('#' . $method);
            }
        }

        return $this->render('tools', [
            'models' => $list
        ]);
    }

    public function actionStatus()
    {
        $this->view->title = 'Статус системы';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('status', [
            'data' => StatusReport::get()
        ]);
    }

    public function actionPhpInfo()
    {
        return $this->render('phpinfo');
    }

    protected function getMenu()
    {
        return [
            [['admin/sessions'], 'Сеансы'],
            [['admin/logins'], 'Входы в систему'],
            [['admin/journal'], 'Журнал'],
        ];
    }

}
