<?php
/**
 * Страница просмотра роли
 */
/* @var $this  EventVacancyController */
/* @var $model EventVacancy */

$this->breadcrumbs = array(
	'Администрирование'          => array('/admin'),
    'Проекты'                    => array('/admin/project'),
    $model->event->project->name => array('/admin/project/view', 'id' => $model->event->project->id),
    $model->event->name          => array('/admin/projectEvent/view', 'id' => $model->event->id),
	$model->name,
);

$this->menu = array(
	array(
	    'label' => 'Страница события', 
	    'url'   => array('/admin/projectEvent/view', 'id' => $model->event->id),
    ),
	array(
	    'label' => 'Добавить роль',
	    'url'   => array('/admin/eventVacancy/create', 'eventid' => $model->event->id),
    ),
	array(
	    'label' => 'Редактировать роль',
	    'url'   => array('/admin/eventVacancy/update', 'id' => $model->id),
    ),
    array(
        'label' => 'Заявки',
        'url'   => array('/admin/projectMember/index', 'vacancyid' => $model->id, 'type' => 'applications'),
    ),
    array(
        'label' => 'Подтвержденные участники',
        'url'   => array('/admin/projectMember/index', 'vacancyid' => $model->id, 'type' => 'members'),
    ),
);

if ( $model->status === EventVacancy::STATUS_DRAFT )
{// разрешаем удалять вакансию-черновик
    $this->menu[] = array(
        'label'       => 'Удалить роль', 
        'url'         => '#',
        'linkOptions' => array(
                'submit'  => array('/admin/eventVacancy/delete', 'id' => $model->id),
                'confirm' => 'Удалить эту роль?',
                'csrf'    => true,
            ),
        );
}
if ( in_array('active', $model->getAllowedStatuses()) )
{// ссылка на активацию вакансии
    $this->menu[] = array(
        'label' => 'Опубликовать роль',
        'url'   => array('/admin/eventVacancy/setStatus', 'id' => $model->id, 'status' => 'active'),
        'linkOptions' => array(
            'confirm' => 'Это действие оповестит всех подходящих участников о начале съемок.
                ВНИМАНИЕ: после публикации роли редактировать критерии поиска будет нельзя.
                Опубликовать роль "'.$model->name.'"?',
        ),
    );
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{// Ссылка на закрытие вакансии
    $this->menu[] = array(
        'label' => 'Закрыть роль',
        'url'   => array('/admin/eventVacancy/setStatus',
            'id'     => $model->id,
            'status' => 'finished',
        ),
        'linkOptions' => array(
            'confirm' => 'Закрыть роль "'.$model->name.'"?',
        ),
    );
}
if ( $model->status === EventVacancy::STATUS_ACTIVE )
{// предоставить доступ
    $this->menu[] = array(
        'label' => 'Предоставить доступ',
        'url'   => array('/admin/customerInvite/create',
            'objectType' => 'vacancy',
            'objectId'   => $model->id, 
        ),
    );
}

// отображение оповещений о смене статуса
$this->widget('bootstrap.widgets.TbAlert');
?>

<div class="row-fluid">
    <h1>Роль "<?php echo $model->name; ?>"</h1>
    <?php
    // описание самой роли
    $this->widget('bootstrap.widgets.TbDetailView', array(
    	'data'       => $model,
    	'attributes' => array(
    		'name',
    		'description:html',
    		array(
                'label' => 'Заполнение',
                'value' => '('.$model->membersCount.' из '.$model->limit.') [Заявки: '.$model->requestsCount.']',
            ),
    		array(
                'label' => ProjectsModule::t('status'),
                'value' => $model->statustext,
            ),
            'salary',
    	),
    ));
    ?>
</div>
<?php 

// виджет расширенной формы поиска (по всей базе)
$this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
    'searchObject' => $model,
    'mode'         => 'vacancy',
    'dataSource'   => 'db',
    'searchUrl'    => '/admin/eventVacancy/setSearchData',
    'clearUrl'     => '/admin/eventVacancy/clearFilterSearchData',
    'countUrl'     => '/admin/eventVacancy/setSearchData',
    'countResultPosition' => 'bottom',
    'refreshDataOnChange' => true,
    //'refreshDataOnChange' => false,
    'searchButtonTitle'      => 'Сохранить',
    'clearButtonHtmlOptions' => array(
        'class' => 'btn btn-danger btn-large',
        'id'    => 'clear_search',
    ),
    'countContainerHtmlOptions' => array(
        'class' => 'well text-center',
    ),
));

$this->widget('admin.extensions.ExtraFieldsManager.ExtraFieldsManager', array(
    'vacancy' => $model,
));

$clips = Yii::app()->getModule('admin')->formClips;
foreach ( $clips as $clip )
{
    echo $this->clips[$clip];
}
?>