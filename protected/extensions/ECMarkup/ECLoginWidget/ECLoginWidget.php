<?php
/**
 * Виджет для входа на сайт.
 * Отображает приглашение войти - если пользователь не авторизован или
 * показывает аватар, имя и ссылку на страницу пользователя, если он уже авторизован 
 */
class ECLoginWidget extends CWidget
{
    /**
     * @var string - html-код основного изображения со ссылкой 
     */
    protected $_image;
    /**
     * @var string - текст под картинкой (ФИО пользователя или ссылка "войти")
     */
    protected $_label;
    /**
     * @var string - ссылка на страницу, на которую перейдет пользователь после нажатия на виджет
     *               (страница авторизации или личная страница пользователя)
     */
    protected $_mainUrl;
    /**
     * @var string - путь к папке с картинками, стиялми и скриптами модуля
     */
    protected $_assetUrl;
    
    /**
     * Подготавливает виджет к запуску
     */
    public function init()
    {
        $this->_assetUrl = Yii::app()->assetManager->publish(
                        Yii::app()->extensionPath . DIRECTORY_SEPARATOR .
                        'ECMarkup' . DIRECTORY_SEPARATOR .
                        'ECLoginWidget' . DIRECTORY_SEPARATOR .
                        'assets'   . DIRECTORY_SEPARATOR);
        if ( Yii::app()->user->isGuest )
        {// готовим виджет для гостя
            // ссылка на вход
            $this->_mainUrl = Yii::app()->getModule('user')->loginUrl;
            // Изображение с иконкой входа
            $this->_image = CHtml::link(
                        CHtml::image($this->_assetUrl.'/login.png', Yii::t('coreMessages','entrance'), array('style' => 'height:75px;width:75px;')),
                        $this->_mainUrl);
            // Текст "войти"
            $this->_label = '<div class="easycast-menu-label">'.
                        CHtml::link(Yii::t('coreMessages','entrance'), $this->_mainUrl).
                        '</div>';
        }else
       {// готовим виджет для авторизованного пользователя
           $username    = Yii::app()->getModule('user')->user()->fullname;
           $questionary = Yii::app()->getModule('user')->user()->questionary;
           $logoutUrl   = Yii::app()->getModule('user')->logoutUrl;
           // ссылка на свою страницу
           $this->_mainUrl = Yii::app()->getModule('questionary')->profileUrl;
           // Аватар пользователя
           $this->_image = CHtml::link(
                           CHtml::image($questionary->avatarUrl, Yii::app()->getModule('user')->user()->fullname, array('style' => 'height:75px;width:75px;')),
                           $this->_mainUrl);
           // Имя пользователя и кнопка "выход"
           $this->_label = '<div class="easycast-menu-label">'.
                       CHtml::link($username, $this->_mainUrl).
                       '<br>('.CHtml::link(Yii::t('coreMessages','logout'), $logoutUrl).')'.
                       '</div>';
        }
    }
    
    /**
     * Отображает виджет с кнопкой входа или аватар пользователя
     */
    public function run()
    {
        echo $this->_image;
        echo $this->_label;
    }
}
