<?php

namespace app\models;

use yii\base\Model;

class ShortenForm extends Model
{
    public ?string $url = null;

    public function rules(): array
    {
        return [
            ['url', 'required', 'message' => 'Введите URL'],
            ['url', 'trim'],
            ['url', 'url',
                'validSchemes' => ['http', 'https'],
                'defaultScheme' => 'https',
                'message' => 'Некорректный формат URL (допустимы http:// и https://)',
            ],
            ['url', 'string', 'max' => 2048],
            ['url', 'validateAccessibility'],
        ];
    }

    public function validateAccessibility(string $attribute): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        $accessible = $this->checkUrlWithMethod($this->$attribute, true);

        if (!$accessible) {
            $accessible = $this->checkUrlWithMethod($this->$attribute, false);
        }

        if (!$accessible) {
            $this->addError($attribute, 'Данный URL не доступен');
        }
    }

    private function checkUrlWithMethod(string $url, bool $headOnly): bool
    {
        $ch = curl_init($url);

        $options = [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; URLChecker/1.0)',
        ];

        if ($headOnly) {
            $options[CURLOPT_NOBODY] = true;
        } else {
            $options[CURLOPT_RANGE] = '0-1024';
        }

        curl_setopt_array($ch, $options);
        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_errno($ch);
        curl_close($ch);

        return !$error && $httpCode > 0 && $httpCode < 400;
    }

    public function shorten(): ?Link
    {
        if (!$this->validate()) {
            return null;
        }

        $link = Link::findOne(['original_url' => $this->url]);
        if ($link !== null) {
            return $link;
        }

        $link = new Link();
        $link->original_url = $this->url;
        $link->short_code = Link::generateShortCode();

        return $link->save() ? $link : null;
    }
}
