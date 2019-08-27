<?php

use yii\db\Migration;

/**
 * Создание таблицы 'pencil'. Для работы модуля необходимо наличие таблицы 'category', которая хранит в себе
 * данные по страницам, в которых будет использоваться данный модуль.
 */
class m190826_064420_create_pencil_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pencil}}', [
            'id_name' => $this->string(60)->notNull(),
            'category_id' => $this->integer()->notNull(),
            'text' => $this->text(),
        ]);

        $this->addPrimaryKey('primary_id_name', 'pencil', 'id_name');

        $this->addForeignKey(
            'FK_pencil_category',
            'pencil',
            'category_id',
            'category',
            'id',
            'cascade',
            'cascade'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pencil}}');
    }
}
