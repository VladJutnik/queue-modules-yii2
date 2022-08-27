<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пациенты в списке для электронной очереди';
/*print_r('<pre>');
print_r($patients);
print_r('</pre> <br>');
print_r($models->attributeLabels()['id']);
print_r('</pre>');*/
?>
<?php
Pjax::begin(); ?>
<?= Html::a(
    'Обновить',
    ['/queue'],
    ['class' => 'btn btn-sm btn-primary d-none', 'id' => 'refreshButton']
) ?>
    <div class="container">
        <h1 class="text-center"><?= $this->title ?></h1>
        <?
        if (!$patients) { ?>

            <div class="alert alert-danger text-center" role="alert">
                Активных пациентов для эл. очерди нет
            </div>
            <?
        } ?>
    </div>
    <div class="contaner-fluid p-2">
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="thead-light ">
                <tr>
                    <th class="text-center"><?= $models->attributeLabels()['patient_id'] ?></th>
                    <th class="text-center"><?= $models->attributeLabels()['active_floor'] ?></th>
                    <th class="text-center"><?= $models->attributeLabels()['active_cabinet'] ?></th>
                    <th class="text-center"><?= $models->attributeLabels()['time'] ?></th>
                    <th class="text-center"><?= $models->attributeLabels()['end_floor1'] ?></th>
                    <th class="text-center"><?= $models->attributeLabels()['end_floor2'] ?></th>
                    <th class="text-center"><?= $models->attributeLabels()['end_floor3'] ?></th>
                    <th class="text-center"><?= $models->attributeLabels()['status'] ?></th>
                    <th class="text-center">Управление</th>
                </tr>
                </thead>
                <tbody>
                <?
                if ($patients) {
                    foreach ($patients as $patient) { ?>
                        <tr>
                            <td><?= $patient['patient']['fio'] ?></td>
                            <td class="text-center"><?= $patient['active_floor'] ?></td>
                            <td class="text-center"><?= $patient['active_cabinet'] ?></td>
                            <td><?= $patient['time'] ?></td>
                            <td><?= $patient['end_floor1'] ?></td>
                            <td><?= $patient['end_floor2'] ?></td>
                            <td><?= $patient['end_floor3'] ?></td>
                            <td><?= $patient['status'] ?></td>
                            <td>
                                <? if ($patient['status'] === $models::STATUS_INPASSED) { ?>
                                    <?= Html::a('<b>Убрать из списка</b>',
                                        ['/queue/queue/patient-escape?id=' . $patient['id']],
                                        [
                                            'class' => 'btn btn-sm btn-outline-danger btn-block'
                                        ]) ?>
                                <?
                                } ?>

                            </td>
                        </tr>
                        <?
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
Pjax::end(); ?>
<?php
$script = <<< JS
$(document).ready(function() {
    setInterval(function(){
        $('#refreshButton').click();
    }, 1000);
    //$('#refreshButton').hide()
});
JS;
$this->registerJs($script);
?>