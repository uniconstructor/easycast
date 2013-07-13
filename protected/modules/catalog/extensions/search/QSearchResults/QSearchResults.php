<?php

/**
 * Виджет для отображения результатов поиска
 * Схема работы: принимает данные из формы поиска (поиск в разделе или большая форма)
 * Определяет, данные с каких виджетов пришли
 * Для каждого виджета составляет критерий запроса, после чего комбинирует все критерии в один
 * Получает по нему списокт анкет и выводит их в нужном виде
 */
class QSearchResults extends CWidget
{
    /**
     * @var (filter/search) - тип запроса (фильтр каталога или большая форма поиска)
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
    
    public $routeParams = array();
    
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
        // указываем путь к классу, который занимается сборкой поискового запроса из отдельных частей
        $pathToAssembler = 'application.modules.catalog.extensions.search.handlers.QSearchCriteriaAssembler';
        if ( $this->mode == 'filter' AND ! is_object($this->section) )
        {
            throw new CHttpException(500, 'Section not found');
        }
        // Указываем параметры для сборки запроса
        $config = array(
            'class'   => $pathToAssembler,
            'data'    => $this->data,
            'section' => $this->section,
        );
        $this->assembler = Yii::createComponent($config);
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
            // @todo переместить статус в критерий раздела
            $criteria->compare('status', 'active');
            $emptyText = $this->getAjaxMessage('noData');
            $dataProvider = new CActiveDataProvider('Questionary', array(
                'criteria'   => $criteria,
                'pagination' => array(
                    'pageSize' => $this->getMaxSectionItems(),
                    'route'    => $this->route,
                    //'params'   => $this->getRouteParams(),
                ),
            ));
        }
        
        
        
        $this->widget('bootstrap.widgets.TbThumbnails', array(
            'dataProvider' => $dataProvider,
            //'enablePagination' => false,
            'ajaxUpdate'   => 'search_results_data',
            'id'           => 'search_results_data',
            //'ajaxUrl'      => array('/catalog/catalog/index', 'a'=>'b'),//Yii::app()->createUrl('/catalog/catalog/index'),//$this->getAjaxUrl(),
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
     *
     * @todo языковые строки
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
        return 72;
    }
    
    protected function getRouteParams()
    {
        $params = $this->routeParams;
        $newParams = array('data' => CJSON::encode($this->data) );
        
        return CMap::mergeArray($params, $newParams);
    }
}