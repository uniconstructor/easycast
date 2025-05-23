<?php
/**
 * Страница отображения события в админке
 */
/* @var $this  ProjectEventController */
/* @var $model ProjectEvent */

$pageTitle    = $model->name;
$addRoleLabel = 'Добавить роль';
$groupId      = $model->parentid;
$showDates    = true;
$showNewGroupEvent = ($model->group AND ($model->group->status != ProjectEvent::STATUS_FINISHED));
if ( $model->type === 'group' )
{
    $pageTitle   .= ' [Группа мероприятий]';
    $addRoleLabel = 'Добавить роль (на все дни группы)';
    $groupId      = $model->id;
    // группы событий не имеют продолжительности
    $showDates    = false;
    // нельзя добавлять события в завершенную группу
    $showNewGroupEvent = ($model->status != ProjectEvent::STATUS_FINISHED);
}

// верхняя навигация
$this->breadcrumbs = array(
    'Администрирование'   => array('/admin'),
    'Проекты'             => array('/admin/project'),
    $model->project->name => array('/admin/project/view', 'id' => $model->project->id),
);
if ( $model->group )
{// ссылка на просмотр группы
    $this->breadcrumbs[$model->group->name] = array('/admin/projectEvent/view', 'id' => $model->group->id);
}
$this->breadcrumbs[] = $model->name;

// меню справа
$this->menu = array(
	array(
	    'label'   => 'Новое событие в проекте',
	    'visible' => ! $showNewGroupEvent,
	    'url'     => array('/admin/projectEvent/create', 'projectid' => $model->project->id),
    ),
    // @todo группы событий временно сотключены: от них пока проблем быльше чем пользы
	/*array(
	    'label'   => 'Новое событие в этой группе', 
	    'visible' => $showNewGroupEvent,
	    'url'     => array('/admin/projectEvent/create', 'projectid' => $model->project->id, 'parentid' => $groupId),
    ),*/
	array(
	    'label' => 'Редактировать',
	    'url'   => array('update', 'id' => $model->id),
    ),
    array(
        'label' => $addRoleLabel,
        'url'   => array('/admin/eventVacancy/create', 'eventid' => $model->id),
    ),
    array(
        'label' => 'Заявки', 
        'url'   => array('/admin/projectMember/index', 'eventid' => $model->id, 'type' => 'applications'),
    ),
    array(
        'label' => 'Участники',
        'url'   => array('/admin/projectMember/index', 'eventid' => $model->id, 'type' => 'members'),
    ),
);

if ( $model->status === ProjectEvent::STATUS_DRAFT )
{// разрешаем удалять мероприятие или группу в статусе "черновик"
    $confirmDeleteText = 'Вы уверены, что хотите удалить это мероприятие?';
    if ( $model->type === 'group' )
    {
        $confirmDeleteText = 'Вы уверены, что хотите удалить эту группу? Все входящие в нее мероприятия также будут удалены.';
    }
    $this->menu[] = array(
        'label' => 'Удалить',
        'url'   => '#',
	    'linkOptions' => array(
	        'submit'  => array('delete', 'id' => $model->id),
	        'confirm' => $confirmDeleteText,
	        'csrf'    => true,
	    ),
    );
}
if ( $model->status === ProjectEvent::STATUS_ACTIVE )
{// для опубликованных мероприятий покажем вызывной лист
    $this->menu[] = array(
        'label' => 'Вызывной лист',
        'url'   => array('/admin/projectEvent/callList', 'eventId' => $model->id)
    );
}
if ( in_array('active', $model->getAllowedStatuses()) )
{// ссылка на активацию мероприятия
    $this->menu[] = array(
        'label' => 'Активировать',
        'url'   => array('/admin/projectEvent/setStatus', 'id' => $model->id, 'status' => 'active'),
        'linkOptions' => array(
            'confirm' => 'Это действие оповестит всех подходящих участников о начале съемок. Все вакансии мероприятия и группы также будут активированы. ВНИМАНИЕ: после публикации мероприятия редактировать критерии отбора людей будет нельзя. На всякий случай проверьте все вакансии. Опубликовать мероприятие "'.$model->name.'"?',
        ),
    );
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{// ссылка на завершение мероприятия
    $this->menu[] = array(
        'label' => 'Завершить',
        'url'   => array('/admin/projectEvent/setStatus', 'id' => $model->id, 'status' => 'finished'),
        'linkOptions' => array(
            'confirm' => 'Завершить мероприятие "'.$model->name.'"?',
        ),
    );
}
//if ( $model->status == ProjectEvent::STATUS_ACTIVE )
if ( true )
{// предоставить доступ
    $this->menu[] = array('label' => 'Предоставить доступ',
        'url' => array('/admin/customerInvite/create', 'objectId' => $model->id, 'objectType' => 'event'),
    );
}

// сообщение о смене статуса
$this->widget('bootstrap.widgets.TbAlert');
?>

<h1><?= $pageTitle; ?></h1>

<?php 
// информация о событии или группе
$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'       => $model,
	'attributes' => array(
	    'name',
	    array(
	        'label' => $model->getAttributeLabel('parentid'),
	        'value' => $model->group ? CHtml::link($model->group->name, Yii::app()->createUrl("/admin/ProjectEvent/view", array("id" => $model->group->id))) : 'Без группы',
            'type'  => 'html',
	    ),
	    array(
	        'label' => $model->getAttributeLabel('type'),
	        'value' => $model->getTypeLabel(),
	    ),
        array(
            'label'   => $model->getAttributeLabel('timestart'),
            'value'   => $model->timestart ? Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $model->timestart) : '[дата уточняется]',
            'visible' => $showDates,
        ),
        array(
            'label'   => $model->getAttributeLabel('timeend'),
            'value'   => $model->timeend ? Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $model->timeend): '[дата уточняется]',
            'visible' => $showDates,
        ),
		'description:raw',
		'memberinfo:html',
        array(
            'label'   => $model->getAttributeLabel('eta'),
            'value'   => 'За '.($model->eta / 60).' мин',
            'visible' => (bool)$model->eta,
        ),
        array(
            'label' => ProjectsModule::t('status'),
            'value' => ProjectsModule::t("event_status_".$model->status),
        ),
	),
));

// ТОЛЬКО ДЛЯ ГРУПП
// список событий группы
if ( $model->type === 'group' )
{
    echo '<h2>События в этой группе</h2>';
    $eventsList = new CActiveDataProvider('ProjectEvent', array(
        'data'       => $model->events,
        'pagination' => false,
    ));

    $this->widget('bootstrap.widgets.TbGridView', array(
        'type'         => 'striped bordered condensed',
        'dataProvider' => $eventsList,
        'template'     => "{summary}{items}",
        'columns'      => array(
            array(
                'name'   => 'name',
                'header' => ProjectsModule::t('name'),
                'value'  => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/ProjectEvent/view", array("id" => $data->id)));',
                'type'   => 'html'),
            array(
                'name'   => 'timestart',
                'header' => ProjectsModule::t('timestart'),
                'value'  => 'Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $data->timestart)',
            ),
            array(
                'name'   => 'timeend',
                'header' => ProjectsModule::t('timeend'),
                'value'  => 'Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $data->timeend)',
            ),
            array(
                'name'   => 'status',
                'header' => ProjectsModule::t('status'),
                'value'  => 'ProjectsModule::t("event_status_".$data->status)',
            ),
        ),
    ));
}

// Список ролей
$vacanciesTitle = 'Роли';
$vacanciesInfo  = '';
if ( $model->group )
{
    $vacanciesTitle = 'Роли события';
}
if ( $model->type === 'group' )
{
    $vacanciesTitle  = 'Роли группы';
    $vacanciesInfo   = '(создаются и набираются одновременно для всех событий в группе)';
}
?>

<h2><?= $vacanciesTitle; ?></h2>
<h3><?= $vacanciesInfo; ?></h3>

<?php 
// Список всех ролей мероприятия
$vacanciesList = new CActiveDataProvider('EventVacancy', array(
    'data'       => $model->vacancies,
    'pagination' => false,
));
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'         => 'striped bordered condensed',
    'dataProvider' => $vacanciesList,
    'template'     => "{summary}{items}",
    'columns'      => array(
        array(
            'name'   => 'name',
            'header' => ProjectsModule::t('name'),
            'type'   => 'html',
            'value'  => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/eventVacancy/view", array("id" => $data->id)));',
        ),
        array(
            'name'   => 'limit',
            'header' => 'Заполнение',
            'value'  => '"(".$data->membersCount." из ".$data->limit.") [Заявки: ".$data->requestsCount."]"',
        ),
        array(
            'name'   => 'salary',
            'header' => 'Оплата (за день)',
            'value'  => '$data->salary." р."',
        ),
        array(
            'name'   => 'status',
            'header' => ProjectsModule::t('status'),
            'value'  => 'ProjectsModule::t("event_status_".$data->status)',
        ),
    ),
));

if ( $model->group )
{// роли из группы
    echo '<h3>Роли группы</h3>';
    $groupVacanciesList = new CActiveDataProvider('EventVacancy', array(
        'data'       => $model->group->vacancies,
        'pagination' => false,
    ));
    $this->widget('bootstrap.widgets.TbGridView', array(
        'type'         => 'striped bordered condensed',
        'dataProvider' => $groupVacanciesList,
        'template'     => "{summary}{items}",
        'columns'      => array(
            array(
                'name'   => 'name',
                'header' => ProjectsModule::t('name'),
                'type'   => 'html',
                'value'  => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/eventVacancy/view", array("id" => $data->id)));',
            ),
            array(
                'name'   => 'limit',
                'header' => 'Заполнение',
                'value'  => '"(".$data->membersCount." из ".$data->limit.") [Заявки: ".$data->requestsCount."]"',
            ),
            array(
                'name'   => 'salary',
                'header' => 'Оплата (за день)',
                'value'  => '$data->salary." р."',
            ),
            array(
                'name'   => 'status',
                'header' => ProjectsModule::t('status'),
                'value'  => 'ProjectsModule::t("vacancy_status_".$data->status)',
            ),
        ),
    ));
}
