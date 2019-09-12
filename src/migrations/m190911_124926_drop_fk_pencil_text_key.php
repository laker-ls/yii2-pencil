<?php

use yii\db\Migration;

/**
 * Class m190911_124926_delete_fk_key
 */
class m190911_124926_drop_fk_pencil_text_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK_pencil_text_category', 'pencil_text');
        $this->alterColumn('pencil_text', 'category_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190911_124926_delete_fk_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190911_124926_delete_fk_key cannot be reverted.\n";

        return false;
    }
    */
}
