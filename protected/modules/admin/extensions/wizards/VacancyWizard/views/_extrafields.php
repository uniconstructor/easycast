<?php
/**
 * Страница добавления дополнительных полей для роли при подаче заявки
 */
/* @var $this VacancyWizard */

$this->render('_progress');

$this->widget('admin.extensions.ExtraFieldsManager.ExtraFieldsManager', array(
    'vacancy' => $this->vacancy,
));