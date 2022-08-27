<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue_designated_patient}}`.
 */
class m220808_080707_create_queue_designated_patient_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%queue_designated_patient}}', [
            'id' => $this->primaryKey(),
            'patient_id' => $this->integer()->notNull()->comment('пациент'),
            'active_floor' => $this->string()->comment('активный этаж'),
            'active_cabinet' => $this->string()->comment('активный кабинет'),
            'date_appointments' => $this->date()->comment('дата записи в очереди'),
            'time' => $this->time()->comment('время'),
            'end_floor1' => $this->string()->comment('закончил этаж 1'),
            'end_floor2' => $this->string()->comment('закончил этаж 2'),
            'end_floor3' => $this->string()->comment('закончил этаж 3'),
            'status' => $this->integer()->defaultValue(1)
        ]);
        // creates index for column `patient_id`
        $this->createIndex(
            'idx-designated_patient-patient_id',
            '{{%queue_designated_patient}}',
            'patient_id'
        );
        // add foreign key for table `list_patients`
        $this->addForeignKey(
            'fk-designated_patient-patient_id',
            '{{%queue_designated_patient}}',
            'patient_id',
            'list_patients',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue_designated_patient}}');
    }
}
