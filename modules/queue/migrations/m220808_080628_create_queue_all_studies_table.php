<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue_all_studies}}`.
 */
class m220808_080628_create_queue_all_studies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%queue_all_studies}}', [
            'id' => $this->primaryKey(),
            'list_studies' => $this->string()->comment('список исследований'),
            'status' => $this->integer()->defaultValue(1)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue_all_studies}}');
    }
}
