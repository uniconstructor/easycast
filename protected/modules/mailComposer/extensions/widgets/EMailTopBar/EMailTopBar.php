<?php

/**
 * Верхняя синенькая полукруглая полоска со ссылками и кнопками соцсетей
 * @todo прописать функционал всех дополнительных ссылок и кнопок
 */
class EMailTopBar extends CWidget
{
    /**
     * @var bool - отображать ли блок ссылок "отписаться/мои настройки/веб-версия"
     */
    public $displayLinks = true;
    /**
     * @var bool - отображать ли кнопки соцсетей
     */
    public $displaySocial = false;
    /**
     * @var bool - отображать ли ссылку "отписаться"
     */
    public $displayUnsubscribe = false;
    /**
     * @var bool - отображать ли ссылку "посмотреть письмо на сайте"
     */
    public $displayWebView = false;
    /**
     * @var bool - отображать ли ссылку "мои настройки"
     */
    public $displaySettings = false;
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
     * @var string - ссылка для like на facebook
     */
    public $fbLikeLink = null;
    /**
     * @var string - ссылка для нового твита
     */
    public $twitterLikeLink = null;
    
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
        $this->render('bar');
    }
    
    /**
     * Отобразить блок соцсетей 
     * 
     * @return null
     * @todo заглушка
     */
    public function displaySocial()
    {
        if ( ! $this->displaySocial )
        {
            return false;
        }
        //$this->render('_social');
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
            return false;
        }
        $this->render('_links');
    }
}