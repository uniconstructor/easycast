<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Разделы каталога"
 * @todo не учитывать статус при извлечении критериев поиска раздела (иначе он проверяется несколько раз)
 */
class QSearchHandlerSections extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data     = $this->getFilterData();
        $sections = $data['sections'];
    
        $criteria = new CDbCriteria();
        foreach ( $sections as $id )
        {// для каждого раздела каталога получаем свой критерий поиска
            if ( ! $section = CatalogSection::model()->findByPk($id) )
            {// @todo записать ошибку в лог
                continue;
            }
            if ( $section->searchdata )
            {// берем условия поиска из данных формы
                $criteria->mergeWith($section->getSearchCriteria(), 'OR');
            }else
            {// @todo плагин SearchScopes больше не используем - оставлено для совместимости
                // @deprecated удалить при рефакторинге
                $criteria = $section->scope->getCombinedCriteria($criteria, 'OR');
            }
        }
        return $criteria;
    }
    
    /**
     * @see QSearchHandlerBase::getFilterData()
     */
    protected function getFilterData()
    {
        // Получаем имя элемента в массиве, в котором должны находится данные из фильтра поиска
        $name = QSearchFilterBase::defaultPrefix().'sections';
        if ( isset($this->data[$name]) AND ! empty($this->data[$name]) )
        {// Данные фильтра есть в массиве - значит он используется
            return $this->data[$name];
        }
        $name = QSearchFilterBase::defaultPrefix().'iconlist';
        if ( isset($this->data[$name]) AND ! empty($this->data[$name]) )
        {// Данные фильтра есть в массиве - значит он используется
            return $this->data[$name];
        }
        return null;
    }
}