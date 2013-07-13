<?php
/**
 * Виджет, отображающий шапку страницы
 */
class ECHeader extends CWidget
{
    /**
     * @var string - ссылка на папку с ресурсами расширения
     */
    protected $_assetUrl;
    
    /**
     * Подготавливает виджет к запуску
     */
    public function init()
    {
        // Загружаем стили шапки страницы
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::app()->extensionPath . DIRECTORY_SEPARATOR . 
            'ECMarkup' . DIRECTORY_SEPARATOR .
            'ECHeader' . DIRECTORY_SEPARATOR . 
            'assets'   . DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/css/header.css');
    }
    /**
     * Отображает шапку страницы
     */
    public function run()
    {
        // Начало шапки страницы
        echo '<div id="header" class="container easycast-header">';
        // Используем стили Twitter Bootstrap для того чтобы сделать резиновую верстку блоков в заголовке
        echo '<div class="row-fluid show-grid">';
        // Список контактов в левом верхнем углу
        $this->printContacts();
        // Логотип
        $this->printLogo();
        // Вход
        $this->printLoginTool();
        // Социальные сети
        $this->printInformer();
        // конец блока резиновой верстки
        echo '</div>';
        // Горизонтальная полоска
        echo '<hr size="2" noshade style="border-color:white;margin-top:1px;margin-bottom:5px;" class="container">';
        // Конец блока с заголовком
        echo '</div><!-- header -->';
    }

    /**
     * Вывести виджет со списком социальных сетей
     *        
     * @deprecated удалить при рефакторинге
     */
    public function printSocialBlock()
    {
        echo '<div class="span2">';
        if ( ! YII_DEBUG )
        {// скрываем социальный блок в режиме разработчика 
            echo "<div class='pluso pluso-theme-dark pluso-multiline'><div class='pluso-more-container'><a class='pluso-more' href=''></a></div><a class='pluso-twitter'></a><a class='pluso-vkontakte'></a><a class='pluso-facebook'></a><br><a class='pluso-odnoklassniki'></a><a class='pluso-google'></a><a class='pluso-moimir'></a></div>
                <script type='text/javascript'>if(!window.pluso){pluso={version:'0.9.1',url:'http://share.pluso.ru/'};h=document.getElementsByTagName('head')[0];l=document.createElement('link');l.href=pluso.url+'pluso.css';l.type='text/css';l.rel='stylesheet';s=document.createElement('script');s.src=pluso.url+'pluso.js';s.charset='UTF-8';h.appendChild(l);h.appendChild(s)}</script>";
        }
        echo '</div>';
    }

    /**
     * Отображает виджет входа на сайт, который для зарегистрированых пользователей отображает
     * их фотографию
     */
    public function printLoginTool()
    {
        echo '<div class="span2 easycast-menu-item text-center">';
        $this->widget('application.extensions.ECMarkup.ECLoginWidget.ECLoginWidget');
        echo '</div>';
    }

    /**
     * Вывести главный логотип со ссылкой
     */
    protected function printLogo()
    {
        echo '<div class="span4 offset2">';
        echo '<div id="logo" class="easycast-logo">';
        echo CHtml::link(CHtml::image($this->_assetUrl.'/images/logo.png').'', Yii::app()->getBaseUrl(true));
        echo '</div>';
        echo '</div>';
    }

    /**
     * Вывести блок с контактами
     */
    public function printContacts()
    {
        echo '<div class="span2">';
        $this->widget('application.extensions.ECMarkup.ECContacts.ECContacts');
        echo '</div>';
    }
    
    /**
     * Добавляет в правый верхний угол виджет с информацией для пользователя
     * 
     * @return null
     */
    protected function printInformer()
    {
        $this->widget('application.extensions.ECMarkup.ECMainInformer.ECMainInformer');
    }
}