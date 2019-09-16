<?php

use yii\db\Migration;

/**
 * Class m190916_063952_update_image_table
 */
class m190916_063952_update_pencil_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('pencil_image', 'src', 'full');
        $this->addColumn('pencil_image', 'mini', $this->string()->notNull()->after('full'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190916_063952_update_image_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190916_063952_update_image_table cannot be reverted.\n";

        return false;
    }
    */
}
