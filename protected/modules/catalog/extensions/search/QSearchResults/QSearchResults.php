<?php

/**
 * Виджет для отображения результатов поиска
 * Схема работы: принимает данные из формы поиска (поиск в разделе или большая форма)
 * Определяет, данные с каких виджетов пришли
 * Для каждого виджета составляет критерий запроса (CDbCriteria), после чего комбинирует все критерии в один
 * при помощи CDbCriteria->merge().
 * Получает по нему список анкет и выводит их в нужном виде
 * 
 * @todo переместить условие поиска по статусу в критерии раздела (миграция)
 * @todo языковые строки
 */
class QSearchResults extends CWidget
{
    /**
     * @var (filter/search) - тип запроса (фильтр каталога или большая форма поиска)
     * 
     * @todo убрать разделение на поиск по разделам и поиск по большой форме
     */
    public $mode;
    
    /**
     * @var CatalogSection - раздел каталога внутри которого производится поиск
     *                       (если поиск производится внутри раздела)
     */
    public $section = null;
    
    /**
     * @var array - данные, пришедшие из формы поиска
     */
    public $data;
    
    /**
     * @var string - url, по которому должно поисходить обновление данных
     */
    public $route = '/catalog/catalog/search';
    
    /**
     * @var array - массив параметров, которые передаются вместе с номером страницы
     */
    public $routeParams = array();
    
    /**
     * @var string - тип объекта к которому привязаны критерии и результаты поиска
     *               (например вакансия или раздел каталога)
     * @todo заготовка для будущего рефакторинга
     */
    public $objectType = 'section';
    
    /**
     * @var int - id объекта, к которому привязаны критерии поиска
     * @todo заготовка для будущего рефакторинга
     */
    public $objectId;
    
    /**
     * @var CActiveRecord - модель к которой привязаны критерии и результаты поиска
     *                      Может быть вичислена на основе objectType и objectId или задана вручную
     * @todo заготовка для рефакторинга
     */
    public $searchObject;
    
    /**
     * @var QSearchCriteriaAssembler - компонент, собирающий критерий запроса по всем данным формы поиска
     */
    protected $assembler;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        // эти классы нужны для отображения результатов поиска, потому что критерии поиска могут быть привязаны к ним
        Yii::import('application.modules.catalog.models.CatalogSection');
        Yii::import('application.modules.projects.models.EventVacancy');
        
        // указываем путь к классу, который занимается сборкой поискового запроса из отдельных частей
        // @todo сделать полем класса
        $pathToAssembler = 'application.modules.catalog.extensions.search.handlers.QSearchCriteriaAssembler';
        if ( $this->mode == 'filter' AND ! is_object($this->section) )
        {
            throw new CHttpException(500, 'Section not found');
        }
        
        // загружаем условия поиска, если они не переданы из формы
        //$this->setDefaultSearchObject();
        
        // Указываем параметры для сборки запроса
        $config = array(
            'class'   => $pathToAssembler,
            'data'    => $this->data,
            'section' => $this->section,
        );
        $this->assembler = Yii::createComponent($config);
    }
    
    /**
     * Определить модель. к которой привязаны критерии и результаты поиска (по objectType и objectId)
     * @return void|CatalogSection
     */
    protected function setDefaultSearchObject()
    {
        if ( is_object($this->searchObject) )
        {// объект поиска уже задан - извлекать его не требуется 
            return;
        }
        if ( $this->objectType XOR $this->objectId )
        {// тип и id связанного объекта должны быть или заданы одновременно или вообще не заданы
            // @todo выбрасывать исключение в этом случае
            $this->searchObject = $this->section;
            return;
        }
        
        switch ( $this->objectType )
        {// получаем объект, к которому привязаны результаты поиска
            case 'section':
                $this->searchObject = CatalogSection::model()->findAllByPk($this->objectId);
            break;
            case 'vacancy':
                $this->searchObject = EventVacancy::model()->findAllByPk($this->objectId);
            break;
            default: return; // @todo выбрасывать исключение в этом случае
        }
        if ( ! is_object($this->searchObject) )
        {// не удалось найти связанный объект
            // @todo выбрасывать исключение в этом случае
        }
    }
    
    /**
     * Получить условия поиска в тех случаях, когда они по каким-то причинам не могут быть переданы в запросе
     * Эта функция помогает исправить проблему с переходом по страницам в условиях поиска: при переходе
     * на любую страницу они сбрасываются.
     * Эта функция берет данные формы поиска из сессии или из базы, если они не передаются при запросе
     * 
     * @return null
     */
    protected function setDefaultSearchData()
    {
        if ( is_array($this->data) AND ! empty($this->data) )
        {// условия поиска переданы из формы - ничего делать не нужно
            return;
        }
        
        // @todo пока что ищем данные только в сессии и только для разделов каталога.
        //       Позже в этом месте мы должны просто обращаться к API объекта, чтобы получить из него нужные данные
        $this->loadSearchData();
    }
    
    /**
     * Подгрузить ранее сохраненные данные формы поиска
     * 
     * @return null
     */
    protected function loadSearchData()
    {
        $data = array();
        
        switch ( $this->objectType )
        {
            case 'section':
                // @todo убрать разделение между поиском по рпзделу и по большой форме
                $data = CatalogModule::getFormSearchData(CatalogModule::SEARCH_FIELDS_PREFIX);
                if ( $this->objectId OR $this->section )
                {
                    $data = CatalogModule::getFilterSearchData(CatalogModule::SEARCH_FIELDS_PREFIX, $this->objectId);
                }
            break;
        }
        
        $this->data = $data;
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->data or empty($this->data) )
        {// не указаны критерии поиска
            $emptyText = $this->getAjaxMessage('noData');
            $dataProvider = new CArrayDataProvider(array());
        }elseif ( ! $criteria = $this->assembler->getCriteria() )
        {// не указаны критерии поиска
            $emptyText = $this->getAjaxMessage('noData');
            $dataProvider = new CArrayDataProvider(array());
        }else
        {// все данные есть, получаем результаты поиска
            // @todo переместить статус и сортировку в критерий раздела
            $criteria->compare('status', 'active');
            $criteria->order = '`rating` DESC';
            
            $emptyText = $this->getAjaxMessage('noData');
            $dataProvider = new CActiveDataProvider('Questionary', array(
                'criteria'   => $criteria,
                'pagination' => array(
                    'pageSize' => $this->getMaxSectionItems(),
                    'route'    => $this->route,
                    'params'   => array(),//$this->getRouteParams(),
                ),
            ));
        }
        
        $this->widget('bootstrap.widgets.TbThumbnails', array(
            'dataProvider' => $dataProvider,
            //'enablePagination' => false,
            //'ajaxUpdate'   => 'search_results_data',
            //'id'           => 'search_results_data',
            'ajaxUrl'      => Yii::app()->createUrl($this->route),//array('/catalog/catalog/index', 'a'=>'b'),//Yii::app()->createUrl('/catalog/catalog/index'),//
            'template'     => "{summary}{items}{pager}",
            //'template'     => "{summary}{items}",
            'itemView'     => '_user',
            'emptyText'    => $emptyText,
        ));
    }
     
    /**
     * Получить html-код сообщения при поисковом запросе
     * @param string $type - тип сообщения (noData, noRecords)
     * @return string
     */
    protected function getAjaxMessage($type)
    {
        $message = '';
        if ( $type == 'noData' )
        {
            $message = '<h4 class="alert-heading">Поиск</h4>';
            $message .= '<p>Пожалуйста выберите критерии поиска и нажмите кнопку &quot;Найти&quot;</p>';
            $message = '<div class="alert alert-block">'.$message.'</div>';
        }
        if ( $type == 'noRecords' )
        {
            $message = '<h4 class="alert-heading">По вашему запросу ничего не найдено</h4>';
            $message .= '<p>Попробуйте выбрать другие критерии поиска</p>';
            $message = '<div class="alert alert-block">'.$message.'</div>';
        }
        return $message;
    }
    
    /**
     * Получить количество анкет, отображаемых на одной странице поиска
     * @return number
     * 
     * @todo перенести параметр в настройки
     */
    protected function getMaxSectionItems()
    {
        return 35;
    }
}