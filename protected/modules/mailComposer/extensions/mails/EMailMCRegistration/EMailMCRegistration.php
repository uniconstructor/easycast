<?php

// подключаем базовый класс для шаблонов писем
Yii::import('application.modules.mailComposer.extensions.mails.EMailBase.EMailBase');

/**
 * Письма, отсылаемые при регистрации участникам проекта "топ-модель по-русски"
 */
class EMailMCRegistration extends EMailBase
{
    /**
     * @var Questionary - зарегистрированный, но не подтвержденный участник 
     */
    public $questionary;
    /**
     * @var EventVacancy
     */
    public $vacancy;
    /**
     * @var string
     */
    public $password;
    
    /**
     * @see EMailBase::init()
     */
    public function init()
    {
        parent::init();
        $this->mailOptions['target']    = 'user';
        $this->mailOptions['signature'] = 'easyCast существует более 7 лет. За это время мы трудоустроили 
            свыше 25.000 моделей и актеров более чем в 300 проектах. ';
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // приветствие, представляемся (если анкету заводит руководитель проектов)
        // и упоминание рекомендации
        $this->addSegment($this->createGreetingBlock());
        // @todo подробнее о нас (пока не готово)
        if ( $this->password )
        {// на всякий случай: напоминание про логин и пароль добавляем только 
            // если есть что отправлять 
            $this->createMoreInfoBlock();
        }
        $this->addSegment($this->createMoreInfoBlock());
        // кнопка активации
        $this->addSegment($this->createInviteBlock());
        
        // заключение
        //$this->addSegment($this->createConclusionBlock());
        
        // в конце выводим виджет со всеми данными
        parent::run();
    }
    
    /**
     * Получить текст с приветствием
     * 
     * @return array
     */
    protected function createGreetingBlock()
    {
        // приветсвие с обращением
        $text  = $this->createUserGreeting($this->questionary);
        $text .= 'Ваша заявка на участие в кастинге проекта  
            «'.$this->vacancy->event->project->name.'» зарегистрирована 
            в системе агентства «easyCast», которое является официальным 
                организатором кастинга для проекта  «'.$this->vacancy->event->project->name.'». ';
        return $this->textBlock($text);
    }
        
    /**
     * Получить текст с кнопкой активации анкеты
     * 
     * @return array
     */
    protected function createInviteBlock()
    {
        $text = 'В настоящее время Ваша заявка находится на рассмотрении.
            Отбор участников будет проходить в несколько этапов.<br>
            Мы будем уведомлять вас об изменении статусе вашей заявки.<br>
            (Это автоматическое уведомление, отвечать на него не нужно) ';
        return $this->textBlock($text);
    }
    
    /**
     * Получить текст со ссылкой на более подробную информацию о компании
     * 
     * @return array
     */
    protected function createMoreInfoBlock()
    {
        $text = 'Для того чтобы изменить или дополнить данные в своей анкете используйте эти данные:<br>
            Логин:<b>'.$this->questionary->user->email.'</b><br>
            Пароль:<b>'.$this->password.'</b><br>
            <br>';
        return $this->textBlock($text);
    }
    
    /**
     * Определить, нужно ли отображать информацию о том, кто ввел анкету
     * 
     * @param User $user
     * @return bool
     */
    protected function displayManagerInfo($user)
    {
        /*if ( ! trim($user->questionary->firstname) )
        {// не указано имя - не можем ничего написать
            return false;
        }
        if ( in_array($user->id, array(16, 92, 775, 831)) )
        {// пользователь не является руководителем проектов
            return true;
        }*/
        return false;
    }
}