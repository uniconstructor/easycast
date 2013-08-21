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
     * @var CatalogFilter
     */
    public $filter;
    
    /**
     * @var CatalogSection|null - раздел каталога, внутри которого производится поиск
     *                        Не используется, если нужен поиск по всей форме
     */
    public $section;
    
    /**
     * @var array - данные, пришедшие из формы поиска
     */
    public $data;
    
    /**
     * @var bool - сохранять ли данные из формы поиска (используется почти всегда,
     *              в основном для сохранения данных о поиске в сессию)
     */
    public $saveData = true;
    
    /**
     * @var string - куда сохранить данные поиска (session/db)
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
    protected function enabled()
    {
        return true;
    }
    
    /**
     * Получить объект CDbCriteria для поиска по фильтру
     * (интерфекс для обращения извне, код общий для всех плагинов)
     * Этот метод также проверяет, нужно ли составлять условие по этому запросу или нет
     * 
     * @return NULL|CDbCriteria
     */
    public function getCriteria()
    {
        if ( ! $this->filterIsUsed() )
        {
            return null;
        }
        $this->saveFilterData();
        return $this->createCriteria();
    }
    
    /**
     * Получить условие поиска из данных пришедших из формы
     * (индивидуальный код для каждого плагина)
     *
     * @return SearchScope
     */
    protected function createScope()
    {
        throw new CHttpException('500', 'createScope() должен быть наследован');
    }
    
    /**
     * Получить массив условий поиска (объекты ScopeCondition) из данных, пришедших из формы
     * (индивидуальный код для каждого плагина)
     *
     * @return array
     */
    protected function createConditions()
    {
        throw new CHttpException('500', 'createConditions() должен быть наследован');
    }
    
    /**
     * Получить объект CDbCriteria для поиска по фильтру
     * (индивидуальный код для каждого плагина)
     * 
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        throw new CHttpException('500', 'createCriteria() должен быть наследован');
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
     * Найти данные фильтра в общем массиве данных из формы поиска 
     * 
     * @return array|null
     */
    protected function getFilterData()
    {
        Yii::import('application.modules.catalog.extensions.search.filters.QSearchFilterBase.QSearchFilterBase');
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
     */
    protected function saveFilterData()
    {
        if ( ! $this->saveData )
        {
            return;
        }
        
        Yii::import('application.modules.catalog.extensions.search.filters.QSearchFilterBase.QSearchFilterBase');
        // Получаем имя элемента в массиве, в котором должны находится данные из фильтра поиска
        $name = QSearchFilterBase::defaultPrefix().$this->filter->shortname;
        $data = $this->getFilterData();
        
        if ( $this->saveTo == 'session' )
        {
            if ( is_object($this->section) )
            {
                CatalogModule::setFilterSearchData($name, $this->section->id, $data);
            }else
            {
                CatalogModule::setFormSearchData($name, $data);
            }
        }
    }
}