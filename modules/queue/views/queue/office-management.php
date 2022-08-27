<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Изменения кабинета у пациента: ';
/*print_r('<pre>');
print_r($patients);
print_r('</pre> <br>');
print_r($models->attributeLabels()['id']);
print_r('</pre>');*/
?>

<?= Html::a(
    'Обновить',
    ['/queue/queue/office-management'],
    ['class' => 'btn btn-sm btn-primary d-none', 'id' => 'refreshButton']
) ?>
<?php Pjax::begin(['enablePushState' => false]); ?>
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
    <div class="contaner-fluid p-1">
        <div class="m-2">
            <div class="row">
                <div class="clo-3 ml-3">
                    <h5>Нагрузка по кабинетам (Оставшиеся количество на сегодня):</h5>
                    <?
                    if ($patients) {
                        ksort($arrayAll,SORT_NUMERIC);
                        foreach ($arrayAll as $key => $one) { ?>
                            <b><?=$key?>: </b> <?=$one?> <br>
                            <?
                        }
                    } ?>
                </div>
                <div class="clo-3 ml-3">
                    <h5>Нагрузка по кабинетам (Все за сегодня):</h5>
                    <?
                    if ($patients) {
                        ksort($arrayRemainingToday,SORT_NUMERIC);
                        foreach ($arrayRemainingToday as $key => $one) { ?>
                            <b><?=$key?>: </b> <?=$one?> <br>
                            <?
                        }
                    } ?>
                </div>
                <div class="clo-3"></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="thead-light ">
                <tr>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['patient_id'] ?></th>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['active_floor'] ?></th>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['active_cabinet'] ?></th>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['time'] ?></th>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['end_floor1'] ?></th>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['end_floor2'] ?></th>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['end_floor3'] ?></th>
                    <th rowspan="2" class="text-center"><?= $models->attributeLabels()['status'] ?></th>
                    <th rowspan="2" class="text-center">Управление</th>
                    <th colspan="4" class="text-center">Управление исследованиями</th>
                </tr>
                <tr class="thead-light">
                    <td>Исследование</td>
                    <td>Назначенный кабинет</td>
                    <td>Статус</td>
                    <td>Управление</td>
                </tr>
                </thead>

                <tbody>
                <?
                if ($patients) {
                    foreach ($patients as $patient) {
                        //print_r($models::STATUS_INPASSED);
                        //print_r($models);
                        //print_r('<pre>');
                        //print_r($patient);
                        //print_r('<pre>');
                        $count = count($patient['queueNecessaryPasses']);
                        ?>
                        <tr>
                            <td rowspan="<?= ++$count ?>"><?= $patient['patient']['fio'] ?></td>
                            <td rowspan="<?= $count ?>"
                                class="text-center"><?= $patient['active_floor'] ?></td>
                            <td rowspan="<?= $count ?>"
                                class="text-center"><?= $patient['active_cabinet'] ?></td>
                            <td rowspan="<?= $count ?>"><?= $patient['time'] ?></td>
                            <td rowspan="<?= $count ?>"><?= $patient['end_floor1'] ?></td>
                            <td rowspan="<?= $count ?>"><?= $patient['end_floor2'] ?></td>
                            <td rowspan="<?= $count ?>"><?= $patient['end_floor3'] ?></td>
                            <td rowspan="<?= $count ?>"><?= $patient['status'] ?></td>
                            <td rowspan="<?= $count ?>">
                                <?
                                if ($patient['status'] == $models::STATUS_INPASSED) { ?>
                                    <?= Html::a('<b>Убрать пациента из очереди </b>',
                                        ['/queue/queue/patient-escape-management?id=' . $patient['id']],
                                        [
                                            'class' => 'btn btn-sm btn-outline-danger btn-block'
                                        ])

                                    ?>
                                    <?
                                }
                                ?>
                            </td>
                        </tr>
                        <?
                        foreach ($patient['queueNecessaryPasses'] as $one) { ?>
                            <tr>
                                <td><?= $one['studiesNumbers']['listStudies']['list_studies'] ?></td>
                                <td><?= $one['studiesNumbers']['cabinet'] ?></td>
                                <td><?= ($one['status'] == $models::STATUS_INPASSED) ? 'не пройден' : 'пройден' ?></td>
                                <td> <?
                                    if ($patient['status'] == $models::STATUS_INPASSED) { ?>
                                        <?=
                                        Html::a('<b>Отметить пройденым </b>',
                                            ['/queue/queue/save-status-necessary-pass?idNeces=' . $one['id']],
                                            [
                                                'title' => Yii::t('yii', 'Изменить кабинет'),
                                                'data-toggle' => 'tooltip',
                                                'data' => ['confirm' => 'Вы уверены что хотите изменить кабинет пациенту?'],
                                                'class' => 'btn btn-sm btn-outline-warning btn-block'
                                            ]
                                        ) ?>
                                        <?=
                                        Html::button('<b>Изменить кабинет</b>', [
                                            'dataidNeces' => $one['id'],
                                            'dataidListStudies' => $one['studiesNumbers']['listStudies']['id'],
                                            'class' => 'btn btn-sm btn-outline-danger btn-block',
                                            'title' => Yii::t('yii', 'Изменить кабинет'),
                                            'data-toggle' => 'tooltip',
                                            'data' => ['confirm' => 'Вы уверены что хотите изменить кабинет пациенту?'],
                                            'onclick' => '
                                                $.get("/queue/queue/reassign-cabinet?idNeces="+ $(this).attr("dataidNeces") + "&idListStudies="+ $(this).attr("dataidListStudies"), function(data){
                                                    $("#showModal .modal-body").empty();
                                                    $("#showModal .modal-body").append(data);
                                                    console.log(data);
                                                    $("#showModal").modal("show");
                                            });'
                                        ]);
                                        ?>
                                        <?
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?
                        } ?>
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
<div id="showModal" class="modal fade">
    <div class="modal-dialog modal-lg" style="">
        <div class="modal-content">
            <div class="modal-header-p3">
                <h4 class="modal-title">Изменить кабинет</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0"></div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
$(document).ready(function() {
    setInterval(function(){
        $('#refreshButton').click();
    }, 5000);
});
JS;
$this->registerJs($script);
?>