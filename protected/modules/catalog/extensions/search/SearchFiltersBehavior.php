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
    
    //public $searchDataField = 'searchdata';
    
    /**
     * Получить все сохраненные данные из формы поиска людей для вакансии
     *
     * @return null
     */
    /*protected function getSearchData()
    {
        return unserialize($this->searchdata);
    }*/
    
    /**
     * Обновить данные о критерии выборки людей, которые подходят под эту вакансию
     * @param array|null $newData - новые условия подбора людей на вакансию
     * @return bool
     *
     * @todo обработать ситуацию, когда набор условий есть, но содержит более одного критерия
     *       или критерий неправильного типа
     */
    /*public function setSearchData($newData)
    {
        if ( is_array($newData) )
        {
            $newData = serialize($newData);
        }
        if ( $this->searchdata == $newData )
        {// если условия выборки не изменились - ничего не надо делать
            return true;
        }
        // сохраняем новые данные из формы поиска в вакансию
        $this->searchdata = $newDataSerialized;
        $this->owner->save(false, array($this->searchDataField));
    }*/
    
    /**
     * Получить данные для одного поискового фильтра
     * @param string $namePrefix - имя ячейки в массиве данных из формы поиска,
     *                             в которой лежит сохраненное значение фильтра
     * @return array
     */
    /*public function getFilterSearchData($namePrefix)
    {
        $searchData = $this->getSearchData();
        if ( ! isset($searchData[$namePrefix]) )
        {// поисковый фильтр не используется
            return array();
        }
        return $searchData[$namePrefix];
    }*/
    
    /**
     * Записать данные для одного поискового фильтра
     * @param string $namePrefix - имя ячейки в массиве данных из формы поиска,
     *                             в которой лежит сохраненное значение фильтра
     * @param array $value - новое значение фильтра
     * @return array
     */
    /*public function getFilterSearchData($namePrefix, $value)
    {
        $searchData = $this->getSearchData();
        $searchData[$namePrefix] = $value;
        return $this->setSearchData($searchData);
    }*/
    
    /**
     * Создать условие поиска (CDbCriteria) по данным из фильтров, прикрепленных к вакансии (роли)
     * По этому условию определяется, подходит участник на вакансию или нет
     * Условие никогда не создается полностью пустым - изначально в него всегда добавляется правило
     * "искать только анкеты в активном статусе" и другие критерии, в зависимости от данных создаваемой роли
     *
     * @param array $data - данные из поисковых фильтров (формы поиска)
     * @return CDbCriteria
     *
     * @todo предусмотреть возможность отключать изначальное содержание CDbCriteria
     * @todo если понадобится - сделать настройку "добавлять/не добавлять префикс 't' к полю status"
     */
    /*protected function createSearchCriteria($data)
    {
        // указываем путь к классу, который занимается сборкой поискового запроса из отдельных частей
        $pathToAssembler = 'catalog.extensions.search.handlers.QSearchCriteriaAssembler';
        $startCriteria = new CDbCriteria();
    
        // Указываем параметры для сборки поискового запроса по анкетам
        $config = array(
            'class'           => $pathToAssembler,
            'data'            => $data,
        );
        if ( $this->isNewRecord )
        {// вакансия создается, она пока еще не сохранена в БД, поэтому
            // фильтры к ней еще не добавлены, и сохранять критерий тоже некуда - зададим все руками
            $config['filters']  = $this->getDefaultFilters();
            $config['saveData'] = false;
        }else
        {// вакансия редактируется - обновляем критерий выборки
            //$config['filters'] = $this->searchFilters;
            $config['filters'] = $this->getDefaultFilters();
            $config['saveTo']  = 'db';
        }
    
        // создаем компонет-сборщик запроса. Он соберет CDbCriteria из отдельных данных формы поиска
        /* @var $assembler QSearchCriteriaAssembler */
        /*$assembler = Yii::createComponent($config);
        $assembler->init();
         
        if ( $finalCriteria = $assembler->getCriteria() )
        {// ни один фильтр поиска не был использован - возвращаем исходные условия
            return $finalCriteria;
        }
        return $startCriteria;
    }*/
    
    /**
     * Получить условия выборки подходящих анкет для этой вакансии
     * @return CDbCriteria
     */
    /*public function getSearchCriteria()
    {
        if ( $this->isNewRecord )
        {// условие еще не создано
            return false;
        }
        return $this->createSearchCriteria($this->getSearchData());
    }*/
    
    /**
     * Получить список фильтров, которые привязываются к записи сразу же после создания
     * @return CatalogFilter[]
     */
    /*protected function getDefaultFilters()
    {
        return CatalogFilter::model()->findAll();
    }*/
    
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