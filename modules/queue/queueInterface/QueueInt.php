<?php

namespace backend\modules\queue\queueInterface;

interface QueueInt
{
    /**
     * Список пациентов на сегодня
     */
    public function actionIndex();
    /**
     * Перерасчет назначенных врачей и обследований для пациенты
     * Назначение этажа
     * Назначения кабинетов пациентов
     */
    public function actionRecalculationDoctorsVisit($user_id);
    /**
     * Отмена выбора пациента у врачей
     * @return $this->redirect('/queue/queue/index');
     */
    public function actionPatientEscapeIndex($id);
    /**
     * Управление кабинетами у пациента
     * @return $this->render('/queue/office-management',[]);
     */
    public function actionOfficeManagement();
    /**
     * Отмена выбора пациента у врачей
     * $idNeces - id из таблицы QueueNecessaryPass для измнения статуса пройден / не пройден
     * @return $this->redirect('/queue/queue/office-management');
     */
    public function actionPatientEscapeManagement($id);
    /**
     *
     */
    public function actionReassignCabinet($idNeces, $idListStudies);
}
?>