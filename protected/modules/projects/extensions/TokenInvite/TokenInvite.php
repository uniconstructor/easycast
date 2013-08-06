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
     * @var ProjectEvent - мероприятие на которое пришло приглашение
     */
    public $event;
    
    /**
     * @var string - ключ подтверждения, переданный по ссылке
     */
    public $key;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->invite )
        {
            throw new CException('Не передано приглашения для виджета TokenInvite');
        }
        $this->event = $this->invite->event;
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->event->type == ProjectEvent::TYPE_GROUP )
        {
            $this->displayGroup();
        }else
        {
            $this->displayEvent($this->event);
        }
    }
    
    /**
     * Отобразить информацию о группе мероприятий
     * 
     * @return null
     * 
     * @todo писать даты начала и окончания всей серии мероприятий
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
     * @param ProjectEvent $event
     *
     * @return null
     * 
     * @todo определить, когда писать "вакансии", а когда "роли"
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
     * @param ProjectEvent $event
     * 
     * @return null
     * 
     * @todo выбрать вакансии по таблице {{invite_vacancies}} через relations
     */
    protected function displayVacancies($event)
    {
        $vacancies = array();
        if ( ! $availableVacancies = $this->event->getAllowedVacancies($this->invite->questionaryid) )
        {// нет доступных вакансий для отображения
            return '';
        }
        
        if ( $event->type == ProjectEvent::TYPE_GROUP )
        {// отображаем вакансии для группы событий - нужен другой заголовок и дополнительное пояснение
            echo '<h4>Роли на весь период съемок</h4>';
            echo '<div class="alert">Отправляя заявку на одну из этих позиций вам
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
            $element = array();
            $element['id']   = $vacancy->id;
            $element['name'] = $vacancy->name;
            $element['description'] = $vacancy->description;
            $element['salary']   = $vacancy->salary;
            $element['actions'] = $this->createActionButtons($vacancy);
            
            $vacancies[] = $element;
        }
        $vacanciesList = new CArrayDataProvider($vacancies, array(
            'pagination' => false,
        ));
        
        // выводим таблицу с доступными вакансиями
        $this->widget('bootstrap.widgets.TbGridView', array(
            'type'         => 'striped bordered condensed',
            'dataProvider' => $vacanciesList,
            'template'     => "{items}{pager}",
            'columns' => array(
                array(// столбец с названием вакансии
                    'name'   => 'name',
                    'type'   => 'html',
                    'header' => 'Роль',//Yii::t('coreMessages', 'name'),
                ),
                array(// столбец с описанием вакансии
                    'name'   => 'description',
                    'header' => Yii::t('coreMessages', 'description'),
                    'type'   => 'html',
                ),
                array(// количество человек
                    'name'   => 'salary',
                    'header' => 'Оплата за съемочный день',
                    'value'  => '$data["salary"]." р."',
                ),
                array(// действия (подать/отозвать заявку)
                    'name'   => 'actions',
                    'type'   => 'raw',
                    'header' => 'Действия',//Yii::t('coreMessages', 'actions'),
                ),
            ),
        ));
    }
    
    /**
     * Получить HTML-код кнопок подписки и отписки
     * @param EventVacancy $vacancy - вакансия для которой создается кнопка
     * @return string - html-код кнопок
     */
    protected function createActionButtons($vacancy)
    {
        return $this->widget('application.modules.projects.extensions.VacancyActions.VacancyActions', 
            array(
                'vacancy' => $vacancy,
                'mode'    => 'token',
                'invite'  => $this->invite,
                'key'     => $this->key,
        ), true);
    }
    
    /**
     * Создать кнопку подачи заявки на вакансию
     * @param EventVacancy $vacancy - вакансия, для которой создается кнопка
     * 
     * @return string - html-код для ajax-кнопки подачи заявки по одноразовому токену
     */
    protected function createAddApplicationButton($vacancy)
    {
        return '(подать заявку)';
    }
    
    /**
     * Создать кнопку отмены заявки на вакансию
     * @param EventVacancy $vacancy - вакансия, для которой создается кнопка
     *
     * @return string - html-код для ajax-кнопки отмены заявки по одноразовому токену
     */
    protected function createCancelApplicationButton($vacancy)
    {
        return '';
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
        $result = '';
    
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