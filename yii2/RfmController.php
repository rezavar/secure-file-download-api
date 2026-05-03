<?php

namespace frontend\controllers\report\create;

use frontend\models\RfmData;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;

class RfmController extends Controller
{
    public function beforeAction($action)
    {
        if ($action->id == "exportirx4fghghjgh6h87eriu") {
            $this->enableCsrfValidation = false;
            if(!$this->checkValidIp()){
                throw new ForbiddenHttpException('invalid ips');
            }
        }
        return parent::beforeAction($action);
    }

    public function actionexportirx4fghghjgh6h87eriu()
    {
        $post = Yii::$app->request->post();
        if(empty($post)) {
            throw new MethodNotAllowedHttpException('invalid method');
        }
        $rfmData = new RfmData();
        $rfmData->load($post,'');
        if(!$rfmData->validate()){
            $errors = $rfmData->getErrorSummary(true);
            $errors = implode("\n", $errors);
            throw new NotAcceptableHttpException($errors);
        }
        $rfmData->getCsvExport();
    }

    private function checkValidIp()
    {
        $whiteListIps = [
            "192.168.3.26",
            "200.50.54.14",
        ];
        $userIp = Yii::$app->request->userIP;
        return in_array($userIp, $whiteListIps);
    }

    

}
