<?php

namespace backend\modules\queue\queueInterface;

interface ElectronicInt
{
    /**
     * Список пациентов на 1 этаже
     */
    public function actionBoardOne();
    /**
     * Список пациентов на 2 этаже
     */
    public function actionBoardTwo();
    /**
     * Список пациентов на 3 этаже
     */
    public function actionBoardThree();
    /**
     * Пересчет если пациент не пришел
     */
    public function actionPatientNotCome($id);
    /**
     * Пересчет этажа пациента
     * Изменения статуса пройденого исследования
     * Обновление информации о пациенте в очереди
     */
    public function actionPatientCompleted();
}