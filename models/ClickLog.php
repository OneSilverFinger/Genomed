<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int $id
 * @property int $link_id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property string|null $referer
 * @property string $created_at
 *
 * @property Link $link
 */
class ClickLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%click_log}}';
    }

    public function rules(): array
    {
        return [
            [['link_id', 'ip_address'], 'required'],
            ['link_id', 'integer'],
            ['ip_address', 'string', 'max' => 45],
            ['user_agent', 'string', 'max' => 512],
            ['referer', 'string', 'max' => 2048],
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->created_at = new Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function getLink(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Link::class, ['id' => 'link_id']);
    }
}
