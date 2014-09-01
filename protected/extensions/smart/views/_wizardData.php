<?php
/**
 * Вся информация из анкеты участника, разбитая по вкладкам
 */
/* @var $this SmartMemberInfo */

$tabs          = array();
$stepInstances = WizardStepInstance::model()->forVacancy($this->vacancy->id)->findAll();

foreach ( $stepInstances as $instance )
{// собираем информацию по каждому разделу
    $tabs[$instance->id] = array(
        'label'   => $instance->header,
        'content' => $this->getTabQuestions($instance, $this->projectMember),
    );
}
// выводим вкладки со всей информацией об участнике
$this->widget('bootstrap.widgets.TbTabs', array(
    'type'      => 'tabs',
    'placement' => 'top',
    'tabs'      => $tabs,
));
