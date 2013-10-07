<?php
/**
 * Страница отображения одного приглашения заказчика
 * @var CustomerInvite $model
 * 
 * @todo добавить предпросмотр отправляемого письма
 */
$this->breadcrumbs = array(
    'Администрирование' => array('/admin/index'),
);
switch ( $model->objecttype )
{
    case 'project':
        $this->breadcrumbs['Проекты'] = array('/admin/project/admin');
        $this->breadcrumbs[$model->project->name] = array('/admin/project/view', 'id' => $model->project->id);
    break;
    case 'event':
        $this->breadcrumbs['Проекты'] = array('/admin/project/admin');
        $this->breadcrumbs[$model->event->project->name] = array('/admin/project/view', 'id' => $model->event->project->id);
        $this->breadcrumbs[$model->event->name] = array('/admin/projectEvent/view', 'id' => $model->event->id);
    break;
    case 'vacancy':
        $this->breadcrumbs['Проекты'] = array('/admin/project/admin');
        $this->breadcrumbs[$model->vacancy->event->project->name] = array('/admin/project/view', 'id' => $model->vacancy->event->project->id);
        $this->breadcrumbs[$model->vacancy->event->name] = array('/admin/projectEvent/view', 'id' => $model->vacancy->event->id);
        $this->breadcrumbs[$model->vacancy->name] = array('/admin/eventVacancy/view', 'id' => $model->vacancy->id);
    break;
    default: throw new CHttpException(400, 'Неизвестный тип приглашения ('.$model->objecttype.')');
}
// @todo вывести все приглашения для мероприятия, проекта, или события
//$this->breadcrumbs['Приглашения для заказчиков'] = array('/admin/customerInvite/admin');
$this->breadcrumbs[] = 'Приглашение №'.$model->name;

?>
<h1>Приглашение №<?php echo $model->id; ?></h1>
<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data' => $model,
	'attributes' => array(
		'id',
		//'objecttype',
		'objectid',
		'email',
		'name',
		'managerid',
		'timecreated',
		'timeused',
		'comment',
		'userid',
	),
));
?>
