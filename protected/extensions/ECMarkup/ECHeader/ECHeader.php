<?php
/**
 * Виджет, отображающий шапку страницы
 */
class ECHeader extends CWidget
{
    public $displayContacts = true;
    
    public $displayLogo = true;
    
    public $displayloginTool = true;
    
    public $displayInformer = true;
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
     * Отображает виджет входа на сайт, который для зарегистрированых пользователей отображает
     * их фотографию
     */
    public function printLoginTool()
    {
        echo '<div class="span2 easycast-menu-item text-center">';
        if ( $this->displayloginTool )
        {
            $this->widget('application.extensions.ECMarkup.ECLoginWidget.ECLoginWidget');
        }
        echo '</div>';
    }

    /**
     * Вывести главный логотип со ссылкой
     */
    protected function printLogo()
    {
        if ( ! $this->displayContacts AND ! $this->displayloginTool AND ! $this->displayInformer )
        {
            echo '<div class="span6 offset3">';
        }else
        {
            echo '<div class="span4 offset2">';
        }
        echo '<div id="logo" class="easycast-logo">';
        echo CHtml::link(CHtml::image('/images/logo-75x330.png').'', Yii::app()->getBaseUrl(true));
        echo '</div>';
        echo '</div>';
    }

    /**
     * Вывести блок с контактами
     */
    public function printContacts()
    {
        if ( $this->displayContacts )
        {
            echo '<div class="span2">';
            $this->widget('application.extensions.ECMarkup.ECContacts.ECContacts');
            echo '</div>';
        }
    }
    
    /**
     * Добавляет в правый верхний угол виджет с информацией для пользователя
     * 
     * @return null
     */
    protected function printInformer()
    {
        if ( $this->displayInformer )
        {
            $this->widget('application.extensions.ECMarkup.ECMainInformer.ECMainInformer');
        }else
        {
            echo '<div class="offset2"></div>';
        }
    }
}