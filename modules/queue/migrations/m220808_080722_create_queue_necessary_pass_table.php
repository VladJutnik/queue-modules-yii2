<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue_necessary_pass}}`.
 */
class m220808_080722_create_queue_necessary_pass_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%queue_necessary_pass}}', [
            'id' => $this->primaryKey(),
            'designated_patient_id' => $this->integer()->notNull()->comment('пациент'),
            'studies_numbers_id' => $this->integer()->notNull()->comment('кабинет'),
            'status' => $this->integer()->defaultValue(1)
        ]);
        // creates index for column `patient_id`
        $this->createIndex(
            'idx-necessary_pass-designated_patient_id',
            '{{%queue_necessary_pass}}',
            'designated_patient_id'
        );
        // add foreign key for table `list_patients`
        $this->addForeignKey(
            'fk-necessary_pass-designated_patient_id',
            '{{%queue_necessary_pass}}',
            'designated_patient_id',
            'queue_designated_patient',
            'id',
            'CASCADE'
        );
        // creates index for column `patient_id`
        $this->createIndex(
            'idx-necessary_pass-studies_numbers_id',
            '{{%queue_necessary_pass}}',
            'designated_patient_id'
        );
        // add foreign key for table `list_patients`
        $this->addForeignKey(
            'fk-necessary_pass-studies_numbers_id',
            '{{%queue_necessary_pass}}',
            'studies_numbers_id',
            'queue_studies_numbers',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue_necessary_pass}}');
    }
}
