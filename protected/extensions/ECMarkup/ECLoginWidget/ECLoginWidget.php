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
     * @deprecated
     * @var string - текст под картинкой (ФИО пользователя или ссылка "войти")
     */
    protected $_label;
    /**
     * @var string - ФИО
     */
    protected $_userName = '';
    /**
     * @var string - ссылка на страницу, на которую перейдет пользователь после нажатия на виджет
     *               (страница авторизации или личная страница пользователя)
     */
    protected $_mainUrl;
    /**
     * @var string - ссылка на выход с сайта
     */
    protected $_logoutUrl = '/user/logout';
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
        if ( ! Yii::app()->user->isGuest )
        {// готовим виджет для авторизованного пользователя
            $this->_userName  = Yii::app()->getModule('user')->user()->fullname;
            $questionary      = Yii::app()->getModule('user')->user()->questionary;
            //$this->_logoutUrl = Yii::app()->createUrl(Yii::app()->getModule('user')->logoutUrl);
            
            // ссылка на свою страницу
            $this->_mainUrl  = Yii::app()->getModule('questionary')->profileUrl;
            
            // Аватар пользователя
            //$avatarUrl = CHtml::image($questionary->avatarUrl, CHtml::encode($this->_userName), array(
            $avatarUrl = CHtml::image($questionary->avatarUrl, '', array(
               'style' => 'height:16px;width:16px;',
            ));
            $this->_image = CHtml::link($avatarUrl, $this->_mainUrl);
        }
    }
    
    /**
     * Отображает виджет с кнопкой входа или аватар пользователя
     */
    public function run()
    {
        if ( Yii::app()->user->isGuest )
        {
            $this->render('login');
        }else
        {
            $this->render('logout');
        }
    }
}