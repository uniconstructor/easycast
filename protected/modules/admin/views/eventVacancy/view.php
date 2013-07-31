<?php
/**
 * Страница просмотра вакансии
 */

$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
    $model->event->project->name=>array('/admin/project/view', 'id' => $model->event->project->id),
    $model->event->name=>array('/admin/projectEvent/view','id'=>$model->event->id),
	$model->name,
);

$this->menu = array(
	array('label' => 'Страница мероприятия', 'url' => array('/admin/projectEvent/view','id'=>$model->event->id)),
	array('label' => 'Добавить вакансию', 'url' => array('/admin/eventVacancy/create', 'eventid'=>$model->event->id)),
	array('label' => 'Редактировать вакансию', 'url' => array('/admin/eventVacancy/update','id'=>$model->id)),
	// @todo решить, можно ли удалять вакансию (можно, но только черновик)
	/*array('label' => 'Удалить вакансию','url'=>'#',
        'linkOptions' => array(
            'submit' => array(
                '/admin/eventVacancy/delete',
                'id' => $model->id,),
            'confirm' => 'Вы уверены что хотите удалить эту вакансию?',
            'csrf' => true),
            ),*/
    array('label'=>'Заявки на участие','url'=>array('/admin/projectMember/index', 'vacancyid'=>$model->id, 'type' => 'applications')),
);

if ( in_array('active', $model->getAllowedStatuses()) )
{// ссылка на активацию вакансии
    $this->menu[] = array('label'=>'Открыть вакансию',
        'url'=>array('/admin/eventVacancy/setStatus', 'id'=>$model->id, 'status' => 'active'),
        'linkOptions' => array(
            'confirm' => 'Это действие оповестит всех подходящих участников о начале съемок. ВНИМАНИЕ: после открытия вакансии редактировать критерии отбора людей будет нельзя. Открыть вакансию "'.$model->name.'"?',
        ),
    );
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{// Ссылка на закрытие вакансии
    $this->menu[] = array('label'=>'Закрыть вакансию',
        'url'=>array('/admin/eventVacancy/setStatus', 'id'=>$model->id, 'status' => 'finished'),
        'linkOptions' => array(
            'confirm' => 'Закрыть вакансию "'.$model->name.'"?',
        ),
    );
}
// отображаение оповещений о смене статуса
$this->widget('bootstrap.widgets.TbAlert', array(
    'block'     => true, // display a larger alert block?
    'fade'      => true, // use transitions?
    'closeText' => '&times;', // close link text - if set to false, no close link is displayed
    'alerts' => array( // configurations per alert type
        'success' => array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
));
?>

<div class="row">
    <div class="span6">
        <h1>Вакансия "<?php echo $model->name; ?>"</h1>
        <?php
        // описание самой вакансии
        $this->widget('bootstrap.widgets.TbDetailView',array(
        	'data'=>$model,
        	'attributes'=>array(
        		'name',
        		'description:html',
        		'limit',
        		array(
                    'label' => ProjectsModule::t('status'),
                    'value' => $model->statustext,
                ),
        	),
        ));
        ?>
    </div>
    <div class="span3">
        <?php
        // Критерии отбора участников для вакансии
        $this->widget('admin.extensions.VacancyFilters.VacancyFilters', array(
            'mode' => 'vacancy',
            'vacancy' => $model,
            //'filterInstances' => $model->filterinstances,
        ));
        ?>
    </div>
</div>
