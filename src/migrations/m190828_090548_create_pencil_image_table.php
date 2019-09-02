<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%gallery}}`.
 */
class m190828_090548_create_pencil_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pencil_image}}', [
            'id' => $this->primaryKey(),
            'group' => $this->string()->notNull(),
            'src' => $this->string()->notNull(),
            'alt' => $this->string(),
            'position'=> $this->integer()->notNull(),
        ]);

        $this->createIndex('group_index', 'pencil_image', 'group');
        $this->createIndex('position_index', 'pencil_image', 'position');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pencil_image}}');
    }
}
