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
 * ВАЖНО: Длина имени класса-обработчика не может быть больше 24 символов (ограничение Yii)
 * 
 * Чаще всего один класс-обработчик отвечает за один фильтр поиска.
 * Можно привязать несколько виджетов к одному обработчику.
 * Привязать несколько обработчиков к одному виджету нельзя.
 * 
 * Если вы создаете новый фильтр поиска - то после создания его виджета и обработчика нужно миграцией добавить 
 * запись о них в таблицу {{catalog_filters}}
 * 
 * 
 * Принцип работы: получает данные формы поиска, перебирает все классы-обработчики,
 * из каждого класса-обработчика получает фрагмент поискового запроса (CDbCriteria), 
 * склеивает из всех полученных критериев один большой и сложный запрос (при помощи CDbCriteria->merge())
 * 
 * @todo переместить этот класс из папки handlers в extensions/search/components
 * @todo когда-нибудь сократить длину префикса с "QSearchHandler" до "QSHandler" или QSH (переименовать все классы и данные в БД)
 * @todo избавиться от поля section, вместо него передавать изначальный критерий поиска + набор фильтров
 * @todo приравнять вариант поиска по большой форме поиска к поиску по фильтрам
 * @todo создать универсальный API для сохранения сериализованных данных формы поиска и критериев поиска в БД
 * @todo добавить в getCriteria параметры $anotherCriteria и $combine
 * @todo оставить поле filterInstances только для совместимости. Сделать "официальным" способом
 *       передачи фильтров в этот модуль установку их в поле filters. Поправить связи в моделях,
 *       сделать возможным извлечение не ссылок yf abkmnhs (filterInstances), а самих фильтров через relations()
 *       После этого пометить $this->filterInstances как deprecated
 */
class QSearchCriteriaAssembler extends CApplicationComponent
{
    /**
     * Изначальное условие выборки анкет, к которому добавляются все условия из фильтров
     * @var CDbCriteria 
     */
    public $startCriteria;
    /**
     * @var array - массив ссылок на используемые фильтры (CatalogFilterInstance)
     * @deprecated не используется, оставлен для совместимости, удалить при рефакторинге
     */
    public $filterInstances = array();
    /**
     * @var CActiveRecord|null - объект, к которому прикреплены критерии поиска
     *                           (если мы составляем условия для роли или раздела каталога)
     */
    public $searchObject;
    /**
     * @var CatalogSection|null - раздел каталога, внутри которого производится поиск
     *                        Не используется, если нужен поиск по всей форме 
     * @deprecated 
     */
    public $section;
    /**
     * @var array - данные из формы поиска: массив, 
     *              ключами которого являются названия фильтров поиска 
     *              (поле shortname в таблице search_filters + префикс из QSearchFilterBase::defaultPrefix())
     *              а значениями - сами данные фильтра
     *              (тоже массив структура которого задается виджетом поиска)
     */
    public $data = array();
    
    /**
     * @var bool - сохранять ли данные из формы поиска?
     *             (происходит почти всегда, в основном для сохранения данных о поиске в сессию)
     */
    public $saveData = true;
    /**
     * @var string - куда сохранить данные поиска (session/db)
     */
    public $saveTo = 'session';
    /**
     * @var array - Используемые фильтры поиска.
     *              Формат: массив объектов CatalogFilter или массив строк с короткими 
     *              именами фильтров ('age', 'gender')
     *              Содержит список фильтров поиска, которые могут быть применены к объекту,
     *              для которого собирается запрос (например раздел каталога, вакансия, и т. п.)
     *              Фильтры могут быть получены из объекта, которому принадлежит запрос или заданы вручную.
     */
    public $filters = array();
    
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
     * @return void
     * 
     * @todo проверить наличие обязательных параметров
     */
    public function init()
    {
        // Подключаем все классы моделей, необходимые для поиска по анкетам
        $this->importDataClasses();
        // создаем заготовку для будущего критерия выборки
        $this->createStartCriteria();
        
        // получаем список фильтров
        if ( ! empty($this->filters) )
        {// фильтры заданы извне (самый правильный способ, если нет объекта)
            $loadedFilters = array();
            foreach ( $this->filters as $key=>$filterElement )
            {// смотрим каждый переданный фильтр: это уже объект или его надо подгрузить? 
                if ( ! ($filterElement instanceof CatalogFilter) )
                {// передано только название фильтра - подгружаем его из базы
                    $criteria = new CDbCriteria();
                    $criteria->compare('shortname', $filterElement);
                    if ( ! $filter = CatalogFilter::model()->find($criteria) )
                    {
                        throw new CException("Фильтр с именем '{$filterElement}' не найден");
                    }
                    
                    // заменяем строку с названием фильтра на объект
                    unset($this->filters[$key]);
                    $this->filters[] = $filter;
                }
            }
        }elseif ( ! empty($this->filterInstances) )
        {// список фильтров задан извне
            foreach ( $this->filterInstances as $instance )
            {// перебираем все фильтры раздела и для каждого создаем условие
                $this->filters[] = $instance->filter;
            }
        }elseif ( is_object($this->searchObject) )
        {// происходит поиск по фильтрам, прикрепленным к произвольному объекту
            $this->filters = $this->searchObject->searchFilters;
        }elseif ( is_object($this->section) )
        {// происходит поиск по фильтрам раздела каталога
            foreach ( $this->section->filterinstances as $instance )
            {// перебираем все фильтры раздела и для каждого создаем условие
                $this->filters[] = $instance->filter;
            }
        }else
        {// происходит поиск по большой форме
            // в этом месте мы определяем, какие фильтры поиска будут использоваться в большой форме поиска
            // (это все фильтры, прикрепленные к корневому разделу "вся база")
            $instances = CatalogFilterInstance::model()->findAll("`linktype` = 'section' AND `linkid` = 1");
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
        Yii::app()->getModule('catalog');
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
        foreach ( $this->filters as $filter )
        {// перебираем все используемые фильтры, и получаем критерий выборки для каждого из них
            $filterCriteria = $this->addFilterCriteria($filter);
        }
        if ( ! $this->_hasFilters )
        {// не использован ни один фильтр поиска
            return false;
        }
        
        return $this->criteria;
    }
    
    /**
     * Получить критерий поиска одного фильтра и совместить его с остальными 
     * 
     * @param CatalogFilter $filter - фильтр поиска из которого получается поисковый критерий
     * @return null
     */
    protected function addFilterCriteria($filter)
    {
        $config = array(
            'class'        => $this->pathPrefix.$filter->handlerclass,
            'filter'       => $filter,
            // передаем в виджет все данные из формы поиска, он сам найдет нужные
            'data'         => $this->data,
            'saveData'     => $this->saveData,
            'saveTo'       => $this->saveTo,
            // @todo оставлено для совместимости, удалить при рефакторинге
            'section'      => $this->section,
            'searchObject' => $this->searchObject,
        );
        
        // для каждого фильтра создается свой обработчик
        /* @var $handler QSearchHandlerBase */
        $handler = Yii::createComponent($config);
        if ( ! $handler->enabled() )
        {// пользователю не разрешен поиск по этому критерию или он отключен по другим причинам 
            // идем дальше, даже не начинаем собирать SQL-запрос
            return;
        }
        if ( $criteria = $handler->getCriteria() )
        {// если фильтр используется - добавляем его к общему условию
            $this->criteria->mergeWith($criteria);
            $this->_hasFilters = true;
        }
    }
    
    /**
     * Создать начальное условие выборки 
     * (фундамент, на котором строится весь остальной поисковый запрос)
     * 
     * @return null
     */
    protected function createStartCriteria()
    {
        if ( $this->startCriteria instanceof CDbCriteria )
        {// изначальное условие выборки задано вручную
            $this->criteria = $this->startCriteria;
        }elseif ( is_object($this->searchObject) )
        {// происходит поиск по фильтрам
            // Получаем условие поиска по разделу (к нему будут добавляться все остальные фильтры)
            if ( ! $this->searchObject->scope )
            {// на тот редкий случай когда критериев поиска в прикрепленном объекте вообще нет
                $this->startCriteria = new CDbCriteria();
                $this->startCriteria->compare('status', 'active');
            }else
            {
                $this->startCriteria = $this->searchObject->scope->getCombinedCriteria();
            }
            $this->criteria = $this->startCriteria;
        }else
        {// изначальное условие выборки не задано - создаем его самостоятельно
            $this->criteria = new CDbCriteria();
            // @todo перенести status=active в условия выборки раздела (в том числе раздела "вся база")
            // возможно следует добавить специальный фильтр поиска "статус анкеты", видимый только администраторам)
            $this->criteria->compare('status', 'active');
            $this->startCriteria = $this->criteria;
        }
    }
}