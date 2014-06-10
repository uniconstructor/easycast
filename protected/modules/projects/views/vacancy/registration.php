<?php
/**
 * Подача заявки на событие через динамическую форму анкеты
 */
/* @var $this VacancyController */
/* @var $model QDynamicFormModel */
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <?php 
            // информация о событии на которое подается заявка
            $this->widget('projects.extensions.ProjectInfo.ProjectInfo', array(
                'eventId'     => $model->vacancy->event->id,
                'displayTabs' => array('main'),
            ));
            ?>
        </div>
        <div class="row-fluid">
            <?php 
            // виджет динамической формы: он сам выбирает нужные поля в зависимости от настроек роли
            $this->widget('questionary.extensions.widgets.QDynamicForm.QDynamicForm', array(
                'model' => $model
            ));
            ?>
        </div>
        <div class="row">
            <?php
            $this->widget('questionary.extensions.widgets.QUserInvites.QUserInvites', array(
                'questionary' => $model->questionary,
            ));
            ?>
        </div>
    </div>
</div>