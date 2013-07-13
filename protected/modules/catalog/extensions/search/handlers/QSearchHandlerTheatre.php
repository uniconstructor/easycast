<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Музыкальный ВУЗ"
 * (Название класса MUni выбрано для краткости - длина имени класса не может быть больше 24 символов)
 * 
 * @todo вынести алгоритм получения unixtime года окончания во внешнюю функцию
 */
class QSearchHandlerTheatre extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $isUsed = false;
        $data = $this->getFilterData();
        
        $criteria = new CDbCriteria();
        $criteria->with = array('theatres');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        if ( isset($data['theatres']) AND $data['theatres'] )
        {// получаем список театров (если он указан) и составляем критерий поиска для них
            $criteria->addInCondition('`theatres`.`theatreid`', $data['theatres']);
            $isUsed = true;
        }
        
        if ( $isUsed )
        {// Если указан театр - значит ищем только актеров театра
            $criteria->compare('istheatreactor', 1);
        }
    
        return $criteria;
    }
}