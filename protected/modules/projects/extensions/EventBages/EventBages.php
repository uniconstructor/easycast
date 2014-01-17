<?php

/**
 * Виджет для вывода важных свойств события (тип/статус/есть ли доступные роли, и т. д.)
 * 
 * @todo добавить tooltip-подсказку к каждому свойству, с кратким описанием для пользователя
 * @todo добавить метку и пояснение для группы событий
 * @todo для гостей сделать кнопку "смогу ли я участвовать?"
 */
class EventBages extends CWidget
{
    /**
     * @var bool - отображать тип проекта, к которому относится мероприятие
     */
    public $displayProjectType   = false;
    /**
     * @var bool - отображать тип мероприятия
     */
    public $displayType          = true;
    /**
     * @var bool - отображать надпись "онлайн-кастинг" (если событие является онлайн-кастингом)
     */
    public $displayCasting       = true;
    /**
     * @var bool - отображать надпись "идет набор" (для активных мероприятий)
     */
    public $displayActive        = true;
    /**
     * @var bool - отображать надпись "завершено" (для завершенных мероприятий) 
     *             или "набор завершен" (для активных мероприятий с полностью закрытыми ролями)
     */
    public $displayFinished      = true;
    /**
     * @var bool - отображать надпись "есть роли"
     */
    public $displayAvailable     = false;
    /**
     * @var bool - отображать "нет доступных ролей"
     */
    public $displayNotAvailable  = false;
    /**
     * @var bool - отображать надпись "вы участвуете"
     */
    public $displayParticipition = true;
    /**
     * @var string - разделитель, вставляется после каждой метки
     */
    public $spacer = '&nbsp;';
    
    /**
     * @var ProjectEvent - мероприятие для которого отображаются свойства
     */
    protected $event;
    /**
     * @var array - список свойств для отображения (пополняется в процессе работы виджета)
     */
    protected $bages = array();
    /**
     * @var Questionary - участник для которого отображаются свойства события 
     */
    protected $questionary;
    
    /**
     * @param ProjectEvenr $event
     * @return void
     */
    public function setEvent($event)
    {
        if ( $event instanceof ProjectEvent )
        {
            $this->event = $event;
        }
    }
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        
        if ( Yii::app()->user->isGuest )
        {// гость
            $this->questionary = null;
        }else
        {// обычный пользователь: загружаем его данные
            $this->questionary = Yii::app()->getModule('questionary')->getCurrentQuestionary();
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->event )
        {// ошибка: нет события для которого отображаются свойства
            ECDebug::handleError('Не передано событие для отображения свойств');
            return;
        }
        
        $this->getType();
        $this->getProjectType();
        $this->getCasting();
        $this->getActive();
        $this->getFinished();
        $this->getAvailable();
        $this->getNotAvailable();
        $this->getParticipition();
        
        echo implode($this->spacer, $this->bages);
    }
    
    /**
     * Определить, является ли текущий участник участником просматриваемого мероприятия
     * @return bool
     */
    protected function isMember()
    {
        if ( ! $this->questionary OR Yii::app()->user->checkAccess('Admin') )
        {
            return false;
        }
        if ( $this->event->hasMember($this->questionary->id) )
        {
            return true;
        }
        return false;
    }
    
    /**
     * Определить, есть ли для текущего пользователя доступные роли
     * @return bool
     */
    protected function hasVacancies()
    {
        if ( ! $this->questionary OR Yii::app()->user->checkAccess('Admin') )
        {
            return false;
        }
        return $this->event->hasVacanciesFor($this->questionary->id);
    }
    
    /**
     * Получить html-код одной метки
     * @param string $type
     * @param string $label
     * @return string
     */
    protected function getBage($type, $label)
    {
        return $this->widget('bootstrap.widgets.TbBadge', array(
            'type'  => $type,
            'label' => $label,
        ), true);
    }
    
    /**
     * Получить метку с типом события
     * @return void
     */
    protected function getType()
    {
        if ( ! $this->displayType )
        {
            return;
        }
        if ( $this->event->type === ProjectEvent::TYPE_EVENT OR $this->event->type === ProjectEvent::TYPE_GROUP )
        {// не отображаем служебные типы событий
            return;
        }
        $this->bages['type'] = $this->getBage('info', $this->event->getTypeLabel());
    }
    
    /**
     * Получить метку с типом проекта
     * @return void
     */
    protected function getProjectType()
    {
        if ( ! $this->displayProjectType )
        {
            return;
        }
        if ( $this->event->project->type != Project::TYPE_PROJECT )
        {// не отображаем служебные типы проекта
            return;
        }
        $this->bages['projectType'] = $this->getBage('info', $this->event->getTypeLabel());
    }
    
    /**
     * Получить метку "онлайн-кастинг"
     * @return void
     */
    protected function getCasting()
    {
        if ( ! $this->displayCasting )
        {
            return;
        }
        if ( $this->event->virtual )
        {
            $this->bages['casting'] = $this->getBage('inverse', 'Онлайн-кастинг');
        }
    }
    
    /**
     * Получить метку "идет набор"
     * @return void
     */
    protected function getActive()
    {
        if ( ! $this->displayActive )
        {
            return;
        }
        if ( $this->isMember() )
        {// если пользователь уже участвует - то ему не важно, идет набор или нет
            return;
        }
        if ( $this->event->status === ProjectEvent::STATUS_ACTIVE )
        {
            $this->bages['active'] = $this->getBage('success', 'Идет набор');
        }
    }
    
    /**
     * Получить метку "завершено"
     * @return void
     */
    protected function getFinished()
    {
        if ( ! $this->displayFinished )
        {
            return;
        }
        if ( $this->event->status === ProjectEvent::STATUS_FINISHED )
        {
            $this->bages['finished'] = $this->getBage('default', 'Завершено');
        }
    }

    /**
     * Получить метку "есть доступные роли"
     * @return void
     */
    protected function getAvailable()
    {
        if ( ! $this->displayAvailable )
        {
            return;
        }
        if ( Yii::app()->user->isGuest )
        {// если зашел гость - не обманываем его и не показываем ему метку "Вы можете участвовать"
            return;
        }
        if ( $this->isMember() )
        {// если пользователь уже участвует в событии - то неважно, есть для него роли или нет - он уже одобрен
            return;
        }
        
        if ( $this->hasVacancies() )
        {
            $this->bages['available'] = $this->getBage('warning', 'Вы можете участвовать');
        }
    }
    
    /**
     * Получить метку "нет доступных ролей"
     * @return void
     */
    protected function getNotAvailable()
    {
        if ( ! $this->displayNotAvailable )
        {
            return;
        }
        if ( $this->isMember() )
        {// если пользователь уже участвует в событии - то неважно, есть для него роли или нет
            return;
        }
        if ( ! $this->hasVacancies() )
        {
            $this->bages['notAvailable'] = $this->getBage('inverse', 'Нет доступных ролей');
        }
    }
    
    /**
     * Получить метку "вы участвуете"
     * @return void
     */
    protected function getParticipition()
    {
        if ( ! $this->displayParticipition )
        {
            return;
        }
        if ( $this->event->status === ProjectEvent::STATUS_FINISHED )
        {// если событие завершено - то не показываем надпись "вы участвуете"
            return;
        }
        if ( $this->isMember() )
        {
            $this->bages['participition'] = $this->getBage('success', 'Вы участвуете');
        }
    }
}