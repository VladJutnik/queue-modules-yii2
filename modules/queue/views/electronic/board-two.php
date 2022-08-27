<?php

use yii\bootstrap4\Html;
use yii\widgets\Pjax;

?>

<?php
Pjax::begin(); ?>
<?= Html::a(
    'Обновить',
    ['/queue/electronic/board-two'],
    ['class' => 'btn btn-sm btn-primary d-none', 'id' => 'refreshButton']
) ?>
<style>
    .timeTablo22 {
        margin-top: -60px !important;
    }
</style>
<div class="row_electronic timeTablo22">
    <table class="col-12 table_electronic">
        <thead>
        </thead>
        <tbody>
        <tr class="table_el_background">
            <td class="col-3 text_el_white text_el_font_25">
                ЭТАЖ 2
            </td>
            <td class="col-5 text_el_white text_el_font_25">
                ФБУН Новосибирский НИИ гигиены Роспотребнадзора
            </td>
            <td class="col-4 text_el_white text_el_font_25 text_el_right">
                <?= $time ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?=
$this->render(
    '_tablo',
    [
        'listPatientFloorAll' => $listPatientFloorAll,
        'listPatientFloor' => $listPatientFloor,
        'time' => $time,
    ]
); ?>

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

