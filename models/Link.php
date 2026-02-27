<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property int $clicks_count
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ClickLog[] $clickLogs
 */
class Link extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%link}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['original_url', 'short_code'], 'required'],
            ['original_url', 'string', 'max' => 2048],
            ['short_code', 'string', 'max' => 10],
            ['short_code', 'unique'],
            ['clicks_count', 'integer', 'min' => 0],
        ];
    }

    public function getClickLogs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ClickLog::class, ['link_id' => 'id']);
    }

    public function getShortUrl(): string
    {
        return Yii::$app->request->hostInfo . '/' . $this->short_code;
    }

    public static function generateShortCode(): string
    {
        $length = Yii::$app->params['shortCodeLength'] ?? 6;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($chars) - 1;

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, $max)];
            }
        } while (static::find()->where(['short_code' => $code])->exists());

        return $code;
    }
}
