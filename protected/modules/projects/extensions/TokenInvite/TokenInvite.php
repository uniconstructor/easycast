<?php

/**
 * Класс для отображения списка доступных вакансий пользователя, для слчаев когда он
 * подает заявку по одноразовой ссылке 
 * 
 * @todo добавить в init() проверку ключа
 * @todo добавить отображение оплаты
 */
class TokenInvite extends CWidget
{
    /**
     * @var EventInvite - приглашение для которого отрисовывается виджет
     */
    public $invite;
    /**
     * @var Questionary - анкета для которой отображается приглашение
     */
    public $questionary;
    /**
     * @var ProjectEvent - мероприятие на которое пришло приглашение
     */
    public $event;
    /**
     * @var string - ключ подтверждения, переданный по ссылке
     */
    public $key;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! ($this->invite instanceof EventInvite) )
        {
            throw new CException('Не передано приглашения для виджета TokenInvite');
        }
        $this->event       = $this->invite->event;
        $this->questionary = $this->invite->questionary;
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->displayEvent($this->event);
    }
    
    /**
     * Отобразить информацию о группе мероприятий
     * 
     * @return null
     * 
     * @deprecated группы больше не используются - удалить при рефакторинге
     */
    protected function displayGroup()
    {
        // при отображении группы событий сначала отображаем вакансии
        $this->displayVacancies($this->event);
        
        foreach ( $this->event->events as $event )
        {// отображаем информацию по каждому мероприятию из группы
            $this->displayEvent($event);
        }
    }
    
    /**
     * Отобразить информацию по одному мероприятию
     * 
     * @param  ProjectEvent $event
     * @return null
     */
    protected function displayEvent($event)
    {
        $eventHeading  = $event->name.' ';
        $eventHeading .= $event->getFormattedTimePeriod();
        
        echo '<h3>'.$eventHeading.'</h3>';
        
        $this->displayVacancies($event);
    }
    
    /**
     * Отобразить список доступных участнику вакансий
     * 
     * @param ProjectEvent $event
     * @return null
     * 
     * @todo выбрать вакансии по таблице {{invite_vacancies}} через relations
     * @todo давать ссылку на другие роли если набор завершен
     */
    protected function displayVacancies($event)
    {
        $vacancies = array();
        if ( ! $availableVacancies = $this->event->getAllowedVacancies($this->invite->questionaryid) )
        {// нет доступных вакансий для отображения
            echo $this->getInfoMessage('Необходимое количество участников уже набрано', 
                'Прием заявок завершен', 'alert alert-info');
            return;
        }
        
        if ( $event->type === ProjectEvent::TYPE_GROUP )
        {// отображаем вакансии для группы событий - нужен другой заголовок и дополнительное пояснение
            echo '<h4>Роли на весь период съемок</h4>';
            echo '<div class="alert">Отправляя заявку на одну из этих ролей вам
                нужно будет присутствовать на всех мероприятиях, перечисленных ниже.
                Оплата производится за каждый съемочный день.</div>';
        }else
        {// отображаем информацию об одном событии
            echo '<h4>Предлагаемые роли</h4>';
            if ( count($availableVacancies) > 1 )
            {
                echo '<div class="alert alert-info">Вы можете подать несколько заявок одновременно.</div>';
            }
        }
        foreach ( $availableVacancies as $vacancy )
        {// перебираем все доступные участнику вакансии и составляем массив для таблицы
            /* @var $vacancy EventVacancy */ 
            $this->widget('projects.extensions.VacancyInfo.VacancyInfo', array(
                'vacancy'             => $vacancy,
                'questionary'         => $this->invite->questionary,
                'displayNotAvailable' => false,
                'isAjaxRequest'       => false,
                'isAvailable'         => $vacancy->isAvailableForUser($this->invite->questionary->id, true),
                'buttonSize'          => 'large',
                'key'                 => $this->key,
                'invite'              => $this->invite,
                'inviteMode'          => 'token',
            ));
        }
    }
    
    /**
     * Получить HTML-код кнопок подписки и отписки
     * 
     * @param EventVacancy $vacancy - вакансия для которой создается кнопка
     * @return string - html-код кнопок
     */
    protected function createActionButtons($vacancy)
    {
        return $this->widget('projects.extensions.VacancyActions.VacancyActions', array(
            'vacancy' => $vacancy,
            'mode'    => 'token',
            'invite'  => $this->invite,
            'key'     => $this->key,
        ), true);
    }
    
    /**
     * Получить html-код сообщения о результате операции (подана заявка, время истекло и т.п.)
     * 
     * @param string $message
     * @param string $header
     * @param string $class
     * @return string
     */
    protected function getInfoMessage($message, $header='', $class='alert alert-block')
    {
        $result  = '';
        $result .= '<div class="'.$class.'" style="text-align:center;">';
        if ( $header )
        {
            $header = '<h4 class="alert-heading">'.$header.'</h4>';
            $result .= $header;
        }
        $result .= $message.'</div>';
    
        return $result;
    }
}