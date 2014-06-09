<?php

// подключаем базовый класс для шаблонов писем
Yii::import('application.modules.mailComposer.extensions.mails.EMailBase.EMailBase');

/**
 * Письма, отсылаемые при регистрации участникам проекта "топ-модель по-русски"
 */
class EMailTMRegistration extends EMailBase
{
    /**
     * @var Questionary - зарегистрированный, но не подтвержденный участник 
     */
    public $questionary;
    
    /**
     * @see EMailBase::init()
     */
    public function init()
    {
        parent::init();
        $this->mailOptions['target']    = 'user';
        $this->mailOptions['signature'] = 'easyCast существует более 7 лет. За это время мы трудоустроили 
            свыше 25.000 моделей и актеров более чем в 300 проектах.
            Участницам кастинга на «Топ-модель по-русски» мы дарим бесплатный 
            доступ к нашему сервису для поиска оплачиваемых съёмок и прохождению кастингов,
            предоставляемых нашим агентством. ';
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // приветствие, представляемся (если анкету заводит руководитель проектов)
        // и упоминание рекомендации
        $this->addSegment($this->createGreetingBlock());
        // персональные данные
        //$this->addSegment($this->createPersonalDataBlock());
        // кнопка активации
        $this->addSegment($this->createInviteBlock());
        // @todo подробнее о нас (пока не готово)
        // $this->addSegment($this->createMoreInfoBlock());
        // заключение
        //$this->addSegment($this->createConclusionBlock());
        
        // в конце выводим виджет со всеми данными
        parent::run();
    }
    
    /**
     * Получить текст с приветствием
     * @return array
     */
    protected function createGreetingBlock()
    {
        // приветсвие с обращением
        $text  = $this->createUserGreeting($this->questionary);
        $text .= 'Ваша заявка на участие в кастинге пятого сезона реалити-шоу 
            «Топ-модель по-русски» зарегистрирована.';
        return $this->textBlock($text);
    }
        
    /**
     * Получить текст с информацией о персональных данных
     * @return array
     */
    /*protected function createPersonalDataBlock()
    {
        $text  = 'Мы с большим уважением относимся к личным данным участников нашего ресурса и актеров, ';
        $text .= 'чьи интересы мы представляем - поэтому ваши контакты, сумма вашего гонорара, ';
        $text .= 'а также некоторые другие условия съемок ';
        $text .= '<b>никогда не будут видны другим посетителям или участникам ресурса</b>. ';
        $text .= '(Связаться с вами можно только после вашего согласия)';
        
        return $this->textBlock($text);
    }*/
    
    /**
     * Получить текст с кнопкой активации анкеты
     * @return array
     */
    protected function createInviteBlock()
    {
        $text = 'Кастинговое агентство «easyCast» является официальным организатором кастинга 
            моделей для 5го сезона для данного телепроекта.<br>
            Мы получили вашу анкету и в настоящее время она находится на рассмотрении.<br>
            Отбор участниц будет проходить в несколько этапов.<br>
            Мы будем присылать Вам уведомления о статусе вашей заявки.<br>
            Возможно именно Вы станете следующей топ-моделью по-русски!';
        return $this->textBlock($text);
    }
    
    /**
     * Получить текст со ссылкой на более подробную информацию о компании
     * @return array
     */
    protected function createMoreInfoBlock()
    {
        return '';
    }
    
    /**
     * Получить текст с заключительными словами и инструкцией по удалению анкеты
     * @return string
     */
    /*protected function createConclusionBlock()
    {
        $text  = 'Спасибо, что уделили нам время. Мы будем ждать вашего решения.<br>';
        $text .= 'До этого момента ваша анкета будет видна только вам, не будет отображаться в каталоге ';
        $text .= 'или выводиться в поиске. <br>';
        $text .= 'Если вы хотите отказаться от нашего предложения и удалить свою страницу - то можете просто ответить ';
        $text .= 'на это письмо или позвонить по телефону '.Yii::app()->params['userPhone'].'. ';
        $text .= 'Мы удалим ваши данные по первому требованию.';
        
        return $this->textBlock($text);
    }*/
    
    /**
     * Определить, нужно ли отображать информацию о том, кто ввел анкету
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