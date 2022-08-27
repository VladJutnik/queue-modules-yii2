<?php

namespace backend\modules\queue\models;

use Yii;

/**
 * This is the model class for table "queue_studies_numbers".
 *
 * @property int $id
 * @property int $list_studies_id какое исследование
 * @property string|null $cabinet кабинет
 * @property string|null $floor этаж
 *
 * @property QueueAllStudies $listStudies
 * @property QueueNecessaryPass[] $queueNecessaryPasses
 */
class QueueStudiesNumbers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'queue_studies_numbers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['list_studies_id'], 'required'],
            [['list_studies_id'], 'integer'],
            [['cabinet', 'floor'], 'string', 'max' => 255],
            [['list_studies_id'], 'exist', 'skipOnError' => true, 'targetClass' => QueueAllStudies::className(), 'targetAttribute' => ['list_studies_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'list_studies_id' => 'List Studies ID',
            'cabinet' => 'Cabinet',
            'floor' => 'Floor',
        ];
    }

    /**
     * Gets query for [[ListStudies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getListStudies()
    {
        return $this->hasOne(QueueAllStudies::className(), ['id' => 'list_studies_id']);
    }

    /**
     * Gets query for [[QueueNecessaryPasses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQueueNecessaryPasses()
    {
        return $this->hasMany(QueueNecessaryPass::className(), ['studies_numbers_id' => 'id']);
    }
}
