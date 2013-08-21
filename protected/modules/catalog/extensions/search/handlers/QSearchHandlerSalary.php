<?php

/**
 * Класс сборки поискового запроса для поля "Оплата за день"
 */
class QSearchHandlerSalary extends QSearchHandlerBase
{
    /**
     * Разрешаем поиск по цене только для админов
     * (non-PHPdoc)
     * @see QSearchFilterBase::enabled()
     */
    public function enabled()
    {
        return Yii::app()->user->checkAccess('Admin');
    }
    
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        
        $criteria = new CDbCriteria();
        $criteria->with = array('recordingconditions');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        if ( isset($data['minsalary']) )
        {
            $criteria->addCondition('`recordingconditions`.`salary` >= :minsalary');
            $criteria->params[':minsalary'] = $data['minsalary'];
        }
        if ( isset($data['maxsalary']) )
        {
            $criteria->addCondition('`recordingconditions`.`salary` <= :maxsalary OR `recordingconditions`.`salary` IS NULL');
            $criteria->params[':maxsalary'] = $data['maxsalary'];
        }
        
        return $criteria;
    }
}