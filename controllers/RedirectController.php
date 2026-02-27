<?php

namespace app\controllers;

use app\models\ClickLog;
use app\models\Link;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RedirectController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionGo(string $code): \yii\web\Response
    {
        $link = Link::find()
            ->where(['short_code' => $code])
            ->one();

        if ($link === null) {
            throw new NotFoundHttpException('Ссылка не найдена');
        }

        $link->updateCounters(['clicks_count' => 1]);

        $log = new ClickLog();
        $log->link_id = $link->id;
        $log->ip_address = Yii::$app->request->userIP ?? '0.0.0.0';
        $log->user_agent = mb_substr(Yii::$app->request->userAgent ?? '', 0, 512);
        $log->referer = Yii::$app->request->referrer;
        $log->save(false);

        return $this->redirect($link->original_url, 302);
    }
}
