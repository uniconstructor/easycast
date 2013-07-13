<?php

/**
 * Компонент для сбора условий поиска из данных формы поиска
 * Собирает любые поисковые запросы в любых комбинациях
 * Работает с фильтрами каталога и большой формой поиска
 * Может сохранять данные как в сессию так и в базу данных
 * 
 * Обработкой одного критерия поиска занимается один класс-наследник QFilterHandlerBase
 * (базовый класс для всех обработчиков фильтров)
 * 
 * Один класс - один фильтр поиска
 * 
 * Принцип работы: получает данные формы поиска, перебирает все классы-обработчики,
 * из каждого класса-обработчика получает фрагмент поискового запроса (CDbCriteria), 
 * склеивает из всех полученных критериев один большой и сложный запрос (при помощи CDbCriteria->merge())
 * 
 * @todo возможно следует переписать всю структуру через классы ControllerAction
 */
class QSearchCriteriaAssembler extends CComponent
{
    /**
     * @var CatalogSection|null - раздел каталога, внутри которого производится поиск
     *                        Не используется, если нужен поиск по всей форме 
     */
    public $section;
    
    /**
     * @var array - данные из формы поиска
     */
    public $data = array();
    
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
     * @var array - используемые фильтры поиска (массив объектов CatalogFilter)
     *               Содержит список фильтров каталога, если поиск происходит в каталоге
     *               Или вообще все возможные фильтры, если поиск происходит через большую форму
     */
    protected $filters = array();
    
    /**
     * @var CDbCriteria - переменная для хранения промежуточного результата конструирования запроса
     */
    protected $criteria;
    
    /**
     * @var string - путь к папке где лежат все классы-обработчики поисковых запросов
     */
    protected $pathPrefix = 'application.modules.catalog.extensions.search.handlers.';
    
    /**
     * @var bool - параметр, определяющий, что хотя бы один из переданных поисковых критериев был использоват
     */
    protected $_hasFilters = false;
    
    /**
     * Подготавливает компонент для работы
     * 
     * @return null
     * 
     * @todo проверить наличие обязательных параметров
     */
    public function init()
    {
        // Подключаем все классы моделей, необходимые для поиска по анкетам
        $this->importDataClasses();
        
        if ( is_object($this->section) )
        {// происходит поиск по фильтрам
            // Получаем условие поиска по разделу (к нему будут добавляться все остальные фильтры)
            $this->criteria = $this->section->scope->getCombinedCriteria();
            
            foreach ( $this->section->filterinstances as $instance )
            {// перебираем все фильтры раздела и для каждого создаем условие
                $this->filters[] = $instance->filter;
            }
        }else
       {// происходит поиск по большой форме
            $this->criteria = new CDbCriteria();
            // @todo перенести status=active в условия выборки раздела
            // (ну или хер с ним вот тут просто исключение раздел-то не указан (0). 
            // возможно следует добавить специальный фильтр поиска "статус анкеты", видимый только администраторам) 
            $this->criteria->compare('status', 'active');
            
            // в этом месте мы определяем, какие фильтры поиска будут исполльзоваться в большой форме поиска
            // (это все фильтры, прикрепленные к "нулевому" разделу)
            // @todo возможно следовало бы прикрепить фильтры большой формы поиска к разделу №1 (вся база)
            // но я выбрал здесь 0 потому что раздел "вся база" содержит другие разделы (но не анкеты)
            // и было бы архитектурно неправильно прикреплять фильтры поиска к разделу в котором не может быть анкет
            $instances = CatalogFilterInstance::model()->findAll('`sectionid` = 0');
            
            foreach ( $instances as $instance )
            {// перебираем все фильтры большой формы поиска и для каждого добавляем условие
                $this->filters[] = $instance->filter;
            }
        }
    }
    
    /**
     * Подключить все необходимые для поиска классы хранения данных
     * (эта функция нужна для корректной работы AJAX-запроса поиска)
     * 
     * @return null
     */
    protected function importDataClasses()
    {
        // Подключаем все необходимые для поиска классы
        
        // базовый класс всех поисковых обработчиков
        Yii::import($this->pathPrefix.'QSearchHandlerBase');
        // Конструктор поисковых запросов
        Yii::import('application.extensions.ESearchScopes.models.*');
        // все модели используемые в анкете
        Yii::import('questionary.models.*');
        Yii::import('questionary.models.complexValues.*');
        Yii::import('questionary.extensions.behaviors.*');
        // модуль каталога (нужен для работы с функциями сессии)
        Yii::import('application.modules.catalog.CatalogModule');
    }
    
    /**
     * Получить комбинированное условие выборки по всем данным формы поиска
     * Этот метод реализует паттерт "Абстрактная фабрика" (abstract factory)
     * Он перебирает все используемые фильтры, для каждого создает класс-обработчик и из
     * каждого класса-обработчика получает фрагмент условия
     * 
     * @return CDbCriteria
     * 
     * @todo переместить status=active в условие раздела и формы
     */
    public function getCriteria()
    {
        $this->init();
        foreach ( $this->filters as $filter )
        {// перебираем все используемые фильтры, и получаем критерий выборки для каждого из них
            $filterCriteria = $this->addFilterCriteria($filter);
        }
        if ( ! $this->_hasFilters )
        {
            return false;
        }
        
        return $this->criteria;
    }
    
    /**
     * Сохранить все данные из формы поиска в таблицу SearchScopes
     * Перебирает все используемые поисковые фильтры, составляет из них условия (SearchCondition)
     * привязывает их к одному условию, и сохраняет в базу   
     * 
     * @return SearchScope
     */
    public function saveScope()
    {
        $this->init();
        foreach ( $this->filters as $filter )
        {
            
        }
    }
    
    /**
     * Получить и слить с общим результатом критерий поиска одного фильтра
     * 
     * @param CatalogFilter $filter - фильтр поиска из которого получается критерий (объект CDbCriteria)
     * @return null
     */
    protected function addFilterCriteria($filter)
    {
        $config = array(
            'class'  => $this->pathPrefix.$filter->handlerclass,
            'filter' => $filter,
            'data'   => $this->data,
            'saveData' => $this->saveData,
            'saveTo'   => $this->saveTo,
            'section'  => $this->section,
        );
        $handler = Yii::createComponent($config);
        
        if ( $criteria = $handler->getCriteria() )
        {// если фильтр используется - добавляем его к общему условию
            $this->criteria->mergeWith($criteria);
            $this->_hasFilters = true;
        }
    }
    
    /**
     * Получить условие поиска из фильтра (объект SearchScope)
     * @param CatalogFilter $filter  - фильтр поиска из которого получается критерий (объект SearchScope)
     * @return null
     */
    protected function getFilterScope($filter)
    {
        
    }
    
    /**
     * Получить список условий поиска для составления из них SearchScope
     * @param CatalogFilter $filter  - фильтр поиска из которого cсписок условий (объекты класса ScopeCondition)
     * @return array - массив объектов класса ScopeCondition
     */
    protected function getFilterConditions($filter)
    {
        
    }
}