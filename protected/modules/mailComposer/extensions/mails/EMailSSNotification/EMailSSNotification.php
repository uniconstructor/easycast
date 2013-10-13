<?php

// подключаем базовый класс для шаблонов писем
Yii::import('application.modules.mailComposer.extensions.mails.EMailBase.EMailBase');

/**
 * Письма, отсылаемые при регистрации актерам из базы Светланы Строиловой
 * Языковые строки здесь не понадобятся, т. к. операция одноразовая
 */
class EMailSSNotification extends EMailBase
{
    /**
     * @var bool - подставлять ли данные об администраторе автоматически?
     */
    const USE_DEFAULT_MANAGER = false;
    
    /**
     * @var Questionary - зарегистрированный, но не подтвержденный участник 
     */
    public $questionary;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        // приветствие, представляемся (если анкету заводит руководитель проектов)
        // и упоминание рекомендации
        $this->addSegment($this->createGreetingBlock());
        // о кастинговом агентстве, предложение, ссылка на анкету
        $this->addSegment($this->createAboutBlock());
        // персональные данные
        $this->addSegment($this->createPersonalDataBlock());
        // кнопка активации
        $this->addSegment($this->createActivationButtonBlock());
        // @todo подробнее о нас (пока не готово)
        // $this->addSegment($this->createMoreInfoBlock());
        // заключение
        $this->addSegment($this->createConclusionBlock());
        
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
        // меня зовут... (представляемся или ничего не пишем)
        $text .= $this->createManagerInfo();
        // по чьей рекомендации связываемся
        $text .= $this->createRecommendationInfo();
        
        return $this->textBlock($text);
    }
    
    /**
     * Получить текст с информацией о том, кто ввел анкету
     * @return string
     */
    protected function createManagerInfo()
    {
        $text = '';
        $manager = $this->questionary->getQuestionaryAuthor();
        if ( ! $manager )
        {// не найден сотрудник, вводивший анкету
            if ( self::USE_DEFAULT_MANAGER )
            {
                $manager = $this->getDefaultManager();
            }else
            {
                return '';
            }
        }
        if ( ! $this->displayManagerInfo($manager) )
        {
            return '';
        }
        return "Меня зовут {$manager->questionary->firstname}, я руководитель проектов кастингового агентства easyCast.<br><br>";
    }
    
    /**
     * Получить текст с описанием рекомендации
     * @return string
     */
    protected function createRecommendationInfo()
    {
        $projectsLink = CHtml::link('наших проектов', 'http://easycast.ru/projects');
        return 'Мы связались с вами по рекомендации кастинг-директора Светланы Строиловой для того чтобы
            предложить вам принять участие в съемках '.$projectsLink.'.';
    }
    
    /**
     * Получить текст с информацией о компании и предложением участия
     * @return array
     */
    protected function createAboutBlock()
    {
        $siteLink = CHtml::link('easycast.ru', 'http://easycast.ru');
        $pageLink = CHtml::link('персональную страницу', 'http://easycast.ru/questionary/questionary/view/id/'.$this->questionary->id);
        
        $text  = 'Наше кастинговое агентство существует более 7 лет. ';
        $text .= 'За это время мы трудоустроили свыше 25.000 актеров более чем в 300 проектах.<br><br>';
        
        $text .= 'К концу 2013 года мы запускаем '.$siteLink.' - первый автоматизированный ресурс для поиска актеров, '; 
        $text .= 'моделей и типажей на территории Москвы.<br><br>';

        $text .= 'Мы предлагаем вам использовать удобный и эффективный инструмент для поиска оплачиваемых съемок ';
        $text .= 'и прохождению кастингов и дарим вам бесплатный доступ к нашему сервису на все время пользования.<br><br>';
        
        $text .= 'Изучив ваш профессиональный опыт и внешность, мы приняли решение ';
        $text .= 'создать вам '.$pageLink.' на нашем ресурсе.<br>';
        //$text .= 'а также предоставить бесплатный доступ к сервису на все время использования.<br><br>';

        $text .= 'Обращаем ваше внимание на то, что наш ресурс уже работает, но основной объем предложений '; 
        $text .= 'начнет поступать вам с момента официального запуска системы (ноябрь-декабрь 2013). ';
        $text .= 'Именно тогда наша компания полностью перейдет на реализацию всего цикла задач по проведению кастингов и подбора актеров на базе нашего нового ресурса.';
        
        return $this->textBlock($text);
    }
    
    /**
     * Получить текст с информацией о персональных данных
     * @return array
     */
    protected function createPersonalDataBlock()
    {
        $text  = 'Мы с большим уважением относимся к личным данным участников нашего ресурса и актеров, ';
        $text .= 'чьи интересы мы представляем - поэтому ваши контакты, сумма вашего гонорара, ';
        $text .= 'а также некоторые другие условия съемок ';
        $text .= '<b>никогда не будут видны другим посетителям или участникам ресурса</b>. ';
        $text .= 'Даже тот кто приглашает вас на съемку сможет связаться с вами только после вашего согласия.';
        
        return $this->textBlock($text);
    }
    
    /**
     * Получить текст с кнопкой активации анкеты
     * @return array
     */
    protected function createActivationButtonBlock()
    {
        $block  = array();
        $button = array();
        // ссылка на активацию анкеты
        $url = Yii::app()->createAbsoluteUrl('/questionary/questionary/userActivation',
            array(
                'id' => $this->questionary->id,
                'key' => $this->questionary->user->activkey,
            ));
        // сама кнопка
        $button['caption'] = 'Начать работу';
        $button['link']    = $url;
        $button['description'] = 'Нажмите сюда, чтобы добавить свою анкету в каталог, отредактировать информацию о себе или подать заявку на участие в съемках';
        // добавляем кнопку в блок
        $block['button'] = $button;
        
        return $block;
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
    protected function createConclusionBlock()
    {
        $text  = 'Спасибо, что уделили нам время. Мы будем ждать вашего решения.<br>';
        $text .= 'До этого момента ваша анкета будет видна только вам, не будет отображаться в каталоге ';
        $text .= 'или выводиться в поиске. <br>';
        $text .= 'Если вы хотите отказаться от нашего предложения и удалить свою страницу - то можете просто ответить ';
        $text .= 'на это письмо или позвонить по телефону '.Yii::app()->params['adminPhone'].'. ';
        $text .= 'Мы удалим ваши данные по первому требованию.';
        
        return $this->textBlock($text);
    }
    
    /**
     * Определить, нужно ли отображать информацию о том, кто ввел анкету
     * @param User $user
     * @return bool
     */
    protected function displayManagerInfo($user)
    {
        if ( ! trim($user->questionary->firstname) )
        {// не указано имя - не можем ничего написать
            return false;
        }
        if ( in_array($user->id, array(16, 92, 775, 831)) )
        {// пользователь не является руководителем проектов
            return true;
        }
        return false;
    }
    
    /**
     * Получить "администратора по умолчанию" - того, кем мы представляемся, если написать
     * имя и контакты того кто ввел анкету нельзя
     * @return User
     * 
     * @todo заглушка, дописать позже
     */
    protected function getDefaultManager()
    {
        
    }
}