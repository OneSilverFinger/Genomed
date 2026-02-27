<?php

namespace app\controllers;

use app\models\ShortenForm;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionShorten(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $form = new ShortenForm();
        $form->url = Yii::$app->request->post('url');

        $link = $form->shorten();

        if ($link === null) {
            return [
                'success' => false,
                'errors' => $form->getFirstErrors(),
            ];
        }

        $shortUrl = $link->getShortUrl();

        return [
            'success' => true,
            'short_url' => $shortUrl,
            'qr_code' => $this->generateQrCode($shortUrl),
        ];
    }

    private function generateQrCode(string $url): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_H,
            'scale' => 10,
            'imageBase64' => true,
        ]);

        return (new QRCode($options))->render($url);
    }
}
