<?php
/**
 * Отображение одного проекта в админке
 */
/* @var $model Project */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
	'Проекты'           => array('/admin/project/admin'),
	$model->name,
);

$this->menu = array(
	//array('label'=>'Список проектов','url'=>array('/admin/project/admin')),
	array('label' => 'Создать проект', 'url' => array('/admin/project/create')),
	array('label' => 'Редактировать проект', 'url' => array('/admin/project/update','id' => $model->id)),
    array('label' => 'Добавить мероприятие', 'url' => array('/admin/projectEvent/create', 'projectid' => $model->id)),
    array('label' => 'Создать группу мероприятий', 'url' => array('/admin/projectEvent/create', 'projectid' => $model->id, 'type' => 'group')),
    array('label' => 'Заявки', 'url' => array('/admin/projectMember/index', 'projectid' => $model->id, 'type' => 'applications')),
    array('label' => 'Подтвержденные участники', 'url' => array('/admin/projectMember/index', 'projectid' => $model->id, 'type' => 'members')),
);

if ( $model->status == Project::STATUS_DRAFT )
{// разрешаем удалять черновик проекта
    $this->menu[] = array(
        'label' => 'Удалить проект',
        'url'   => '#',
        'linkOptions' => array(
	        'submit' => array('/admin/project/delete', 'id' => $model->id),
	        'confirm' => 'Вы уверены, что хотите удалить этот проект? Все входящие в него мероприятия и роли также будут удалены.',
	        'csrf' => true
        ),
    );
}
if ( in_array('active', $model->getAllowedStatuses()) )
{// ссылка на активацию проекта
    $this->menu[] = array('label' => 'Запустить проект',
        'url' => array('/admin/project/setStatus', 'id' => $model->id, 'status' => 'active'),
        'linkOptions' => array(
            'confirm' => 'Запустить проект "'.CHtml::encode($model->name).'"? Это действие опубликует все мероприятия проекта и оповестит всех подходящих участников.',
        ),
    );
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{// ссылка на завершение проекта
    $this->menu[] = array('label' => 'Завершить проект',
        'url' => array('/admin/project/setStatus', 'id' => $model->id, 'status' => 'finished'),
        'linkOptions' => array(
            'confirm' => 'Завершить проект "'.CHtml::encode($model->name).'"? Это действие завершит все оставшиеся мероприятия и закроет все вакансии. Запустить проект заново будет нельзя.',
        ),
    );
}
if ( $model->status == Project::STATUS_ACTIVE )
{// предоставить доступ
    $this->menu[] = array('label' => 'Предоставить доступ',
        'url' => array('/admin/customerInvite/create', 'objectId' => $model->id, 'objectType' => 'project'),
    );
}

$this->widget('bootstrap.widgets.TbAlert', array(
    'block' => true, // display a larger alert block?
    'fade'  => true, // use transitions?
    'closeText' => '&times;', // close link text - if set to false, no close link is displayed
    'alerts' => array(
        'success' => array('block' => true, 'fade' => true, 'closeText' => '&times;'),
    ),
));

$dateFormatter = new CDateFormatter('ru');
$timeStart     = Yii::t('coreMessages', 'not_set');
$timeEnd       = Yii::t('coreMessages', 'not_set');
if ( $model->timestart )
{
    $timeStart = $dateFormatter->format('dd.MM.yyyy', $model->timestart);
}
if ( $model->timeend )
{
    $timeEnd = $dateFormatter->format('dd.MM.yyyy', $model->timestart);
}

?>

<h1>Проект "<?php echo $model->name; ?>"</h1>

<?php 

$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'       => $model,
	'attributes' => array(
		array(
            'label' => '&nbsp;',
            'value' => CHtml::link(
                '(Посмотреть как проект выглядит на сайте)',
                Yii::app()->createUrl('/projects/projects/view',
                array('id' => $model->id))
            ),
            'type' => 'raw', 
        ),
		'typetext',
        'shortdescription:raw',
        'description:raw',
        'customerdescription:raw',
        array(
            'label' => ProjectsModule::t('timestart'),
            'value' => $timeStart, 
        ),
		array(
            'label' => ProjectsModule::t('timeend'),
            'value' => $timeEnd, 
        ),
		array(
	        'label' => ProjectsModule::t('project_leaderid'),
	        'value' => $model->leader->fullname,
	    ),
		//'customerid',
		//'orderid',
		'isfree',
		//'memberscount',
		'statustext',
	),
));
?>

<h2>Мероприятия проекта</h2>

<?php 
// таблица со списком мероприятий
$eventsList = new CActiveDataProvider('ProjectEvent', array(
    'data'       => $model->events,
    'pagination' => false,
));
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'         => 'striped bordered condensed',
    'dataProvider' => $eventsList,
    'template' => "{summary}{items}",
    'columns'  => array(
        array(
            'name'   => 'name',
            'header' => ProjectsModule::t('name'),
            'value'  => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/ProjectEvent/view", array("id" => $data->id)));',
            'type'   => 'html'
        ),
        array(
            'name'   => 'groupname',
            'header' => 'Группа',
            'value'  => '$data->group ? CHtml::link($data->group->name, Yii::app()->createUrl("/admin/ProjectEvent/view", array("id" => $data->group->id))): "Нет";',
            'type'   => 'html',
        ),
        array(
            'name'   => 'timestart',
            'header' => 'Время',
            'value'  => '$data->getFormattedTimePeriod()',
        ),
        array(
            'name'   => 'status',
            'header' => ProjectsModule::t('status'),
            'value'  => 'ProjectsModule::t("event_status_".$data->status)',
        ),
    ),
));

