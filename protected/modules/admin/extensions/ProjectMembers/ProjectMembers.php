<?php

/**
 * Список участников проекта или заявок на участие в проекте
 * 
 * @todo в списке мероприятий не разделять события на группы, вместо этого показывать для каждого события
 *       отдельный блок с заявками группы (если мероприятие содержит группу)
 *       Второй вариант: отображать сначала группы (в рамочке), внутри них события, потом отдельные события
 *       Всегда писать список дней в группах
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
     * @var bool - отображать ли заголовок?
     */
    public $displayHeader = true;
    
    /**
     * @var отображать ли столбец "роль" в списке заявок? (для краткого вида заявок)
     */
    public $displayVacancyColumn = true;
    
    /**
     * @var отображать ли столбец "время подачи заявки" в списке заявок?
     */
    public $displayTimeColumn = true;
    
    /**
     * @var bool - отображать ли полную анкету участника в заявке?
     */
    public $displayFullInfo = false;

    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::app()->getClientScript()->registerSweelixScript('shadowbox');
        parent::init();
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
            case 'event':   echo $this->getEventMembers();   break;
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
        if ( ! $project = Project::model()->findByPk($this->objectId) )
        {
            throw new UnexpectedValueException('Проект не найден');
        }

        if ( $this->displayHeader )
        {// отображаем заголовок проекта только тогда когда нужно
            $result .= $this->getProjectHeader($project);
        }
        
        foreach ( $project->groups as $event )
        {// показываем сначала группы проекта
            $result .= $this->getEventMembers($event);
        }
        
        foreach ( $project->events as $event )
        {// затем отдельные события
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
            if ( $this->displayHeader OR $this->objectType != 'event' )
            {// отображаем заголовок для мероприятия, только если это явно задано при отображании мероприятия
                // или если отображается проект
                $result .= $this->getEventHeader($event);
            }
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
        {// отображается одна роль отдельно, а не все роли мероприятия
            $vacancy = EventVacancy::model()->findByPk($this->objectId);
        }

        if ( $this->displayType == 'applications' )
        {// показываем заявки
            $members = $vacancy->requests;
        }else
        {// показываем участников
            $members = $vacancy->members;
        }
        // получаем краткое описание вакансии
        $result .= $this->getVacancySummary($vacancy);
        if ( ! $members )
        {// для этой роли нет участников или заявок - так и напишем
            $result .= '<div class="alert">(Пусто)</div>';
            return $result;
        }
        // участники или заявки есть - отобразим их
        if ( $this->displayFullInfo )
        {// выводим полную анкету для каждого участника
            $result .= $this->getFullMembersList($members);
        }else
        {// выводим краткую таблицу
            $result .= $this->getShortMembersList($members);
        }

        return $result;
    }
    
    /**
     * Получить список заявок на участие с кнопками принятия/отклонения заявки
     * с краткой информацией о каждом участнике (только ФИО, ссылка на анкету и действия)
     * 
     * @param array $members - список заявок (объекты класса ProjectMember) 
     * @return string
     */
    protected function getShortMembersList($members)
    {
        $elements = array();
        foreach ( $members as $member )
        {
            $elements[] = $this->getMemberData($member);
        }
        // в списке участников разбивка по страницам не нужна
        $arrayProvider = new CArrayDataProvider($elements, array('pagination' => false));
        // выводим список участников таблицей
        return $this->widget('bootstrap.widgets.TbGridView', array(
            'type'         => 'striped bordered condensed',
            'dataProvider' => $arrayProvider,
            'template'     => "{summary}{items}{pager}",
            'columns'      => $this->getMemberColumns(),
        ), true);
    }
    
    /**
     * Получить список заявок на участие с кнопками принятия/отклонения заявки
     * с подробной информацией о каждом участнике (вся анкета)
     * 
     * @param array $members - список заявок (объекты класса ProjectMember) 
     * @return string
     */
    protected function getFullMembersList($members)
    {
        return $this->widget('application.modules.admin.extensions.QAdminFullDataList.QAdminFullDataList', array(
            'members' => $members,
        ), true);
    }
    
    /**
     * 
     * @param Project $project
     * @return string
     */
    protected function getProjectHeader($project)
    {
        return '<h1>'.CHtml::encode($project->name).'</h1>';
    }
    
    /**
     * 
     * @param ProjectEvent $event
     * @return string
     */
    protected function getEventHeader($event)
    {
        return '<h2>'.CHtml::encode($event->name).'</h2>';
    }
    
    /**
     * 
     * @param EventVacancy $vacancy
     * @return string
     */
    protected function getVacancyHeader($vacancy)
    {
        return '<h3>Роль: '.CHtml::encode($vacancy->name).'</h3>';
    }
    
    /**
     * Получить описание роли
     * @param EventVacancy $vacancy
     * @return string
     */
    protected function getVacancySummary($vacancy)
    {
        $result = $this->getVacancyHeader($vacancy);
        $result .= '<div class="alert alert-info">'.$vacancy->description.'</div>';
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
        if ( $this->displayVacancyColumn )
        {
            $columns[] = array(
                'name'   => 'vacancy',
                'header' => 'Роль',
                'type'   => 'html');
        }
        if ( $this->displayType == 'applications' AND $this->displayTimeColumn )
        {// для заявок покажем время отправки участником
            $columns[] = array(
                'name'   => 'timecreated',
                'header' => 'Отправлена участником',
                'type'   => 'html');
        }else
        {// @todo для подтвержденных участников покажем кто их утвердил
            
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
        $element = array();
        $element['id']      = $member->id;
        $element['name']    = $this->getMemberName($member);
        
        if ( $this->displayVacancyColumn )
        {// показываем название роли
            $element['vacancy'] = $member->vacancy->name;
        }
        if ( $this->displayType == 'applications' AND $this->displayTimeColumn )
        {// для заявок покажем время отправки участником
            $element['timecreated'] = Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $member->timecreated);
        }else
        {// @todo для подтвержденных участников покажем кто их утвердил
            
        }
        $element['actions'] = $this->getMemberActions($member);

        return $element;
    }
    
    /**
     * Получить ФИО участника как ссылку
     * @param ProjectMember $member
     * @return string
     */
    protected function getMemberName($member)
    {
        $memberUrl   = Yii::app()->createAbsoluteUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $member->member->id));
        $memberLink  = CHtml::link($member->member->fullname, $memberUrl, array('target' => '_blank'));
        return $memberLink;
    }
    
    /**
     * Получить кнопки с действиями для участника
     * @param ProjectMember $member
     * @return string
     */
    protected function getMemberActions($member)
    {
        return $this->widget('application.modules.projects.extensions.MemberActions.MemberActions', 
            $this->getMemberActionsParams($member), true);
    }
    
    /**
     * Получить параметры для создания кнопок управления одной заявкой при кратком отображении
     * @param ProjectMember $member - заявка участника
     * @return array
     */
    protected function getMemberActionsParams($member)
    {
        return array(
            'member' => $member,
        );
    }

    /**
     * Получить статусы записей, с которыми будут извлекаться участники
     * @return array
     * 
     * @todo удалить если так и не понадобится
     * @deprecated
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