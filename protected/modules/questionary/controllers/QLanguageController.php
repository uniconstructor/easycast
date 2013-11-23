<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком иностранных языков
 *
 * @package easycast
 * @subpackage questionary
 */
class QLanguageController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QLanguage';
    
    /**
     *
     * @param array $instanceData
     * @param CActiveRecord $instance
     * @return void
     */
    protected function getCreatedData($instanceData, $instance)
    {
        $result = array();
        foreach ( $instanceData as $field => $value )
        {
            $result[$field] = $instance->$field;
        }
        
        return $result;
    }
}