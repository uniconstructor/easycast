<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Разделы каталога"
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
            $criteria = $section->scope->getCombinedCriteria($criteria, 'OR');
        }
    
        return $criteria;
    }
}