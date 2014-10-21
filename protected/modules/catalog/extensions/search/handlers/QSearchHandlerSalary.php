<?php

/**
 * Класс сборки поискового запроса для поля "Оплата за день"
 */
class QSearchHandlerSalary extends QSearchHandlerBase
{
    /**
     * Разрешаем поиск по цене только для админов
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
            $criteria->scopes['withSalaryMoreThen'] = array($data['minsalary']);
        }
        if ( isset($data['maxsalary']) )
        {
            $criteria->scopes['withSalaryLessThen'] = array($data['maxsalary']);
        }
        return $criteria;
    }
}