<?php
/**
 * Отображение списка заявок или участников
 */

$breadcrumbs = array();
if ( $type == 'applications' )
{
    $titleString = 'Заявки';
}else
{
    $titleString = 'Участники';
}
$viewMode = 'event';
if ( $projectid AND $project = Project::model()->findByPk($projectid) )
{
    $breadcrumbs = array(
        'Администрирование' => array('/admin'),
        'Проекты'           => array('/admin/project'),
        $project->name      => array('/admin/project/view','id'=>$project->id),
    );
    $viewMode = 'project';
    $objectid = $projectid;
}
if ( $eventid AND $event = ProjectEvent::model()->findByPk($eventid) )
{
    $breadcrumbs = array(
        'Администрирование'   => array('/admin'),
        'Проекты'             => array('/admin/project'),
        $event->project->name => array('/admin/project/view', 'id' => $event->project->id),
        $event->name          => array('/admin/projectEvent/view','id' => $event->id),
    );
    $viewMode = 'event';
    $objectid = $eventid;
}
if ( $vacancyid AND $vacancy = EventVacancy::model()->findByPk($vacancyid) )
{
    $breadcrumbs = array(
        'Администрирование'            => array('/admin'),
        'Проекты'                      => array('/admin/project'),
        $vacancy->event->project->name => array('/admin/project/view', 'id' => $vacancy->event->project->id),
        $vacancy->event->name          => array('/admin/projectEvent/view','id' => $vacancy->event->id),
        $vacancy->name                 => array('/admin/eventVacancy/view', 'id' => $vacancy->id),
    );
    $viewMode = 'vacancy';
    $objectid = $vacancyid;
}
$breadcrumbs[] = $titleString;
// составляем верхнее меню навигации
$this->breadcrumbs = $breadcrumbs;

?>
<div class="page">
<?php
    if ( $vacancyid )
    {// список заявок на роль
        $this->widget('admin.extensions.wizards.processor.MemberProcessor.MemberProcessor', array(
            'vacancy'           => $vacancy,
            'widgetRoute'       => '/admin/projectMember/index',
            'sectionGridOptions' => array(
                'gridControllerPath' => '/admin/memberInstanceGrid/',
                'updateUrl'          => '/admin/memberInstanceGrid/update',
            ),
            'sectionInstanceId' => Yii::app()->request->getParam('siid', -1),
            'currentMemberId'   => Yii::app()->request->getParam('cmid', 0),
            'lastMemberId'      => Yii::app()->request->getParam('lmid', 0),
            'draft'             => Yii::app()->request->getParam('draft', 0),
            'pending'           => Yii::app()->request->getParam('pending', 0),
            'active'            => Yii::app()->request->getParam('active', 0),
            'rejected'          => Yii::app()->request->getParam('rejected', 0),
            'nograde'           => Yii::app()->request->getParam('nograde', 0),
            'good'              => Yii::app()->request->getParam('good', 0),
            'normal'            => Yii::app()->request->getParam('normal', 0),
            'sad'               => Yii::app()->request->getParam('sad', 0),
        ));
    }else
    {// отображаем список участников события или проекта
        $this->widget('admin.extensions.ProjectMembers.ProjectMembers',array(
            'objectType'      => $viewMode,
            'objectId'        => $objectid,
            'displayType'     => $type,
            'displayFullInfo' => true,
        ));
    }
    ?>
</div>