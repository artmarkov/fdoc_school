<?php

namespace main\controllers;

use Yii;
use yii\helpers\Url;
use main\SessionStorage;

class SmartselectController extends BaseController
{

    public $layout = 'main-simple';

    public function actionIndex($type)
    {
        $sessionStorage = SessionStorage::get('smartselect');
        $sessionStorage->register('form');
        $sessionStorage->register('field');
        $sessionStorage->register('submit');

        $request = Yii::$app->request;
        if ($request->get('form')) {
            $sessionStorage->save('form', Yii::$app->request->get('form'));
        }
        if ($request->get('field')) {
            $sessionStorage->save('field', Yii::$app->request->get('field'));
        }
        if ($request->get('submit')) {
            $sessionStorage->save('submit', Yii::$app->request->get('submit'));
        }

        $formName = $sessionStorage->load('form');
        $formField = $sessionStorage->load('field');
        $formSubmit = $sessionStorage->load('submit');

        $view = [];
        $m = $this->getManager($type, [$this->getRoute(), 'type' => $type]);
        if ($m->handleRequest($request)) {
            $id = $m->getSelectedId();
            if (null !== $id) {
                $view['selectedId'] = $id;
                $view['display'] = $m->getSelectedValue();
                $view['form'] = $formName;
                $view['field'] = $formField;
                $view['submit'] = $formSubmit;
            } else {
                return $this->redirect(Url::to([$this->getRoute(), 'type' => $type]));
            }
        }
        $view['manager'] = $m->render();
        return $this->render('index', $view);
    }

    protected function getManager($type, $route)
    {
        switch ($type) {
            case 'user':
                return \main\manager\UserSelect::create($route, Yii::$app->user->identity);
            case 'usergroup':
                return \main\manager\GroupSelect::create($route, 'user');
            case 'client':
                return \manager_ClientSelect::create($route, Yii::$app->user->identity);
            case 'employees':
                return \manager_EmployeesSelect::create($route, Yii::$app->user->identity);
            case 'students':
                return \manager_StudentsSelect::create($route, Yii::$app->user->identity);
            case 'parents':
                return \manager_ParentsSelect::create($route, Yii::$app->user->identity);
            case 'order':
                $m = \manager_OrderSelect::create($route, Yii::$app->user->identity);
                $m->setSubType(substr($type, 6));
                return $m;

        }
        throw new \RuntimeException('unimplemented');
    }

}
