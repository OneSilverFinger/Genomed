<?php

use yii\db\Migration;

class m241001_000001_create_link_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%link}}', [
            'id' => $this->primaryKey()->unsigned(),
            'original_url' => $this->string(2048)->notNull(),
            'short_code' => $this->string(10)->notNull(),
            'clicks_count' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->createIndex('idx_link_short_code', '{{%link}}', 'short_code', true);
        $this->createIndex('idx_link_created_at', '{{%link}}', 'created_at');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%link}}');
    }
}
