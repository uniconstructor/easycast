<?php

/**
 * Список участников проекта или заявок на участие в проекте
 */
class ProjectMembers extends CWidget
{
    /**
     * @var string - для какого объекта отображается список участников
     *               project
     *               event
     *               vacancy
     */
    public $objectType;

    /**
     * @var int - id объекта для которого отображается список участников
     */
    public $objectId;

    /**
     * @var string - режим отображения: 
     *               заявки (applications) 
     *               подтвержденные участники (members)
     *               предварительно отобранные (pending)
     *               отклоненные (rejected)
     */
    public $displayType;

    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        switch ( $this->objectType )
        {
            case 'project': echo $this->getProjectMembers(); break;
            case 'event':   echo $this->getEventMembers(); break;
            case 'vacancy': echo $this->getVacancyMembers(); break;
        }
    }

    /**
     * Получить список участников всего проекта
     * @return string - html-код таблицы с участниками
     */
    public function getProjectMembers()
    {
        $result = '';

        $project = Project::model()->findByPk($this->objectId);

        $result .= '<h3>'.$project->name.'</h3>';
        
        foreach ( $project->groups as $event )
        {
            $result .= $this->getEventMembers($event);
        }
        
        foreach ( $project->events as $event )
        {
            $result .= $this->getEventMembers($event);
        }

        return $result;
    }

    /**
     * Получить список участников мероприятия (а также неподтвержденные заявки)
     * @param ProjectEvent $event
     * @return string - html-код таблицы с участниками
     */
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

    /**
     * Посмотреть заявки на вакансию, а также утвержденных на вакансию участников
     * @param EventVacancy $vacancy - просматриваемая вакансия
     * @return string - html-код таблицы с участниками
     */
    protected function getVacancyMembers($vacancy=null)
    {
        $result = '';

        if ( ! $vacancy )
        {
            $vacancy = EventVacancy::model()->findByPk($this->objectId);
        }

        if ( $this->displayType == 'applications' )
        {// показываем заявки
            $members = $vacancy->requests;
        }else
        {// показываем участников
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
            // в списке участников разбивка по страницам не нужна
            $arrayProvider = new CArrayDataProvider($elements, array('pagination' => false));
            
            $result .= $this->widget('bootstrap.widgets.TbGridView', array(
                'type'         => 'striped bordered condensed',
                'dataProvider' => $arrayProvider,
                'template'     => "{items}{pager}",
                'columns' => $this->getMemberColumns(),
            ), true);
        }

        return $result;
    }
    
    /**
     * Определить, какие столбцы должны отображаться в таблице со списком заявок/участников
     * @return array
     */
    protected function getMemberColumns()
    {
        $columns = array();
        $columns[] = array(
            'name'   => 'name',
            'header' => 'Участник',
            'type'   => 'html');
        $columns[] = array(
            'name'   => 'vacancy',
            'header' => 'Вакансия',
            'type'   => 'html');
        if ( $this->displayType == 'applications' )
        {// для заявок покажем время отправки участником
            $columns[] = array(
                'name'   => 'timecreated',
                'header' => 'Отправлена участником',
                'type'   => 'html');
        }else
        {// для подтвержденных участников покажем кто их утвердил
            
        }
        $columns[] = array(
            'name'   => 'actions',
            'header' => 'Действия',
            'type'   => 'raw');
        
        return $columns;
    }

    /**
     * Получить одну строку с информацией об участнике
     * @param ProjectMember $member - id анкеты пользователя в таблице questionaries
     * @return array
     */
    protected function getMemberData($member)
    {
        $memberUrl = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $member->member->id));
        
        $element = array();
        $element['id']      = $member->id;
        $element['name']    = CHtml::link($member->member->user->fullname, $memberUrl);
        $element['vacancy'] = $member->vacancy->name;
        if ( $this->displayType == 'applications' )
        {// для заявок покажем время отправки участником
            $element['timecreated'] = Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $member->timecreated);
        }else
        {// для подтвержденных участников покажем кто их утвердил
            
        }
        $element['actions'] = $this->getMemberActions($member);

        return $element;
    }
    
    /**
     * Получить кнопки с действиями для участника
     * @param ProjectMember $member
     * @return string
     */
    protected function getMemberActions($member)
    {
        return $this->widget('application.modules.projects.extensions.MemberActions.MemberActions', array(
            'member' => $member,
        ), true);
    }

    /**
     * Получить статусы записей, с которыми будут извлекаться участники
     * @return array
     * 
     * @todo удалить если так и не понадобится
     */
    protected function getStatuses()
    {
        $statuses = array();
        if ( $this->displayType == 'applications' )
        {
            $statuses[] = 'draft';
            $statuses[] = 'pending';
        }else
        {
            $statuses[] = 'active';
            $statuses[] = 'finished';
        }

        return $statuses;
    }
}