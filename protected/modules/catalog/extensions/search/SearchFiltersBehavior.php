<?php

/**
 * Поведение для объектов, к которым привязаны критерии поиска
 * 
 * @todo добавить автоматическое создание связей (relations)
 */
class SearchFiltersBehavior extends CActiveRecordBehavior
{
    /**
     * @var string - название типа связи для для таблицы {{catalog_filter_instances}}
     *               Выбирается в зависимости от типа объекта, к которому привязываются фильтры
     *               Например для разделов каталога используется слово 'section' и т. д.
     */
    public $linkType;
    
    /**
     * Привязать фильтры поиска
     * @param CatalogFilter[] $filters - прикрепляемые фильтры поиска
     * @return void
     *
     * @todo добавить обработку ошибок
     */
    public function bindSearchFilters($filters)
    {
        // очищаем старый набор привязанных фильтров, если нужно
        $this->clearSearchFilters();
        // добавляем новый
        $orderNum = 0;
        foreach ( $filters as $filter )
        {
            $instance = new CatalogFilterInstance;
            $instance->linktype = $this->linkType;
            $instance->linkid   = $this->owner->id;
            $instance->filterid = $filter->id;
            $instance->order    = $orderNum;
            $instance->visible  = 1;
            
            $instance->save();
            $orderNum++;
        }
    }
    
    /**
     * Удалить все ранее привязанные к этому объекту фильтры поиска
     * @return void
     *
     * @todo добавить обработку ошибок
     */
    protected function clearSearchFilters()
    {
        if ( ! $this->owner->filterInstances )
        {
            return;
        }
        foreach ( $this->owner->filterInstances as $instance )
        {
            $instance->delete();
        }
    }
}