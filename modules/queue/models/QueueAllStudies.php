<?php

namespace backend\modules\queue\models;

use Yii;

/**
 * This is the model class for table "queue_all_studies".
 *
 * @property int $id
 * @property string|null $list_studies список исследований
 * @property int|null $status
 *
 * @property QueueStudiesNumbers[] $queueStudiesNumbers
 */
class QueueAllStudies extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'queue_all_studies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['list_studies'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'list_studies' => 'List Studies',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[QueueStudiesNumbers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQueueStudiesNumbers()
    {
        return $this->hasMany(QueueStudiesNumbers::className(), ['list_studies_id' => 'id']);
    }
}
