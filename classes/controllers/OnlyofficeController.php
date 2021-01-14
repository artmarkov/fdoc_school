<?php

namespace main\controllers;

use Yii;
use yii\helpers\Url;
use main\acl\AccessControl;

class OnlyofficeController extends BaseController
{
    public $layout = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['save', 'load'], // доступ для неавторизованных пользователей (onlyoffice)
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($id, $method)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            return [
                'result' => Yii::$app->onlyoffice->{$method}($id),
                'success' => true
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'success' => false
            ];
        }
    }

    /**
     * @param $query
     * @return string
     */
    public function actionEdit($query)
    {
        if (isset($_SERVER['HTTPS'])) {
            // onlyoffice работает по http, конвертим https в http иначе ошибка blocked:mixed-content
            return $this->redirect(Url::current([],'http'));
        }
        $f = json_decode(base64_decode($query));
        return $this->render('edit', [
            'serviceUrl' => Yii::$app->onlyoffice->url,
            'key' => substr(md5($f->id . time()), 0, 20),
            'fileName' => $f->name,
            'downloadUrl' => Url::to(['onlyoffice/load/', 'id' => $f->id], true),
            'saveUrl' => Url::to(['onlyoffice/save/', 'id' => $f->id], true),
        ]);
    }

    /**
     * Метод для выгрузки файла в onlyoffice
     * @param string $id id файла
     * @return string
     */
    public function actionLoad($id)
    {
        Yii::$app->response->sendFile(Yii::$app->onlyoffice->getFilePath($id))->send();
        exit;
    }

    /**
     * Метод для сохранения файла из onlyoffice
     * @param string $id id файла
     * @return array
     */
    public function actionSave($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        if ($data['status'] == 2) {
            $downloadUri = $data['url'];
            if (($content = file_get_contents($downloadUri)) === false) {
                throw new \RuntimeException('Bad Response');
            }
            $filePath = Yii::$app->onlyoffice->getFilePath($id, '.new');
            file_put_contents($filePath, $content, LOCK_EX);
        }
        return ['error' => 0];
    }

}
