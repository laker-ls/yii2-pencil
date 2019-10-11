<?php

use yii\db\Migration;

/**
 * Class m191011_082701_update_pencil_text_table
 */
class m191011_082701_update_pencil_text_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('pencil_text', 'category_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191011_082701_update_pencil_text_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191011_082701_update_pencil_text_table cannot be reverted.\n";

        return false;
    }
    */
}
