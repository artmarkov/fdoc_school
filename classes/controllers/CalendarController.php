<?php

namespace main\controllers;

use Yii;
use main\models\Calendar;

class CalendarController extends BaseController
{

    /**
     * Список пользователей
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Производственный календарь';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('index.php');
    }

    public function actionMetadata()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'max' => Calendar::getMax(),
            'min' => Calendar::getMin(),
            'weekend_exceptions' => Calendar::getHolidayExceptions(),
            'weekday_exceptions' => Calendar::getWeekdayExceptions()
        ];
        return $result;
    }

    public function actionMarkday()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $day = Yii::$app->request->post('day');
        $flag = Yii::$app->request->post('flag');
        Calendar::markDay(date('Y-m-d',$day/1000), $flag);
        return ['success' => true];
    }

}
