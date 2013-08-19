<?php

/**
 * Самый низ письма: стандартная подпись, контакты, ссылки на настройки и отписку
 */
class EMailFooter extends CWidget
{
    /**
     * @var bool - отображать ли блок ссылок "отписаться/мои настройки/веб-версия"
     */
    public $displayLinks = false;
    /**
     * @var string - контактный email
     */
    public $contactEmail = null;
    /**
     * @var string - контактный телефон
     */
    public $contactPhone = null;
    /**
     * @var string - стандартный текст подписи в конце каждого письма
     */
    public $signature = null;
    /**
     * @var string - ссылка для отписки от рассылки
     */
    public $unsubscribeLink = null;
    /**
     * @var string - ссылка на просмотр письма на сайте
     */
    public $webViewLink = null;
    /**
     * @var string - ссылка для настроек
     */
    public $settingsLink = null;
    
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
        $this->render('footer');
    }
    
    /**
     * Отобразить блок ссылок "отписаться/мои настройки/веб-версия"
     *
     * @return null
     * @todo заглушка
     */
    public function displayLinks()
    {
        if ( ! $this->displayLinks )
        {
            return;
        }
        // $this->render('_links');
    }
    
    /**
     * Отобразить контакты внизу письма
     * 
     * @return null
     */
    public function displayContacts()
    {
        if ( ! $this->contactEmail AND ! $this->contactPhone )
        {
            return;
        }
        $this->render('_contacts');
    }
    
    /**
     * Отобразить стандартную подпись внизу письма
     * 
     * @return null
     */
    public function displaySignature()
    {
        if ( ! $this->signature )
        {
            return;
        }
        $this->render('_signature');
    }
}