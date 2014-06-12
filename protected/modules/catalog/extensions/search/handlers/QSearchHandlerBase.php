<?php

/**
 * Базовый класс для всех обработчиков фильтров
 * Принимает данные из одного фильтра поиска и преобразует его в CDbCriteria или SearchScope 
 * 
 * @todo убрать разницу между поиском по разделу каталога и поиском по всей базе
 */
class QSearchHandlerBase extends CComponent
{
    /**
     * @var CatalogFilter - фильтр поиска по которому составляется запрос
     */
    public $filter;
    /**
     * @var CActiveRecord - объект, к которому прикреплены критерии поиска
     */
    public $searchObject;
    /**
     * @var CatalogSection|null - раздел каталога, внутри которого производится поиск
     *                            Не используется, если нужен поиск по всей форме
     * @deprecated использовать searchObject
     */
    public $section;
    /**
     * @var array - данные, пришедшие из формы поиска: содержит в себе данные всех полей формы поиска
     *              (это нужно на случай если обработчику поискового критерия потребуются данные
     *              других фильтров для того чтобы изменить или упростить собственный запрос)
     */
    public $data;
    /**
     * @var bool - сохранять ли данные из формы поиска (используется почти всегда,
     *             в основном для сохранения данных о поиске в сессию)
     * @deprecated сохранением теперь занимаются или QSearchCriteriaAssembler или классы соответствующих моделей
     */
    public $saveData = false;
    /**
     * @var string - куда сохранить данные поиска (session/db)
     * @deprecated сохранением теперь занимаются или QSearchCriteriaAssembler или классы соответствующих моделей
     */
    public $saveTo = 'session';
    
    /**
     * Можно ли текущему пользователю использовать этот фильтр?
     * (По умолчанию все фильтры можно использовать.
     * Если фильтр не разрешен - его виджет просто не покажется. Проверка здесь больше для страховки.
     * Функция нужна для того чтобы скрыть от посторонних поиск по цене или контактным данным.
     * Также фильтр может быть отключен в зависимости от любых других условий.
     * 
     * @return boolean
     */
    public function enabled()
    {
        return true;
    }
    
    /**
     * Получить объект CDbCriteria для поиска по фильтру
     * (интерфекс для обращения извне, код общий для всех плагинов)
     * Этот метод также проверяет, нужно ли составлять условие по этому запросу или нет,
     * а также сохраняет данные фильтра в сессию (если происходит поиск по фильтрам, и данные постоянно обновляются)
     * 
     * @return NULL|CDbCriteria
     */
    public function getCriteria()
    {
        if ( ! $this->filterIsUsed() )
        {
            return null;
        }
        // @todo удалить при рефакторинге
        //$this->saveFilterData();
        return $this->createCriteria();
    }
    
    /**
     * Получить условие поиска из данных пришедших из формы
     * (индивидуальный код для каждого плагина)
     *
     * @return SearchScope
     * @deprecated отказываемся от плагина searchScopes и переходим на сериализованные критерии поиска
     */
    protected function createScope()
    {
        throw new CException('createScope() должен быть наследован');
    }
    
    /**
     * Получить массив условий поиска (объекты ScopeCondition) из данных, пришедших из формы
     * (индивидуальный код для каждого плагина)
     *
     * @return array
     * @deprecated отказываемся от плагина searchScopes и переходим на сериализованные критерии поиска
     */
    protected function createConditions()
    {
        throw new CException('createConditions() должен быть наследован');
    }
    
    /**
     * Получить объект CDbCriteria для поиска по фильтру
     * (индивидуальный код для каждого плагина)
     * 
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        throw new CException('createCriteria() должен быть наследован');
    }
    
    /**
     * Определить, используется ли этот фильтр при поиске
     * (то есть нужно для него составлять условие или нет)
     * 
     * @return bool
     */
    protected function filterIsUsed()
    {
        if ( $this->getFilterData() )
        {
            return true;
        }
        return false;
    }
    
    /**
     * Найти данные одного фильтра поиска в общем массиве данных из всей формы поиска 
     * Обычно эта функция просто получает данные из одного фрагмента формы, но ее можно переопределить
     * и получить данные из другого фильтра, если два критерия поиска взаимосвязаны
     * @return array|null
     */
    protected function getFilterData()
    {
        //Yii::import('catalog.extensions.search.filters.QSearchFilterBase.QSearchFilterBase');
        // Получаем имя элемента в массиве, в котором должны находится данные из фильтра поиска
        $name = QSearchFilterBase::defaultPrefix().$this->filter->shortname;
        if ( isset($this->data[$name]) AND ! empty($this->data[$name]) )
        {// Данные фильтра есть в массиве - значит он используется
            return $this->data[$name];
        }
        return null;
    }
    
    /**
     * Сохранить данные фильтра
     * 
     * @return null
     * @deprecated сохранением теперь занимаются или QSearchCriteriaAssembler или классы соответствующих моделей
     */
    protected function saveFilterData()
    {
        if ( ! $this->saveData )
        {
            return;
        }
        // Получаем имя элемента в массиве, в котором должны находится данные из фильтра поиска
        $name = QSearchFilterBase::defaultPrefix().$this->filter->shortname;
        $data = $this->getFilterData();
        
        if ( $this->saveTo == 'session' )
        {// сохраняем результат поиска в сессию
            if ( is_object($this->searchObject) )
            {
                CatalogModule::setFilterSearchData($name, $this->searchObject->id, $data);
            }else
            {
                CatalogModule::setFormSearchData($name, $data);
            }
        }
    }
}