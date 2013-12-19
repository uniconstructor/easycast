<?php

/**
 * Модуль "база актеров" (она же каталог)
 * 
 * Отображает всех актеров из базы по разным критериям.
 * Для составления критериев используется расширение "ESearchScopes" (критерии поиска)
 * Один критерий поиска - один раздел анкеты. Основные разделы анкеты отличаются от дополнительных
 * по свойству type
 * 
 * @todo вынести функции для работы с сессией в отдельный класс, объединить с функциями из FastOrder
 */
class CatalogModule extends CWebModule
{
    /**
     * @var string - Nип главных разделов каталога. Используется в объекте DefaultScope,
     *               при сохранении критериев выборки анкет в раздел.
     *               Все разделы этого типа будут считаться главными разделами каталога и
     *               отображаться в верхнем меню
     */
    const BASE_SECTION_TYPE = 'catalog|section';
    
    /**
     * @var string - Тип вкладок каталога. Используется в объекте DefaultScope,
     *               при сохранении критериев выборки анкет в раздел.
     */
    const BASE_TAB_TYPE = 'catalog|tab';
    
    /**
     * @var int - количество анкет на одной странице или вкладке каталога
     */
    const PAGE_ITEMS_COUNT = 24;
    
    /**
     * @var string - Префикс, который добавляется ко всем полям формы поиска, чтобы избежать 
     *               конфликта имен input-полей на странице.
     *               Этот параметр постоянно нужен в разных модулях, поэтому он вынесен сюда
     */
    const SEARCH_FIELDS_PREFIX = 'QSearch';
    
    /**
     * @var string - путь к расширению "ESearchScopes" (критерии поиска)
     *                предполагается, что расширение уже установлено на момент установки и запуска каталога
     */
    public $searchScopesPath = 'application.extensions.ESearchScopes.';
    
    /**
     * @var array
     */
    public $controllerMap = array(
        'default' => array(
            'class'=>'application.modules.catalog.controllers.CatalogController',
        ),
        'catalog' => array(
            'class'=>'application.modules.catalog.controllers.CatalogController',
        ),
    );
    
    /**
     * (non-PHPdoc)
     * @see CModule::init()
     */
	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			$this->searchScopesPath.'models.*',
		));
	}

	/**
	 * (non-PHPdoc)
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
		if ( parent::beforeControllerAction($controller, $action) )
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
	/**
	 * Получить условия для разделов каталога
	 * @todo прописать критерий через SearchScopes
	 * @param array|SearchScope $scopes - критерий поиска проекта или несколько таких критериев поиска
     *         (например если мы ищем по своим критериям)
	 * @return CDbCriteria
	 */
	public function getCatalogCriteria($scopes=array())
	{
	    $criteria  = new CDbCriteria();
	    // Показываем в базе пользователей только проверенные анкеты
	    $criteria->compare('status', 'active');
	    //$criteria->compare('virtual', '0');
	    
	    return $criteria;
	}
	
	/**
	 * Создать условие поиска (CDbCriteria) по данным из фильтров, переданных в функцию
	 * Условие никогда не создается полностью пустым - изначально в него всегда добавляется правило
	 * "искать только анкеты в активном статусе" + сортировка по рейтингу
	 *
	 * @param array $data - данные из поисковых фильтров (формы поиска)
	 * @param CatalogFilter $filters - набор фильтров, по которым составляется критерий поиска
	 * @return CDbCriteria
	 *
	 * @todo предусмотреть возможность отключать изначальное содержание CDbCriteria
	 */
	public static function createSearchCriteria($data, $filters)
	{
	    // указываем путь к классу, который занимается сборкой поискового запроса из отдельных частей
	    $pathToAssembler = 'catalog.extensions.search.handlers.QSearchCriteriaAssembler';
	    // создаем основу для критерия выборки
	    $startCriteria = self::createStartCriteria();
	     
	    // Указываем параметры для сборки поискового запроса по анкетам
	    $config = array(
	        'class'           => $pathToAssembler,
	        'data'            => $data,
	        'startCriteria'   => $startCriteria,
	    );
	    $config['filters']  = $filters();
	    $config['saveData'] = false;
	    
	     
	    // создаем компонет-сборщик запроса. Он соберет CDbCriteria из отдельных данных формы поиска
	    $assembler = Yii::createComponent($config);
	    if ( ! $finalCriteria = $assembler->getCriteria() )
	    {// ни один фильтр поиска не был использован - возвращаем исходные условия
	        return $startCriteria;
	    }
	     
	    return $finalCriteria;
	}
	
	/**
	 * Создать базовый критерий выборки анкет (то условие, поверх
	 * которого будут накладываться все остальные критерии поиска)
	 *
	 * @return CDbCriteria
	 */
	public static function createStartCriteria()
	{
	    $criteria = new CDbCriteria();
	     
	    // (по умолчанию - берем только анкеты в активном статусе)
	    $criteria->compare('status', 'active');
	    // $criteria->addCondition("`t`.`status` = 'active'");
	    // сортируем анкеты по рейтингу (сначала лучшие)
	    $criteria->order = '`t`.`rating` DESC';
	     
	    return $criteria;
	}
	
	/**
	 * Получить полный набор фильтров поиска для создания раздела каталога, вкладки, онлайн-кастинга
	 * или условий отбора участников для роли   
	 * 
	 * @param string $type - настройка на будущее, если понадобится получать разные наборы фильтров для разных
	 *                       случаев создания новых объектов
	 * @return array
	 */
	public static function getFullFilterKit($type=null)
	{
	    return CatalogFilter::model()->findAll();
	} 
	
	/**
	 * @param $str
	 * @param $params
	 * @param $dic
	 * @return string
	 */
	public static function t($str='',$params=array(),$dic='catalog')
	{
	    if (Yii::t("CatalogModule", $str)==$str)
	    {
	        return Yii::t("CatalogModule.".$dic, $str, $params);
	    }else
	    {
	        return Yii::t("CatalogModule", $str, $params);
	    }
	}
	
	/// методы для работы с сессией (навигация) /// 
	
	/**
	 * Подготавливает переменную каталога в сессии к работе с навигацией
	 *
	 * @return null
	 *
	 * @todo вынести время хранения переменной в сессии в настройки
	 */
	protected static function initCatalogNavigation()
	{
	    if ( ! Yii::app()->session->contains('catalogNavigation') )
	    {
	        $navigation = array();
	        $navigation['sectionId'] = 0;
	        $navigation['tab']       = 0;
	        $navigation['page']      = 0;
	        Yii::app()->session->add('catalogNavigation', $navigation);
	    }
	     
	    // помним навигацию как минимум 2 часа
	    Yii::app()->session->setTimeout(3600*2);
	}
	
	/**
	 * Получить информацию о навигации в каталоге
	 * 
	 * @return array|bool
	 */
	public static function getCatalogNavigation()
	{
	    if ( ! Yii::app()->session->contains('catalogNavigation') )
	    {
	        return false;
	    }
	    return Yii::app()->session->itemAt('catalogNavigation');
	}
	
	/**
	 * Удалить данные навигации из сессии
	 */
	public static function clearNavigationData()
	{
	    if ( ! self::navigationIsEmpty() )
	    {
	        Yii::app()->session->remove('catalogNavigation');
	    }
	}
	
	/**
	 * Получить данные о навигации в сессии (раздел, вкладка или страница каталога)
	 * @param string $name - какую часть навигации получить:
	 *                     sectionId - id раздела каталога
	 *                     tab - короткое название вкладки каталога внутри раздела
	 *                     page - номер страницы (если каталог разбит на страницы)
	 *                     
	 * @return int|string
	 */
	public static function getNavigationParam($name)
	{
	    if ( ! $navigation = self::getCatalogNavigation() )
	    {
	        return false;
	    }
	    return $navigation[$name];
	}
	
	/**
	 * Сохранить данные о навигации в сессии (раздел, вкладка или страница каталога)
	 * @param string $name - какую часть навигации сохранить:
	 *                     sectionId - id раздела каталога
	 *                     tab - короткое название вкладки каталога внутри раздела
	 *                     page - номер страницы (если каталог разбит на страницы)
	 * @param string|int $value - значение переменной навигации
	 *
	 * @return null
	 */
	public static function setNavigationParam($name, $value)
	{
	    self::initCatalogNavigation();
	    $navigation = self::getCatalogNavigation();
	    $navigation[$name] = $value;
	    Yii::app()->session->add('catalogNavigation', $navigation);
	}
	
	/// Функции для работы с сессией, которые надо переместить в другой класс :) ///
	
	/**
	 * Подготавливает переменную поиска в сессии к работе с критериями поиска
	 *
	 * @return null
	 *
	 * @todo вынести время хранения переменной в сессии в настройки
	 */
	protected static function initSessionSearchData()
	{
	    if ( ! Yii::app()->session->contains('searchData') )
	    {
	        Yii::app()->session->add('searchData', array(
	        'form'   => array(),
	        'filter' => array(),
	        ));
	    }
	
	    // помним поиск как минимум 2 дня
	    Yii::app()->session->setTimeout(3600 * 24 * 2 );
	}
	
	/**
	 * Получить из сессии все данные о поиске
	 * @return array
	 */
	public static function getSessionSearchData($type='', $id=0)
	{
	    self::initSessionSearchData();
	    $data = Yii::app()->session->itemAt('searchData');
	    
	    if ( ! $type )
	    {
	        return $data;
	    }elseif( $type == 'form' )
	    {
	        return $data['form'];
	    }
	    if ( $type AND $id )
	    {
	        if ( isset($data['filter'][$id]) )
	        {
	            return $data['filter'][$id];
	        }
	    }
	}
	
	/**
	 * Записать в сессию данные поисковой формы
	 * @param array $searchData - данные поисковой формы
	 * @return null
	 */
	protected static function setSessionSearchData($searchData)
	{
	    Yii::app()->session->add('searchData', $searchData);
	}
	
	/**
	 * Подготовить переменную сессии, которая хранит данные текущего фрагмента формы
	 *
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента формы
	 * @return null
	 */
	protected static function initFormSearchData($namePrefix)
	{
	    $searchData = self::getSessionSearchData();
	    $searchData['form'][$namePrefix] = array();
	     
	    self::setSessionSearchData($searchData);
	}
	
	/**
	 * Подготовить переменную сессии, которая хранит данные текущего фрагмента фильтра
	 *
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента фильтра
	 * @return null
	 */
	protected static function initFilterSearchData($namePrefix, $sectionId)
	{
	    $searchData = self::getSessionSearchData();
	    if ( ! isset($searchData['filter'][$sectionId][$namePrefix]) )
	    {
	        $searchData['filter'][$sectionId][$namePrefix] = array();
	    }
	     
	    self::setSessionSearchData($searchData);
	}
	
	/**
	 * Получить данные для фрагмента формы поиска
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента
	 * @return array
	 * 
	 * @todo не разделять поиск по разделам и большой форме. Оставить для совместимости.
	 */
	public static function getFormSearchData($namePrefix)
	{
	    if ( ! $searchData = self::getSessionSearchData() )
	    {// данные изначально не установлены
	        self::initFormSearchData($namePrefix);
	        $searchData = self::getSessionSearchData();
	    }
	    if ( ! isset($searchData['form'][$namePrefix]) )
	    {
	        return array();
	    }
	    return $searchData['form'][$namePrefix];
	}
	
	/**
	 * Записать в сессию данные для фрагмента формы поиска
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента
	 * @param array $data - данные из фрагмента
	 * @return null
	 */
	public static function setFormSearchData($namePrefix, $data)
	{
	    self::initFormSearchData($namePrefix);
	    $searchData = self::getSessionSearchData();
	    $searchData['form'][$namePrefix] = $data;
	     
	    self::setSessionSearchData($searchData);
	}
	
	/**
	 * Очистить данные для фрагмента формы поиска
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента
	 * @return null
	 */
	public static function clearFormSearchData($namePrefix)
	{
	    self::initFormSearchData($namePrefix);
	    $searchData = self::getSessionSearchData();
	    $searchData['form'][$namePrefix] = array();
	     
	    self::setSessionSearchData($searchData);
	}
	
	/**
	 * Получить данные для фрагмента фильтра поиска в текущем разделе каталога
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента
	 * @return array
	 */
	public static function getFilterSearchData($namePrefix, $sectionId)
	{
	    self::initFilterSearchData($namePrefix, $sectionId);
	    $searchData = self::getSessionSearchData();
	     
	    return $searchData['filter'][$sectionId][$namePrefix];
	}
	
	/**
	 * Записать в сессию данные для фрагмента фильтра поиска в текущем разделе каталога
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента
	 * @param array $data - данные из фрагмента
	 * @return null
	 */
	public static function setFilterSearchData($namePrefix, $sectionId, $data)
	{
	    self::initFilterSearchData($namePrefix, $sectionId);
	    $searchData = self::getSessionSearchData();
	    $searchData['filter'][$sectionId][$namePrefix] = $data;
	

	    self::setSessionSearchData($searchData);
	}
	
	/**
	 * Очистить данные для фрагмента фильтра поиска в текущем разделе каталога
	 * @param string $namePrefix - название переменной в которой хранятся все значения текущего фрагмента
	 * @return null
	 */
	public static function clearFilterSearchData($namePrefix, $sectionId)
	{
	    self::initFilterSearchData($namePrefix, $sectionId);
	    $searchData = self::getSessionSearchData();
	    $searchData['filter'][$sectionId][$namePrefix] = array();
	
	    self::setSessionSearchData($searchData);
	}
}
