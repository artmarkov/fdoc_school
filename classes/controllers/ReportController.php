<?php

namespace main\controllers;

use Report;
use Yii;
use main\DocTemplate;

class ReportController extends BaseController
{

//    public function actionIndex()
//    {
//        return $this->redirect(['report/smev-consumer']);
//    }
//
//    /**
//     * @return string
//     */
//    public function actionSmevConsumer()
//    {
//        $this->view->title = 'Отчет1';
//        $this->view->params['breadcrumbs'][] = $this->view->title;
//        $this->view->params['tabMenu'] = $this->getMenu();
//
//        $req = Yii::$app->request;
//        $dateStart = $req->post('dateStart', date('d-m-Y', time() - 24 * 7 * 3600));
//        $dateEnd = $req->post('dateEnd', date('d-m-Y'));
//        $orders = null;
//        if ($req->post('html')) {
//            $orders = Report::getSmevConsumeStats($dateStart, $dateEnd);
//        } elseif ($req->post('excel')) {
//            $user = Yii::$app->user->identity;
//            $data = [
//                'dateStart' => $dateStart,
//                'dateEnd' => $dateEnd,
//                'rows' => Report::getSmevConsumeStats($dateStart, $dateEnd),
//                'footer' => date('d-m-Y') . '  ' . $user->job . ' __________________  ' . $user->name
//            ];
//            $data['rows'][] = [
//                'type' => 'ИТОГО',
//                'cnt' => array_sum(array_map(function ($v) {
//                    return $v['cnt'];
//                }, $data['rows']))
//            ];
//            DocTemplate::get('smev_consumer.xlsx')
//                ->setHandler(function ($tbs) use ($data) {
//                    $tbs->MergeBlock('b', $data['rows']);
//                    $tbs->VarRef['footer'] = $data['footer'];
//                    $tbs->VarRef['dateStart'] = $data['dateStart'];
//                    $tbs->VarRef['dateEnd'] = $data['dateEnd'];
//                })
//                ->send('smev_consume_list.xlsx');
//        }
//        return $this->render('smev-consumer', [
//            'orders' => $orders,
//            'dateStart' => $dateStart,
//            'dateEnd' => $dateEnd
//        ]);
//    }
//
//    /**
//     * @return string
//     */
//    public function actionSmevProvider()
//    {
//        $this->view->title = 'Отчет2';
//        $this->view->params['breadcrumbs'][] = $this->view->title;
//        $this->view->params['tabMenu'] = $this->getMenu();
//
//        $req = Yii::$app->request;
//        $dateStart = $req->post('dateStart', date('d-m-Y', time() - 24 * 7 * 3600));
//        $dateEnd = $req->post('dateEnd', date('d-m-Y'));
//        $orders = null;
//        if ($req->post('html')) {
//            $orders = Report::getSmevProvideStats($dateStart, $dateEnd);
//        } elseif ($req->post('excel')) {
//            $user = Yii::$app->user->identity;
//            $data = [
//                'dateStart' => $dateStart,
//                'dateEnd' => $dateEnd,
//                'rows' => Report::getSmevProvideStats($dateStart, $dateEnd),
//                'footer' => date('d-m-Y') . '  ' . $user->job . ' __________________  ' . $user->name
//            ];
//            $data['rows'][] = [
//                'type' => 'ИТОГО',
//                'cnt' => array_sum(array_map(function ($v) {
//                    return $v['cnt'];
//                }, $data['rows']))
//            ];
//            DocTemplate::get('smev_provider.xlsx')
//                ->setHandler(function ($tbs) use ($data) {
//                    $tbs->MergeBlock('b', $data['rows']);
//                    $tbs->VarRef['footer'] = $data['footer'];
//                    $tbs->VarRef['dateStart'] = $data['dateStart'];
//                    $tbs->VarRef['dateEnd'] = $data['dateEnd'];
//                })
//                ->send('smev_provide_list.xlsx');
//        }
//        return $this->render('smev-provider', [
//            'orders' => $orders,
//            'dateStart' => $dateStart,
//            'dateEnd' => $dateEnd
//        ]);
//    }
//
//    protected function getMenu()
//    {
//        return [
//            [['report/smev-consumer'], 'Потребитель СМЭВ'],
//            [['report/smev-provider'], 'Обработка заявлений АИС/ЕПГУ'],
//        ];
//    }

}