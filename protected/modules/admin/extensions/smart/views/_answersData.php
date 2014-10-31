<?php
/**
 * Вся информация из анкеты участника, разбитая по вкладкам
 * @todo использовать списки
 */
/* @var $this SmartMemberInfo */


/**
$tabs          = array();
$stepInstances = WizardStep::model()->forVacancy($this->vacancy->id)->findAll();

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
*/
// полный список вопросов анкеты
$answers = ExtraField::model()->forVacancy($this->vacancy)->findAll();

echo $this->getQuestionsSet($answers, $this->projectMember);