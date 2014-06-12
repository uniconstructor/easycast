<?php

/**
 * Обработчик фильтра поиска по статусу
 */
class QSearchHandlerStatus extends QSearchHandlerBase
{
    /**
     * Получить CDbCriteria при поиске
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $criteria = new CDbCriteria();
        $data = $this->getFilterData();
        
        if ( isset($data['status']) AND ! empty($data['status']) )
        {// если задан хотя бы один статус - ищем по нему
            $values = implode("', '", $data['status']);
            $criteria->addCondition("`t`.`status` IN ('{$values}')");
        }else
        {// на всякий случай всегда категорически исключим статусы отложенных и неподтвержденных анкет
            $values = implode("', '", array('draft', 'unconfirmed', 'delayed'));
            $criteria->addCondition("`t`.`status` NOT IN ('{$values}')");
        }
        return $criteria;
    }
    
    /**
     * Фильтр по поиску всегда используется
     * @see QSearchHandlerBase::filterIsUsed()
     */
    /*protected function filterIsUsed()
    {
        return true;
    }*/
}