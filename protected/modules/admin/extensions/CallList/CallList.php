<?php

// подключаем родительский класс (участники проекта)
Yii::import('application.modules.admin.extensions.ProjectMembers.ProjectMembers');

/**
 * Вызывной лист для мероприятия
 * Отображает заголовок (название и дата мероприятия) и список участников с контактами
 */
class CallList extends ProjectMembers
{
    /**
     * @var ProjectEvent - мероприятие для которого формируется вызывной лист
     */
    protected $event;
    
    /**
     * @var отображать ли контакты участников?
     */
    public $showContacts = true;
    
    /**
     * @var string - комментарий к фотовызывному
     */
    public $comment = '';
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->objectType  = 'event';
        $this->displayType = 'members';
        $this->event = ProjectEvent::model()->findByPk($this->objectId);
        
        if ( ! $this->event )
        {
            throw new InvalidArgumentException('Не указано мероприятие для создания вызывного листа');
        }
        
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see ProjectMembers::run()
     */
    public function run()
    {
        parent::run();
        $this->displayComment();
    }
    
    /**
     * (non-PHPdoc)
     * @see ProjectMembers::getMemberColumns()
     */
    protected function getMemberColumns()
    {
        $columns = array();
        $columns[] = array(
            'name'   => 'name',
            'header' => 'ФИО',
            'type'   => 'raw',
            'htmlOptions' => array('style' => 'text-align:center;'),
            'headerHtmlOptions' => array('style' => 'text-align:center;'),
        );
        $columns[] = array(
            'name'   => 'age',
            'header' => 'Возраст',
            'type'   => 'html'
        );
        $columns[] = array(
            'name'   => 'bages',
            'header' => 'Характеристики',
            'type'   => 'html'
        );
        if ( $this->showContacts )
        {// нужно отобразить вызывной с контактами
            $columns[] = array(
                'name'   => 'phone',
                'header' => 'Телефон',
                'type'   => 'html'
            );
            $columns[] = array(
                'name'   => 'email',
                'header' => 'email',
                'type'   => 'html'
            );
        }
        return $columns;
    }
    
    /**
     * (non-PHPdoc)
     * @see ProjectMembers::getMemberData()
     */
    protected function getMemberData($member)
    {
        $element = array();
        $element['id']      = $member->id;
        $element['name']    = $this->getMemberName($member);
        $element['age']     = $member->member->age;
        $element['bages']   = implode(', ', $member->member->getBages());
        if ( $this->showContacts )
        {// нужно отобразить вызывной с контактами
            $element['phone'] = $this->getMemberPhones($member->member);
            $element['email'] = '<a href="mailto:'.$member->member->user->email.'">'.$member->member->user->email.'</a>';
        }
        
        return $element;
    }
    
    /**
     * (non-PHPdoc)
     * @see ProjectMembers::getMemberName()
     */
    protected function getMemberName($member)
    {
        $memberLink  = '<h4>'.parent::getMemberName($member).'</h4>';
        $memberImageUrl = $member->member->getAvatarUrl('medium');
        $memberImage = CHtml::image($memberImageUrl, CHtml::encode($member->member->fullname),
            array('style'=> 'width:400px;'));
        
        return $memberLink.'<br>'.$memberImage;
    }
    
    /**
     * Получить все контактные телефоны участника
     * @param Questionary $questionary
     * @return string
     */
    protected function getMemberPhones($questionary)
    {
        $result = '<i>Не указан</i>';
        $phones = array();
        
        if ( $questionary->mobilephone )
        {
            $phones[] = 'моб.: '.$questionary->mobilephone;
        }
        if ( $questionary->homephone )
        {
            $phones[] = 'дом.: '.$questionary->homephone;
        }
        if ( $questionary->addphone )
        {
            $phones[] = 'доп.: '.$questionary->addphone;
        }
        if ( ! empty($phones) )
        {
            $result = implode('<br>', $phones);
        }
        
        return $result;
    }
    
    /**
     * Создать заголовок вызывного листа
     * @param ProjectEvent $event
     * @return string
     */
    protected function getEventHeader($event)
    {
        $date = $this->event->getFormattedTimePeriod();
        $name = CHtml::encode($event->name);
        if ( $event->type != ProjectEvent::TYPE_EVENT )
        {// выведем тип мероприятия, если он указан
            $name .= ' ('.$event->getTypeLabel().')';
        }
        
        $header  = '<h2>Вызывной лист на '.$date.'</h2>';
        $header .= '<h4>'.$name.'</h4>';
        
        return $header;
    }
    
    /**
     * Отобразить комментарий к фотовызывному (если есть)
     * @return string
     */
    protected function displayComment()
    {
        if ( trim(strip_tags($this->comment)) )
        {
            echo '<h4>Комментарий:</h4>';
            echo $this->comment;
        }
    }
}