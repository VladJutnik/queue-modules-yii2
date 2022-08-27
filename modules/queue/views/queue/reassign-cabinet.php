<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="dictionary-form container">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => ['template' => "<div class=\"row mt-2 mb-0 ml-0 mr-0'\"><div class=\"col-sm-12 col-md-12 col-lg-6 col-xl-6 mt-1\">{label}</div><div class=\"col-sm-12 col-md-12 col-lg-6 col-xl-6\">{input}\n{hint}\n{error}</div></div>"],
    ]); ?>

    <?= $form->field($model, 'studies_numbers_id')->dropDownList($typeCabinet) ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
