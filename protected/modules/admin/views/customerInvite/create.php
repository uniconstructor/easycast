<?php
/**
 * Создание нового приглашения на отбор актеров
 */
/* @var $model CustomerInvite */

// заголовок страницы
$title = 'Предоставить доступ ';

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
);

switch ( $model->objecttype )
{
    case 'project':
        $this->breadcrumbs['Проекты'] = array('/admin/project/admin');
        $this->breadcrumbs[$model->project->name] = array('/admin/project/view', 'id' => $model->project->id);
        $title .= 'к отбору заявок на проект "'.CHtml::encode($model->project->name).'"' ;
    break;
    case 'event':
        $this->breadcrumbs['Проекты'] = array('/admin/project/admin');
        $this->breadcrumbs[$model->event->project->name] = array('/admin/project/view', 'id' => $model->event->project->id);
        $this->breadcrumbs[$model->event->name] = array('/admin/projectEvent/view', 'id' => $model->event->id);
        $title .= 'к отбору заявок на событие "'.CHtml::encode($model->event->name).'" '.$model->event->getFormattedTimePeriod();
    break;
    case 'vacancy':
        $this->breadcrumbs['Проекты'] = array('/admin/project/admin');
        $this->breadcrumbs[$model->vacancy->event->project->name] = array('/admin/project/view', 'id' => $model->vacancy->event->project->id);
        $this->breadcrumbs[$model->vacancy->event->name] = array('/admin/projectEvent/view', 'id' => $model->vacancy->event->id);
        $this->breadcrumbs[$model->vacancy->name] = array('/admin/eventVacancy/view', 'id' => $model->vacancy->id);
        $title .= 'к отбору заявок на роль "'.CHtml::encode($model->vacancy->name).'" '.$model->vacancy->event->getFormattedTimePeriod();
    break;
    default: throw new CHttpException(400, 'Неизвестный тип приглашения ('.$model->objecttype.')');
}
$this->breadcrumbs[] = 'Предоставить доступ';

$this->widget('bootstrap.widgets.TbAlert');
?>

<div class="page">
    <div class="container">
        <h2 class="page-title"><?= $title; ?></h2>
        <?php 
        // форма создания приглашения
        echo $this->renderPartial('_form', array('model' => $model)); 
        
        $searchFilter = new CustomerInvite('search');
        $searchFilter->unsetAttributes();
        $searchFilter->objecttype = $model->objecttype;
        $searchFilter->objectid   = $model->objectid;
        
        $this->widget('bootstrap.widgets.TbGridView',array(
            'id'           => 'customer-invite-grid',
            'dataProvider' => $searchFilter->search(),
            'filter'       => $searchFilter,
            'columns' => array(
                'id',
                'email',
                'name',
                // Цель приглашения
                /*array(
                    'name'   => 'objectid',
                    'value'  => '$data->'.$model->objecttype.'->name." [Отбор участников]"',
                    'header' => '<b>Цель приглашения</b>',
                    'type'   => 'html',
                ),*/
                // кто отправил ссылку
                array(
                    'name'   => 'managerid',
                    'value'  => '$data->manager->fullname',
                    'header' => '<b>Кто пригласил</b>',
                    'type'   => 'html',
                ),
                // время создания
                array(
                    'name'    => 'timecreated',
                    'value'   => '($data->timecreated ? date("Y-m-d H:i", $data->timecreated): "Не отправлена")',
                    'header'  => '<b>Время создания</b>',
                    'type'    => 'html',
                ),
                // время использования
                array(
                    'name'    => 'timeused',
                    'value'   => '($data->timeused ? date("Y-m-d H:i", $data->timeused): "Не использована")',
                    'header'  => '<b>Время использования</b>',
                    'type'    => 'html',
                ),
                //'comment',
                //'userid',
                /*array(
                 'class' => 'bootstrap.widgets.TbButtonColumn',
                    'template' => '{view} {update}',
                    'viewButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/view", array("id" => $data->questionaryid))',
                    'updateButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/update", array("id" => $data->questionaryid))',
                ),*/
            ),
        ));
        ?>
        
    </div>
</div>