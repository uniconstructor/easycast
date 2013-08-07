<?php

/**
 * Класс для выполнения cron-команд (заданий по расписанию) из консоли
 * Все регулярные задачи easycast должны быть собраны здесь
 */
class CronCommand extends CConsoleCommand
{
    /**
     * @var EasyCastAmazonAPI - наш маленький API для работы с сервисами Amazon :)
     */
    protected $ecawsapi;
    
    /**
     * @var int - сколько пакетов email-сообщений отсылать за 1 запуск крона (1 пакет = 5 сообщений)
     */
    const MAIL_PACKAGES_COUNT = 5;
    
    /**
     * (non-PHPdoc)
     * @see CConsoleCommand::init()
     */
    public function init()
    {
        // поскольку почти все cron-команды используют обращение к Amazon, то сразу же подключим наш API
        $this->ecawsapi = Yii::app()->getComponent('ecawsapi');
        $this->ecawsapi->trace = true;
        
        parent::init();
    }
    /**
     * (non-PHPdoc)
     * @see CConsoleCommand::run()
     * 
     * Главная функция для запуска всех консольных команд
     */
    public function run()
    {
        echo "Wellcome back, Commander. Starting cron for you...\n\n";
        
        // отправляем запланированную почту
        $this->actionSendMail();
        // загружаем картинки пользователей на Amazon S3
        //$this->actionUploadImages()
        
        // для экспериментов
        //$this->actionTest();
        
        echo "Cron finished, waiting orders.\n";
    }
    
    /**
     * Отправляет часть накопившейся почты, учитывая ограничения хостинга
     * 
     * @return null
     */
    public function actionSendMail($mailPackegesCount=self::MAIL_PACKAGES_COUNT)
    {
        echo "Sending email...\n";
        if ( $this->ecawsapi->emailQueueIsEmpty() )
        {// очередь сообщений пуста - ничего не нужно отправлять
            echo "Queue empty.\n";
            return 0;
        }
        for ( $i = 0; $i < $mailPackegesCount; $i++ )
        {// отправляем по несколько пакетов писем за 1 запуск крона (1 пакет = 5 писем)
            $this->ecawsapi->processEmailQueue();
            if ( $this->ecawsapi->emailQueueIsEmpty() )
            {// все сообщения отправлены
                break;
            }
        }
        // в конце выводим статистику, сколько осталось
        $this->ecawsapi->showEmailQueryInfo();
        
        echo "Done.\n\n";
        return 0;
    }
    
    /**
     * Загружает картинки пользователей на AmazonS3
     * 
     * @return null
     */
    public function actionUploadImages()
    {
        echo "Uploading images...\n";
        
        
        
        echo "Done.\n\n";
        return 0;
    }
    
    /**
     * Просто тестовая команда
     * (Иногда используется для проверки новых библиотек и технологий)
     * 
     */
    public function actionTest()
    {
        Yii::import('application.modules.projects.models.*');
        
        /*$invite = new EventInvite;
        $invite->questionaryid = 1;
        $invite->eventid = 124;
        $invite->save();*/
        
        //$vacancy = EventVacancy::model()->findByPk(17);
        //$vacancy->sendInvites();
        
        $this->ecawsapi->processEmailQueue(1);
        $this->ecawsapi->showEmailQueryInfo();
    }
}