<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue_studies_numbers}}`.
 */
class m220808_080651_create_queue_studies_numbers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%queue_studies_numbers}}', [
            'id' => $this->primaryKey(),
            'list_studies_id' => $this->integer()->notNull()->comment('какое исследование'),
            'cabinet' => $this->string()->comment('кабинет'),
            'floor' => $this->string()->comment('этаж'),
        ]);
        // creates index for column `patient_id`
        $this->createIndex(
            'idx-studies_numbers-list_studies_id',
            '{{%queue_studies_numbers}}',
            'list_studies_id'
        );
        // add foreign key for table `list_patients`
        $this->addForeignKey(
            'fk-studies_numbers-list_studies_id',
            '{{%queue_studies_numbers}}',
            'list_studies_id',
            'queue_all_studies',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue_studies_numbers}}');
    }
}
