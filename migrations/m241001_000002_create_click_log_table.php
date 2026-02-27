<?php

use yii\db\Migration;

class m241001_000002_create_click_log_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%click_log}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'link_id' => $this->integer()->unsigned()->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'user_agent' => $this->string(512)->null(),
            'referer' => $this->string(2048)->null(),
            'created_at' => $this->dateTime()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->createIndex('idx_click_log_link_id', '{{%click_log}}', 'link_id');
        $this->createIndex('idx_click_log_created_at', '{{%click_log}}', 'created_at');

        $this->addForeignKey(
            'fk_click_log_link',
            '{{%click_log}}',
            'link_id',
            '{{%link}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_click_log_link', '{{%click_log}}');
        $this->dropTable('{{%click_log}}');
    }
}
