<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Виды спорта"
 * @todo убрать вторую часть запроса (которая с `value` = 'custom')
 *       после того как будет налажен механизм распознавания стандартных значений
 *       (любые добавленные пользователем значения должны сверяться со стандартными,
 *       и если они совпадают с ними - заменяться на стандартные)
 */
class QSearchHandlerSport extends QSearchHandlerBase
{
    /**
     * Получить условия для CDbCriteria при поиске
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        
        $userValues = array();
        $values     = array();
        // проверяем, что все переданные в из формы поиска значения являются стандартными
        // (на случай попытки sql-injection)
        // если под видом стандартных значений передано что-то другое - то критерий просто не будет использован
        $types = QActivityType::model()->findAllByAttributes(array(
            'name'  => 'sporttype',
            'value' => $data['sporttype'],
        ));
        if ( empty($types) )
        {// вероятнее всего произошла попытка sql-injection: не используем фильтр
            // @todo вероятность такой атаки очень низка, но все же нужно записать эту ошибку в лог, запомнив IP
            return;
        }
        foreach ( $types as $type )
        {
            $values[]     = $type->value;
            $userValues[] = $type->translation;
        }
        // подставляем в SQL-запрос только проверенные значения
        $values     = implode("', '", $values);
        $userValues = implode("', '", $userValues);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('sporttypes');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('issportsman', 1);
        $criteria->addCondition("`sporttypes`.`value` IN ('{$values}')
            OR (`sporttypes`.`value` = 'custom' AND `sporttypes`.`uservalue` IN ('{$userValues}'))");
    
        return $criteria;
    }
}