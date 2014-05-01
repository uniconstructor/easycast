<?php

/**
 * Верхняя часть страницы (заголовок), используется вместе с темой Maximal
 */
class ECResponsiveHeader extends CWidget
{
    /**
     * @var string - содержимое верхнего правого блока 
     */
    public $infoBlockContent    = '';
    /**
     * @var string - содержимое верхнего левого блока
     */
    public $contactBlockContent = '';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::app()->clientScript->registerCoreScript('jquery');
        if ( ! $this->infoBlockContent )
        {
            if ( Yii::app()->getModule('user')->getViewMode(false) OR ! Yii::app()->user->isGuest )
            {// режим просмотра выбран - покажем информер
                $this->infoBlockContent = $this->widget('ext.ECMarkup.ECUserInformer.ECUserInformer', array(), true);
            }else
            {// режим просмотра не выбран - покажем только кнопки "вход" и "регистрация"
                $loginUrl        = Yii::app()->createUrl(current(Yii::app()->getModule('user')->loginUrl));
                $registrationUrl = Yii::app()->createUrl('//easy');
                $loginButton        = CHtml::link('Вход', $loginUrl, array('class' => 'btn btn-primary btn-large'));
                $registrationButton = CHtml::link('Регистрация', $registrationUrl, array('class' => 'btn btn-info btn-large'));
                /*$registrationButton = CHtml::link('Регистрация', '#', array(
                    'class' => 'btn btn-info btn-large',
                    'data-toggle' => 'modal',
                    'data-target' => '#registration-modal',
                ));*/
                
                $this->infoBlockContent = $loginButton.' '.$registrationButton;
            }
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('header');
    }
}