<?php

use yii\db\Migration;

/**
 * Создание таблицы 'pencil'. Для работы модуля необходимо наличие таблицы 'category', которая хранит в себе
 * данные по страницам, в которых будет использоваться данный модуль.
 */
class m190826_064420_create_pencil_text_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pencil_text}}', [
            'id_name' => $this->string(60)->notNull(),
            'category_id' => $this->integer()->notNull(),
            'text' => $this->text(),
        ]);

        $this->addPrimaryKey('primary_id_name', 'pencil_text', 'id_name');
        $this->createIndex('index_category_id', 'pencil_text', 'category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pencil_text}}');
    }
}
