<?php

namespace backend\modules\queue\models;

use backend\traits\Debug;
use common\models\ListPatients;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "queue_designated_patient".
 *
 * @property int $id
 * @property int $patient_id пациент
 * @property string|null $active_floor активный этаж
 * @property string|null $active_cabinet активный кабинет
 * @property string|null $date_appointments дата записи в очереди
 * @property string|null $time время
 * @property string|null $end_floor1 закончил этаж 1
 * @property string|null $end_floor2 закончил этаж 2
 * @property string|null $end_floor3 закончил этаж 3
 * @property int|null $status
 *
 * @property ListPatients $patient
 * @property QueueNecessaryPass[] $queueNecessaryPasses
 */
class QueueDesignatedPatient extends \yii\db\ActiveRecord
{
    use Debug;

    const STATUS_PASSED = 0;
    const STATUS_INPASSED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'queue_designated_patient';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_id'], 'required'],
            [['patient_id', 'status'], 'integer'],
            [['date_appointments', 'time'], 'safe'],
            [['active_floor', 'active_cabinet', 'end_floor1', 'end_floor2', 'end_floor3'], 'string', 'max' => 255],
            [
                ['patient_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ListPatients::className(),
                'targetAttribute' => ['patient_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'patient_id' => 'Пациент',
            'active_floor' => 'Сейчас на этаже',
            'active_cabinet' => 'Сейчас в кабинете',
            'date_appointments' => '',
            'time' => 'Время прихода',
            'end_floor1' => '1 этаж',
            'end_floor2' => '2 этаж',
            'end_floor3' => '3 этаж',
            'status' => 'Активен',
        ];
    }

    /**
     * Gets query for [[Patient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(ListPatients::className(), ['id' => 'patient_id']);
    }

    /**
     * Gets query for [[QueueNecessaryPasses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQueueNecessaryPasses()
    {
        return $this->hasMany(QueueNecessaryPass::className(), ['designated_patient_id' => 'id']);
    }

    public function generateFloor()
    {
        $floor = [
            1 => $this->countFloorDay(date('Y-m-d'), 1),
            2 => $this->countFloorDay(date('Y-m-d'), 2),
            3 => $this->countFloorDay(date('Y-m-d'), 3),
        ];

        return $this->arrayKeysMin($floor);
    }

    public function countFloorDay($day, $floor)
    {
        return (new \yii\db\Query())
            ->select([
                'countFloor' => 'count(*)',
            ])
            ->from('queue_designated_patient')
            ->where([
                'date_appointments' => $day,
                'active_floor' => $floor,
            ])
            ->groupBy(['active_floor'])
            ->one()['countFloor'];
    }

    public function countFloorDayAll($day)
    {
        return (new \yii\db\Query())
            ->select([
                'floor' => 'active_floor',
                'countFloor' => 'count(*)',
            ])
            ->from('queue_designated_patient')
            ->where([
                'date_appointments' => $day,
            ])
            ->groupBy(['active_floor'])
            ->all();
    }

    public function setcabinetsDoctor($dataDoctors, $dataResearch, $user_id)
    {
        $this->listQueueAllStudies($dataDoctors, $user_id);
        $this->listQueueAllStudies($dataResearch, $user_id);

        return 'ok';
    }

    public function listQueueAllStudies($array, $user_id)
    {
        foreach (
            QueueAllStudies::find()
                ->where([
                    'list_studies' => $array,
                    'status' => QueueAllStudies::STATUS_ACTIVE,
                ])
                ->with('queueStudiesNumbers')
                ->all()
            as $studies
        ) {
            $this->saveQueueNecessaryPass($studies['queueStudiesNumbers'], $user_id);
        }
        return 'ok';
    }

    public function saveQueueNecessaryPass($array, $user_id)
    {
        $cabinetId = 0;
        foreach ($array as $cabinet) {
            $queueNecessaryPass = QueueNecessaryPass::find()
                ->with('studiesNumbers')
                ->joinWith([
                    'studiesNumbers' => function (Activequery $query) {
                        $query->from(['cabinet' => QueueStudiesNumbers::tableName()]); // назначается псевдоним таблицы
                    },
                ])
                ->where(['designated_patient_id' => $user_id, 'cabinet.cabinet' => $cabinet['cabinet']])
                ->count();
            if($queueNecessaryPass != 0){
                $cabinetId = $cabinet['id'];
            }

        }
        $floor = [];
        if ($cabinetId === 0) {
            foreach ($array as $cabinet) {
                //1 вариант дописать тут
                $countCabinet =
                    QueueDesignatedPatient::find()
                        ->with('queueNecessaryPasses')
                        ->joinWith([
                            'queueNecessaryPasses' => function (Activequery $query) {
                                $query->from(['cabinet' => QueueNecessaryPass::tableName()]); // назначается псевдоним таблицы
                            },
                        ])
                        ->with('queueNecessaryPasses.studiesNumbers')
                        ->joinWith([
                            'queueNecessaryPasses.studiesNumbers' => function (Activequery $query) {
                                $query->from(['cabinetName' => QueueStudiesNumbers::tableName()]); // назначается псевдоним таблицы
                            },
                        ])
                        ->where(['date_appointments' => date('Y-m-d'), 'cabinet.studies_numbers_id' => $cabinet['id'], 'cabinetName.cabinet' => $cabinet['cabinet']])
                        ->asArray()
                        ->count();
                $floor[$cabinet['id']] = $countCabinet;
            }
            //$this->debug($ca);
            $modelResearchCabinet = new QueueNecessaryPass();
            $modelResearchCabinet->designated_patient_id = $user_id;
            //2 вариант дописать тут
            $modelResearchCabinet->studies_numbers_id = $this->arrayKeysMin($floor);
            $modelResearchCabinet->save();
        } else {
            $modelResearchCabinet = new QueueNecessaryPass();
            $modelResearchCabinet->designated_patient_id = $user_id;
            //2 вариант дописать тут
            $modelResearchCabinet->studies_numbers_id = $cabinetId;
            $modelResearchCabinet->save();
        }

        return 'ok';
    }

    public function arrayKeysMin($array)
    {
        return array_keys($array, min($array))[0];
    }

    public function setQueueDesignatedPatient($user_id, $floor)
    {
        $model = new QueueDesignatedPatient();
        $model->patient_id = $user_id;
        $model->active_floor = $floor;
        $model->date_appointments = date('Y-m-d');
        $model->time = date('H:i');
        $model->end_floor1 = 'не пройден';
        $model->end_floor2 = 'не пройден';
        $model->end_floor3 = 'не пройден';
        $model->save(false);

        return $model->id;
    }

    public function getDesignatedPatient()
    {
        return QueueDesignatedPatient::find()->where(['date_appointments' => date('Y-m-d')])->orderBy([
            'date_appointments' => SORT_DESC,
            'time' => SORT_DESC,
        ])->with('patient')->all();
    }

    public function getDesignatedPatientAll()
    {
        return QueueDesignatedPatient::find()->where(['date_appointments' => date('Y-m-d')])->orderBy([
            'date_appointments' => SORT_DESC,
            'time' => SORT_DESC,
        ])->with('patient')->with('queueNecessaryPasses.studiesNumbers.listStudies')->asArray()->all();
    }

    public function getDesignatedPatientParams($params)
    {
        return QueueDesignatedPatient::find()
            ->where($params)
            ->with('patient')
            ->asArray()
            ->all();
    }

    public function setCountCabinetNumbers($patients)
    {
        $result = [];
        foreach ($patients as $patient) {
            foreach ($patient['queueNecessaryPasses'] as $one) {
                if ($one['status'] == 1) {
                    $result['arrayAll'][$one['studiesNumbers']['cabinet']] += 1;//нагрузка кабинетов оставшихся
                }
                $result['arrayRemainingToday'][$one['studiesNumbers']['cabinet']] += 1;//нагрузка кабинетов всех за сегодня
            }
        }
        return $result;
    }

    public function saveStatusNecessaryPass($id)
    {
        $model = QueueNecessaryPass::findOne($id);
        $model->status = QueueNecessaryPass::STATUS_PASSED;
        return $model->save();
    }

    public function updatePatientEscape($id)
    {
        return QueueDesignatedPatient::updateAll(['status' => QueueDesignatedPatient::STATUS_PASSED], ['id' => $id]);
    }
}
