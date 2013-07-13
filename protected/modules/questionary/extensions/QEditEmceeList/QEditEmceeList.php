<?php

// Импортируем расширение для редактирования сложных значений
Yii::import('application.modules.questionary.extensions.QEditComplexActivity.QEditComplexActivity');

/**
 * Виджет для редактирования поля "мероприятия ведущего"
 * Позволяет редактировать название мероприятия и год.
 * 
 * @author frost
 *
 */
class QEditEmceeList extends QEditComplexActivity
{
    public $modelName  = 'Questionary';
    
    public $attribute  = 'emcee';
    
    public $objectType = 'emcee';
    
    public $copylinkId = 'emcee_copylink';
    
    public function init()
    {
        parent::init();
        
        $this->_elements = $this->model->emceelist;
    }
}