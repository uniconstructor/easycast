<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Музыкальный ВУЗ"
 * (Название класса MUni выбрано для краткости - длина имени класса не может быть больше 24 символов)
 * 
 * @todo вынести алгоритм получения unixtime года окончания во внешнюю функцию
 */
class QSearchHandlerMUni extends QSearchHandlerBase
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
        $criteria->with = array('musicuniversities');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        if ( isset($data['musicuniversities']) AND $data['musicuniversities'] )
        {// получаем список ВУЗов (если он указан) и составляем критерий поиска для них
            $criteria->addInCondition('`musicuniversities`.`universityid`', $data['musicuniversities']);
            $isUsed = true;
        }
        
        // Если указан максимальный или минимальный год окончания - то добавим критерии и для них
        if ( isset($data['minmusicendyear']) AND $data['minmusicendyear'] )
        {
            $criteria->addCondition('`musicuniversities`.`timeend` >= CAST(:minmusicendyear AS SIGNED)');
            $criteria->params[':minmusicendyear'] = mktime(12, 0, 0, 1, 1, $data['minmusicendyear']);
            $isUsed = true;
        }
        if ( isset($data['maxmusicendyear']) AND $data['maxmusicendyear'] )
        {
            $criteria->addCondition('`musicuniversities`.`timeend` <= CAST(:maxmusicendyear AS SIGNED)');
            $criteria->params[':maxmusicendyear'] = mktime(12, 0, 0, 1, 1, $data['maxmusicendyear']);
            $isUsed = true;
        }
        
        if ( $isUsed )
        {// Если указан или музыкальный ВУЗ или год его окончания - значит ищем только музыкантов
            $criteria->compare('ismusician', 1);
        }
    
        return $criteria;
    }
}