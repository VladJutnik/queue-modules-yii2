<?php

namespace backend\modules\queue\controllers;

use backend\modules\queue\models\QueueCabinetEmployment;
use backend\modules\queue\models\QueueCabinetNumbers;
use backend\modules\queue\models\QueueDesignatedPatient;
use backend\modules\queue\models\QueueDoctors;
use backend\modules\queue\models\QueueNecessaryPass;
use backend\modules\queue\models\QueueOnlineFloorOne;
use backend\modules\queue\models\QueueOnlineFloorThree;
use backend\modules\queue\models\QueueOnlineFloorTwo;
use backend\modules\queue\models\QueueStudiesNumbers;
use backend\modules\queue\queueInterface\ElectronicInt;
use backend\traits\CheckingDoctors;
use backend\traits\Debug;
use backend\traits\Queue;
use backend\traits\Slider;
use common\models\Doctors;
use common\models\Organization;
use Yii;
use yii\db\ActiveQuery;
use yii\web\Controller;

/**
 * Default controller for the `queue` module
 */

class ElectronicController extends Controller  implements ElectronicInt
{
    use Debug;

    public function actionBoardOne()
    {
        $model = new QueueDesignatedPatient();
        return $this->render('/electronic/board-one', [
            'time' => date('d.m.y H:i:s', strtotime("now + 4 hours")),
            'listPatientFloorAll' => $model->getDesignatedPatientParams(['date_appointments' => date('Y-m-d'), 'status' => QueueDesignatedPatient::STATUS_INPASSED]),
            'listPatientFloor' => $model->getDesignatedPatientParams(['date_appointments' => date('Y-m-d'), 'active_floor' => 1, 'status' => QueueDesignatedPatient::STATUS_INPASSED]),
        ]);
    }

    public function actionBoardTwo()
    {
        $model = new QueueDesignatedPatient();
        return $this->render('/electronic/board-two', [
            'time' => date('d.m.y H:i:s', strtotime("now + 4 hours")),
            'listPatientFloorAll' => $model->getDesignatedPatientParams(['date_appointments' => date('Y-m-d'), 'status' => QueueDesignatedPatient::STATUS_INPASSED]),
            'listPatientFloor' => $model->getDesignatedPatientParams(['date_appointments' => date('Y-m-d'), 'active_floor' => 2, 'status' => QueueDesignatedPatient::STATUS_INPASSED]),
        ]);
    }

    public function actionBoardThree()
    {
        $model = new QueueDesignatedPatient();
        return $this->render('/electronic/board-three', [
            'time' => date('d.m.y H:i:s', strtotime("now + 4 hours")),
            'listPatientFloorAll' => $model->getDesignatedPatientParams(['date_appointments' => date('Y-m-d'), 'status' => QueueDesignatedPatient::STATUS_INPASSED]),
            'listPatientFloor' => $model->getDesignatedPatientParams(['date_appointments' => date('Y-m-d'), 'active_floor' => 3, 'status' => QueueDesignatedPatient::STATUS_INPASSED]),
        ]);
    }

    public function actionPatientAppointment($params, $patient)
    {
        $model = QueueDesignatedPatient::findOne($params);
        $model->active_cabinet = Yii::$app->user->identity->fixed_cabinet;
        $model->save();
        return $this->redirect(['../../list-patients/view', 'id' => $patient]);
    }

    public function actionPatientNotCome($id)
    {
        if ($this->deletingActiveCabinet($id) === 'ok') {
            Yii::$app->session->setFlash('success', "Пациент останеться в Ваше списке, Вы можете вызвать его позже!");
        } else {
            Yii::$app->session->setFlash('error', "Что то пошло не так!");
        }
        return $this->redirect(['../../site/electronic-queue']);
    }

    //если пациент завершил прием
    //ПОСМОТРЕТЬ МБ Объеденить таблицы queue_doctors с queue_research
    public function actionPatientCompleted()
    {
        $post = Yii::$app->request->post();
        //[userId] => 14690  [idDesignatedPatient] => 31
        $modelDesignatedPatient = QueueDesignatedPatient::findOne($post['idDesignatedPatient']);
        //Удалить у пациента активный кабинет
        $modelDesignatedPatient->active_cabinet = '';
        //Изменить статус пройденого кабинета
        $idCabinets = QueueStudiesNumbers::find()->select(['id'])->where(['cabinet' => Yii::$app->user->identity->fixed_cabinet])->all();
        $idCabinetArray = [];
        foreach ($idCabinets as $idCabinet){
            $idCabinetArray[] = $idCabinet['id'];
        }
        QueueNecessaryPass::updateAll(['status' => QueueNecessaryPass::STATUS_PASSED], ['and', ['designated_patient_id' => $post['idDesignatedPatient']], ['studies_numbers_id' => $idCabinetArray]]);
        //Посмотреть остались ли еще на этаже кабинеты
        $listCabinets = QueueNecessaryPass::find()
            ->with('studiesNumbers')
            ->joinWith([
                'studiesNumbers' => function (Activequery $query) {
                    $query->from(['cabinet' => QueueStudiesNumbers::tableName()]); // назначается псевдоним таблицы
                },
            ])
            ->where([
                'designated_patient_id' => $post['idDesignatedPatient'],
                'status' => 1,
            ])
            ->all();
        $florCountPatient = [];
        foreach ($listCabinets as $one) {
            switch ($one['studiesNumbers']['floor']) {
                case 1:
                    $florCountPatient[1] += 1;
                    break;
                case 2:
                    $florCountPatient[2] += 1;
                    break;
                case 3:
                    $florCountPatient[3] += 1;
                    break;
            }
        }
        //Если не осталось то пересчитать этаж хз как пока
        if (!$florCountPatient[$modelDesignatedPatient->active_floor]) {

            $florCount = [
                1 => 0,
                2 => 0,
                3 => 0,
            ]; // тут лежит количество людей сейчас на этажах
            // $florCountPatient тут колчисетво оставшихся кабинетов по этажам
            $countFloorDayAll = $modelDesignatedPatient->countFloorDayAll(date('Y-m-d'));
            foreach ($countFloorDayAll as $one) {

                switch ($one['floor']) {
                    case 1:
                        $florCount[1] += $one['countFloor'];
                        break;
                    case 2:
                        $florCount[2] += $one['countFloor'];
                        break;
                    case 3:
                        $florCount[3] += $one['countFloor'];
                        break;
                }
            }
            switch ($modelDesignatedPatient->active_floor) {
                case 1:
                    $modelDesignatedPatient->end_floor1 = 'пройден';
                    break;
                case 2:
                    $modelDesignatedPatient->end_floor2 = 'пройден';
                    break;
                case 3:
                    $modelDesignatedPatient->end_floor3 = 'пройден';
                    break;
            }
            if($florCountPatient !== []){
                asort($florCount,SORT_NUMERIC);
                foreach ($florCount as $key => $one2){
                    if($florCountPatient[$key]){
                        $modelDesignatedPatient->active_floor = $key;
                    }
                }
            } else {
                $modelDesignatedPatient->active_floor = '';
                $modelDesignatedPatient->status = QueueDesignatedPatient::STATUS_PASSED;
            }
        }
        $modelDesignatedPatient->save(false);

        return $this->redirect('../../site/electronic-queue');
    }

    public function deletingActiveCabinet($id)
    {
        $model = QueueDesignatedPatient::findOne($id);
        $model->active_cabinet = '';
        return ($model->save()) ? 'ok' : 'no';
    }
}