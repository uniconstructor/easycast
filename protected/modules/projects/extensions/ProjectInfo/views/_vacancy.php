<?php
/**
 * Отображение одной вакансии
 */
?>
<div class="row-fluid">
    <h4><?= CHtml::encode($vacancy->name); ?></h4>
    <?= $vacancy->description; ?>
    <?php
    // отображаем кнопки "подать заявку" и "отозвать заявку" 
    $this->widget('application.modules.projects.extensions.VacancyActions.VacancyActions',
        array(
            'vacancy' => $vacancy,
            'mode'    => 'normal',
        ));
    ?>
    <hr>
</div>