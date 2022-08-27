<?php

namespace backend\modules\queue\controllers;

use backend\modules\queue\models\QueueAllStudies;
use backend\modules\queue\models\QueueDesignatedPatient;
use backend\modules\queue\models\QueueNecessaryPass;
use backend\modules\queue\models\QueueStudiesNumbers;
use backend\modules\queue\queueInterface\QueueInt;
use backend\traits\CheckingDoctors;
use backend\traits\Debug;
use backend\traits\Queue;
use backend\traits\Slider;
use common\models\Doctors;
use common\models\Organization;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Default controller for the `queue` module
 */
class QueueController extends Controller implements QueueInt
{
    use CheckingDoctors, Slider, Debug;

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('/queue/index', [
            'models' => $model = new QueueDesignatedPatient(),
            'patients' => $model->getDesignatedPatient(),

        ]);
    }

    public function actionRecalculationDoctorsVisit($user_id)
    {
        $modelQueueDesignatedPatient = new QueueDesignatedPatient();
        $models = $this->findModel($user_id);
        $models->data_p = date('d.m.Y');
        $models->save(false);

        $data = $this->getArraySlider($user_id);

        if ($data) {
            try {
                $this->setDoctorsNeeded($user_id, $data['name_doctors_id']);
                $this->setDoctorsNeededResearch($user_id, $data['name_research_id']);
                $floor = $modelQueueDesignatedPatient->generateFloor();
                $idDesignatedPatient = $modelQueueDesignatedPatient->setQueueDesignatedPatient($user_id,
                    $floor);//ТУТ ID записи
                //ТУТ ДО этого все работает
                $modelQueueDesignatedPatient->setcabinetsDoctor($data['name_doctors_id'], $data['name_research_id'],
                    $idDesignatedPatient);
                Yii::$app->session->setFlash('success', "Сведение успешно обновлены, пациент добавлен в очередь");
                //$this->debug($arrayNumberDoctor);
            } catch (exception $e) {
                if ($idDesignatedPatient) {
                    QueueDesignatedPatient::findOne($idDesignatedPatient)->delete();
                }
                Yii::$app->session->setFlash('error', "Что то пошло не так");
                //return $this->redirect(['../../list-patients/view', 'id' => $user_id]);
            }
        } else {
            Yii::$app->session->setFlash('error', "Что то пошло не так");
        }

        return $this->redirect(['../../list-patients/view', 'id' => $user_id]);
    }

    public function actionPatientEscapeIndex($id)
    {
        $model = new QueueDesignatedPatient();
        $model->updatePatientEscape($id);
        return $this->redirect('/queue/queue/index');
    }

    public function actionPatientEscapeManagement($id)
    {
        $model = new QueueDesignatedPatient();
        $model->updatePatientEscape($id);
        return $this->redirect('/queue/queue/office-management');
    }

    public function actionOfficeManagement()
    {
        $model = new QueueDesignatedPatient();
        $patients = $model->getDesignatedPatientAll();
        $arrayCountCabinetNumbers = $model->setCountCabinetNumbers($patients);
        return $this->render('/queue/office-management', [
            'models' => $model,
            'patients' => $patients,
            'arrayAll' => $arrayCountCabinetNumbers['arrayAll'],//нагрузка кабинетов оставшихся
            'arrayRemainingToday' => $arrayCountCabinetNumbers['arrayRemainingToday'],//нагрузка кабинетов всех за сегодня
        ]);
    }

    public function actionSaveStatusNecessaryPass($idNeces)
    {
        $model = new QueueDesignatedPatient();
        $model->saveStatusNecessaryPass($idNeces);
        $patients = $model->getDesignatedPatientAll();
        $arrayCountCabinetNumbers = $model->setCountCabinetNumbers($patients);
        return $this->render('/queue/office-management', [
            'models' => $model,
            'patients' => $patients,
            'arrayAll' => $arrayCountCabinetNumbers['arrayAll'],//нагрузка кабинетов оставшихся
            'arrayRemainingToday' => $arrayCountCabinetNumbers['arrayRemainingToday'],//нагрузка кабинетов всех за сегодня
        ]);
    }

    public function actionReassignCabinet($idNeces, $idListStudies)
    {
        $this->layout = false;
        $model = QueueNecessaryPass::findOne($idNeces);
        $typeCabinet = ArrayHelper::map(QueueStudiesNumbers::find()->where(['list_studies_id' => $idListStudies])->all(), 'id', 'cabinet');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('/queue/reassign-cabinet', [
            'model' => $model,
            'typeCabinet' => $typeCabinet,
        ]);
    }

}
