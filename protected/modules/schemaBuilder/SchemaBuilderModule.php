<?php

namespace SchemaBuilder;

/**
 * Модуль для перестройки структуры AR-класса через GUI
 */
class SchemaBuilderModule extends CWebModule
{
    /**
     * @var string - префикс всех таблиц модуля
     */
    public $tablePrefix = 'ntt';
    
    /**
     * @see CModule::init()
     */
    public function init()
    {
        $this->setImport(array(
            //'schemaBuilder.components.*',
            'schemaBuilder.models.*',
            'schemaBuilder.controllers.*',
            //'schemaBuilder.actions.*',
        ));
        parent::init();
    }
    
    /**
     * @see CWebModule::beforeControllerAction()
     */
    public function beforeControllerAction($controller, $action)
    {
        if ( parent::beforeControllerAction($controller, $action) )
        {
            if( Yii::app()->user->isGuest )
            {// просим авторизоваться для использования любого действия в модуле управления правами
                Yii::app()->user->loginRequired();
            }
            if ( Yii::app()->user->checkAccess('Admin') )
            {
                return true;
            }else
            {// без прав доступа делаем вид что такой страницы нет
                throw new CHttpException(404, 'Страница не найдена');
            }
        }
        return false;
    }
}