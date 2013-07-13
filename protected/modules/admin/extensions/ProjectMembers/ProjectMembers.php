<?php

/**
 * Список участников проекта или заявок на участие в проекте
 */
class ProjectMembers extends CWidget
{
    public $objectType;

    public $objectId;

    public $displayType;

    public function run()
    {
        switch ( $this->objectType )
        {
            case 'project': echo $this->getProjectMembers(); break;
            case 'event': echo $this->getEventMembers(); break;
            case 'vacancy': echo $this->getVacancyMembers(); break;
        }
    }

    public function getProjectMembers()
    {
        $result = '';

        $project = Project::model()->findByPk($this->objectId);

        $result .= '<h3>'.$project->name.'</h3>';

        foreach ( $project->events as $event )
        {
            $result .= $this->getEventMembers($event);
        }

        return $result;
    }

    protected function getEventMembers($event=null)
    {
        $result = '';

        if ( ! $event )
        {
            $event = ProjectEvent::model()->findByPk($this->objectId);
        }

        if ( $event->vacancies )
        {
            $result .= '<h3>'.$event->name.'</h3>';
            foreach ( $event->vacancies as $vacancy )
            {
                $result .= $this->getVacancyMembers($vacancy);
            }
        }

        return $result;
    }

    protected function getVacancyMembers($vacancy=null)
    {
        $result = '';

        if ( ! $vacancy )
        {
            $vacancy = EventVacancy::model()->findByPk($this->objectId);
        }

        if ( $this->displayType == 'applications' )
        {
            $members = $vacancy->requests;
        }else
        {
            $members = $vacancy->members;
        }

        if ( $members )
        {// есть участники или заявки на участие - отобразим их
            $result .= '<h4>'.$vacancy->name.'</h4>';
            $elements = array();
            foreach ( $members as $member )
            {
                $elements[] = $this->getMemberData($member);
            }

            $arrayProvider = new CArrayDataProvider($elements);
            $result .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered condensed',
                'dataProvider' => $arrayProvider,
                'template'=>"{items}{pager}",
                'columns'=>array(
                    array('name'=>'name', 'header'=>'Участник', 'type' => 'html'),
                    array('name'=>'vacancy', 'header'=>'Вакансия', 'type' => 'html'),
                    array('name'=>'actions', 'header'=>'Действия', 'type' => 'html'),
                ),
            ), true);
        }

        return $result;
    }

    /**
     * Получить одну строку с информацией об участнике
     * @param ProjectMember $member - id анкеты пользователя в таблице questionaries
     * @return array
     */
    protected function getMemberData($member)
    {
        $element = array();
        $element['id'] = $member->id;
        $memberUrl = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $member->member->id));
        $element['name'] = CHtml::link($member->member->user->fullname, $memberUrl);
        $element['vacancy'] = $member->vacancy->name;

        if ( $this->displayType == 'applications' )
        {// если мы отображаем заявки  - то их можно либо утвердить либо отклонить
            $approveUrl = Yii::app()->createUrl('/admin/projectMember/setStatus', array('id' => $member->id, 'status' => 'active'));
            $approveButton = CHtml::link('Подтвердить заявку', $approveUrl, array('class' => 'btn btn-success'));
            $rejectUrl = Yii::app()->createUrl('/admin/projectMember/setStatus', array('id' => $member->id, 'status' => 'rejected'));
            $rejectButton = CHtml::link('Отклонить заявку', $rejectUrl, array('class' => 'btn btn-danger'));

            $element['actions'] = $approveButton.' '.$rejectButton;
        }else
        {
            $element['actions'] = '';
        }

        return $element;
    }

    /**
     * Получить статусы записей, с которыми будут извлекаться участники
     * @return array
     */
    protected function getStatuses()
    {
        $statuses = array();
        if ( $this->displaytype == 'applications' )
        {
            $statuses[] = 'draft';
        }else
        {
            $statuses[] = 'active';
            $statuses[] = 'finished';
        }

        return $statuses;
    }
}