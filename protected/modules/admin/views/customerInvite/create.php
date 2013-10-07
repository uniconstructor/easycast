<?php
/**
 * Создание нового приглашения на отбор актеров
 * @var CustomerInvite $model
 */

// заголовок страницы
$title = 'Предоставить доступ ';

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
	//'Приглашения для заказчиков' => array('/admin/customerInvite/admin'),
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
?>

<h2><?= $title; ?></h2>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>