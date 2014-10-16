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
     * @deprecated не использовать в новых функциях
     */
    public $mode        = 'filter';
    /**
     * @var CatalogSection - раздел каталога внутри которого производится поиск
     *                       (если поиск производится внутри раздела)
     * @deprecated теперь вместо этого поля используется $this->searchObject
     */
    public $section     = null;
    /**
     * @var array - данные, пришедшие из формы поиска
     */
    public $data        = array();
    /**
     * @var string - url, по которому должно поисходить обновление данных
     */
    public $route       = '/catalog/catalog/ajaxSearch';
    /**
     * @var array - массив параметров, которые передаются вместе с номером страницы
     */
    public $routeParams = array();
    /**
     * @var string - тип объекта к которому привязаны критерии и результаты поиска
     *               (например вакансия или раздел каталога)
     * @todo заготовка для будущего рефакторинга
     */
    public $objectType  = 'section';
    /**
     * @var int - id объекта, к которому привязаны критерии поиска
     * @todo заготовка для будущего рефакторинга
     */
    public $objectId    = 1;
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
     * @see CWidget::init()
     */
    public function init()
    {
        // подключаем библиотеку sweekit, чтобы отображадось видео в загружаемых по AJAX анкетах
        Yii::app()->getClientScript()->registerSweelixScript('shadowbox');
        // эти классы нужны для отображения результатов поиска, 
        // потому что критерии поиска могут быть привязаны к ним
        Yii::import('catalog.models.CatalogSection');
        Yii::import('projects.models.EventVacancy');
        
        // загружаем условия поиска, если они не переданы из формы
        $this->setDefaultSearchObject();
        $this->setDefaultSearchData();
        
        // Указываем параметры для сборки запроса
        $config = array(
            // класс, который занимается сборкой поискового запроса из отдельных частей
            'class'        => 'catalog.extensions.search.handlers.QSearchCriteriaAssembler',
            'data'         => $this->data,
            'saveData'     => false,
            'searchObject' => $this->searchObject,
        );
        $this->assembler = Yii::createComponent($config);
        
        // подключаем стили galleria, чтобы выводить фотографии участника через AJAX
        $cs = Yii::app()->clientScript;
        $galleriaAssets = Yii::app()->assetManager->publish(Yii::app()->basePath.'/extensions/galleria/assets');
        $cs->registerCssFile($galleriaAssets.'/themes/classic/galleria.classic.css');
        $cs->registerScriptFile($galleriaAssets.'/galleria.min.js');
        $cs->registerScriptFile($galleriaAssets.'/themes/classic/galleria.classic.min.js');
    }
    
    /**
     * Определить модель. к которой привязаны критерии и результаты поиска (по objectType и objectId)
     * @return void|CatalogSection
     */
    protected function setDefaultSearchObject()
    {
        if ( is_object($this->searchObject) )
        {// объект поиска уже задан - извлекать его не требуется
            $this->objectId = $this->searchObject->id;
            return;
        }
        if ( ! $this->objectType OR ! $this->objectId )
        {// тип и id связанного объекта должны быть или заданы одновременно или вообще не заданы
            // @todo выбрасывать исключение в этом случае
            $this->searchObject = $this->section;
            return;
        }
        
        switch ( $this->objectType )
        {// получаем объект, к которому привязаны результаты поиска
            case 'section':
                $this->searchObject = CatalogSection::model()->findByPk($this->objectId);
            break;
            case 'vacancy':
                $this->searchObject = EventVacancy::model()->findByPk($this->objectId);
            break;
            // по умолчанию считаем объект разделом каталога
            default: 
                $this->searchObject = CatalogSection::model()->findByPk($this->objectId);
            break; 
        }
        if ( ! is_object($this->searchObject) )
        {// не удалось найти связанный объект
            // @todo выбрасывать исключение в этом случае
            throw new CException('searchObject not set');
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
        if ( ! $this->data )
        {
            $this->data = CatalogModule::getFilterSearchData(CatalogModule::SEARCH_FIELDS_PREFIX, $this->objectId);
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->data or empty($this->data) )
        {// не указаны критерии поиска
            $emptyText    = $this->getAjaxMessage('noData');
            $dataProvider = new CArrayDataProvider(array());
        }elseif ( ! $criteria = $this->assembler->getCriteria() )
        {// не указаны критерии поиска
            $emptyText    = $this->getAjaxMessage('noData');
            $dataProvider = new CArrayDataProvider(array());
        }else
        {// все данные есть, получаем результаты поиска
            // @todo переместить статус и сортировку в фильтры поиска
            if ( ! Yii::app()->user->checkAccess('Admin') )
            {// отложенные, неподтвержденные и скрытые анкеты видны только админам
                $criteria->addCondition("`t`.`status` NOT IN ('delayed', 'draft', 'unconfirmed') AND `t`.`visible` = 1");
            }
            $criteria->order = '`rating` DESC';
            
            $emptyText    = $this->getAjaxMessage('noRecords');
            $dataProvider = new CActiveDataProvider('Questionary', array(
                'criteria'   => $criteria,
                'pagination' => array(
                    'pageSize' => $this->getMaxSectionItems(),
                    'route'    => $this->route,
                    'params'   => $this->getRouteParams(),
                ),
            ));
        }
        $this->printCss3Grid($dataProvider, $emptyText);
    }
    
    /**
     * Отобразить результаты поиска (для старых браузеров)
     * @return void
     * 
     * @deprecated пока оставлено для совместимости, но новая верстка работает нормально
     *             так что в будущем можно будет удалить
     */
    protected function printSafeGrid($dataProvider, $emptyText)
    {
        $this->widget('bootstrap.widgets.TbThumbnails', array(
            'dataProvider' => $dataProvider,
            'ajaxUpdate'   => 'search_results_data',
            'id'           => 'search_results_data',
            //'ajaxType'     => 'post',
            'ajaxUrl'      => Yii::app()->createUrl($this->route, $this->routeParams),
            'template'     => "{summary}{items}{pager}",
            'itemView'     => '_user',
            'emptyText'    => $emptyText,
        ));
    }
    
    /**
     * Отобразить результаты поиска (для новых браузеров)
     * @return void
     */
    protected function printCss3Grid($dataProvider, $emptyText)
    {
        $this->widget('ext.CdGridPreview.CdGridPreview', array(
            'dataProvider'     => $dataProvider,
            'listViewLocation' => 'bootstrap.widgets.TbListView',
            'descriptionOnly'  => true,
            'listViewOptions' => array(
                'ajaxUpdate'   => 'search_results_data',
                'id'           => 'search_results_data',
                'ajaxUrl'      => Yii::app()->createUrl($this->route, $this->routeParams),
                'template'     => "{summary}{items}{pager}",
                'emptyText'    => $emptyText,
            ),
            'options' => array(
                'headerClass' => ' ',
                'textClass'   => ' ',
            ),
        ));
    }
    
    /**
     * 
     * @return array
     */
    protected function getRouteParams()
    {
        $params = array();
        
        if ( $this->searchObject )
        {
            if ( ! $params['mode'] = $this->mode )
            {
                $params['mode'] = 'filter';
            }
            if ( isset($this->searchObject->id) )
            {
                $params['searchObjectId'] = $this->searchObject->id;
            }
        }
        
        return $params;
    }
     
    /**
     * Получить html-код сообщения при поисковом запросе
     * @param string $type - тип сообщения (noData, noRecords)
     * @return string
     */
    protected function getAjaxMessage($type)
    {
        $message = '';
        if ( $type === 'noData' )
        {
            $message = '<h4 class="alert-heading">Поиск</h4>';
            $message .= '<p>Пожалуйста выберите критерии поиска и нажмите кнопку &quot;Найти&quot;</p>';
            $message = '<div class="alert alert-block">'.$message.'</div>';
        }
        if ( $type === 'noRecords' )
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
        return 100;
    }
}