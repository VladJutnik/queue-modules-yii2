<?php

namespace common\widgets\queue;

use backend\modules\queue\models\QueueCabinetEmployment;
use backend\modules\queue\models\QueueCabinetNumbers;
use backend\modules\queue\models\QueueDesignatedPatient;
use backend\modules\queue\models\QueueStudiesNumbers;
use Yii;
use yii\base\Widget;

/*use backend\assets\WidgetsUpAsset;*/

use yii\bootstrap4\Html;
use yii\db\ActiveQuery;

class QueueWidget extends Widget
{
    public $status = 'Hello World';

    public function init()
    {
        parent::init();
        //$this->fff();
    }

    public function run()
    {

        $paitent = $this->patient();

        return $this->render('index', [
            'message' => $this->status,
            'paitent' => $paitent,
        ]);
    }

    public function patient()
    {
        $listPatientFloor =
            QueueDesignatedPatient::find()
                ->with('patient')
                ->where(['date_appointments' => date('Y-m-d'), 'active_cabinet' => Yii::$app->user->identity->fixed_cabinet])
                ->asArray()
                ->one();

        return $listPatientFloor;
    }
}