<?php

namespace backend\modules\queue\models;

use Yii;

/**
 * This is the model class for table "queue_necessary_pass".
 *
 * @property int $id
 * @property int $designated_patient_id пациент
 * @property int $studies_numbers_id кабинет
 * @property int|null $status
 *
 * @property QueueDesignatedPatient $designatedPatient
 * @property QueueStudiesNumbers $studiesNumbers
 */
class QueueNecessaryPass extends \yii\db\ActiveRecord
{
    const STATUS_PASSED = 0;
    const STATUS_INPASSED = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'queue_necessary_pass';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['designated_patient_id', 'studies_numbers_id'], 'required'],
            [['designated_patient_id', 'studies_numbers_id', 'status'], 'integer'],
            [['designated_patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => QueueDesignatedPatient::className(), 'targetAttribute' => ['designated_patient_id' => 'id']],
            [['studies_numbers_id'], 'exist', 'skipOnError' => true, 'targetClass' => QueueStudiesNumbers::className(), 'targetAttribute' => ['studies_numbers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'designated_patient_id' => 'Designated Patient ID',
            'studies_numbers_id' => 'Выбор кабинета: ',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[DesignatedPatient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDesignatedPatient()
    {
        return $this->hasOne(QueueDesignatedPatient::className(), ['id' => 'designated_patient_id']);
    }

    /**
     * Gets query for [[StudiesNumbers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudiesNumbers()
    {
        return $this->hasOne(QueueStudiesNumbers::className(), ['id' => 'studies_numbers_id']);
    }
}
