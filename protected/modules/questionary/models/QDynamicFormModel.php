<?php

/**
 * Модель для динамической формы анкеты
 * Определяет нужные проверки (rules) в зависимости от того какие поля нужно вывести
 * 
 * @todo определить сценарии формы
 */
class QDynamicFormModel extends CFormModel
{
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        
    }
    
    /**
     * Задать анкету, из которой будут получены все значения по умолчанию (только при тедактировании)
     * @param Questionary $questionary
     * @return void
     */
    public function setQuestionary($questionary)
    {
        
    }
    
    /**
     * Задать список отображаемых полей. Вызывается перед setScenario
     * @param array $fields
     * @return void
     */
    public function setDispayedFields($fields)
    {
        
    }
}