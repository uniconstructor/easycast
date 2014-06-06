<?php

/**
 * Класс сборки поискового запроса для поля "email"
 */
class QSearchHandlerEmail extends QSearchHandlerBase
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
        $criteria->with = array('user');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        if ( isset($data['email']) )
        {
            $criteria->addInCondition('user.email', $data['email']);
        }
        return $criteria;
    }
}