<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Музыкальный ВУЗ"
 * (Название класса MUni выбрано для краткости - длина имени класса не может быть больше 24 символов)
 * 
 * @todo вынести алгоритм получения unixtime года окончания во внешнюю функцию
 */
class QSearchHandlerAUni extends QSearchHandlerBase
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
        $criteria->with = array('actoruniversities');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        if ( isset($data['actoruniversities']) AND $data['actoruniversities'] )
        {// получаем список ВУЗов (если он указан) и составляем критерий поиска для них
            $criteria->addInCondition('`actoruniversities`.`universityid`', $data['actoruniversities']);
            $isUsed = true;
        }
        
        // Если указан максимальный или минимальный год окончания - то добавим критерии и для них
        if ( isset($data['minactorendyear']) AND $data['minactorendyear'] )
        {
            $criteria->addCondition('`actoruniversities`.`timeend` >= CAST(:minactorendyear AS SIGNED)');
            $criteria->params[':minactorendyear'] = mktime(12, 0, 0, 1, 1, $data['minactorendyear']);
            $isUsed = true;
        }
        if ( isset($data['maxactorendyear']) AND $data['maxactorendyear'] )
        {
            $criteria->addCondition('`actoruniversities`.`timeend` <= CAST(:maxactorendyear AS SIGNED)');
            $criteria->params[':maxactorendyear'] = mktime(12, 0, 0, 1, 1, $data['maxactorendyear']);
            $isUsed = true;
        }
        
        if ( $isUsed )
        {// Если указан или музыкальный ВУЗ или год его окончания - значит ищем только музыкантов
            $criteria->compare('isactor', 1);
        }
    
        return $criteria;
    }
}