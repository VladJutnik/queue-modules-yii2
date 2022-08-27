<?php

use yii\bootstrap4\Html;

?>

<div class="container p-3 mb-2 bg-light text-dark rounded">
    <div class="row">
        <div class="col-6">
            <?= ($paitent['patient']['fio']) ?
                'Вам назначен пациент:<br>
                  <strong>' . $paitent['patient']['fio'] . '</strong>'
                : Html::a('Выбор пациента', ['../../site/electronic-queue'], [
                    //'style' => 'font-size: 0.9rem !important; font-family: serif !important;',
                    'class' => 'btn btn-outline-success btn-block text-center btn-sm',
                ]) ?>
        </div>
        <?
        if ($paitent['patient']['fio']) {
            ?>
            <div class="col-2">
                <?= Html::a('Просмотр пациент', ['../../list-patients/view?id=' . $paitent['patient']['id']],
                    [
                        //'style' => 'font-size: 0.9rem !important; font-family: serif !important;',
                        'class' => 'btn btn-outline-primary btn-block text-center btn-sm',
                    ]
                ) ?>
            </div>
            <div class="col-2">
                <!--Удалить у него активный кабинет
                Изменить статус пройденого кабинета
                Посмотреть остались ли еще на этаже кабинеты
                Если не осталось то пересчитать этаж хз как пока-->
                <?= Html::a('Пациент завершил прием',
                    ['/queue/electronic/patient-completed'],
                    [
                        'class' => 'btn btn-outline-success btn-block text-center btn-sm',
                        'data-toggle' => 'tooltip',
                        'data-method' => 'POST',
                        'data-params' => [
                            'idDesignatedPatient' => $paitent['id'],
                            'userId' => $paitent['patient']['id'],
                        ],
                        'data' => ['confirm' => 'Вы уверены что пациент завершил прием?'],
                    ]
                );
                ?>
            </div>
            <div class="col-2">
                <?= Html::a('Пациент не явился', ['/queue/electronic/patient-not-come?id=' . $paitent['id']],
                    [
                        'class' => 'btn btn-outline-danger btn-block text-center btn-sm',
                        'data-toggle' => 'tooltip',
                        'data' => ['confirm' => 'Вы уверены что пациент не пришел на прием?'],
                    ]
                ) ?>
            </div>
            <?
        } ?>
    </div>
</div>
