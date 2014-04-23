<?php

/**
 * Виджет для отображения списка анкет в категории и списка категорий каталога
 * 
 * @todo добавить локализацию
 */
class CatalogData extends CWidget
{
    /**
     * @var int максимальное количество подразделов (или анкет) в разделе, 
     *           отображаемое на одной странице 
     */
    const MAX_SECTION_ITEMS = 36;
    
    /**
     * @var int id раздела каталога,который отображается
     */
    public $sectionid;
    /**
     * @var string - id активной в данной момент вкладки
     */
    public $tab = null;
    /**
     * @var array - условия формы поиска (если они есть) 
     */
    public $scopes = array();
    
    /**
     * @var CatalogSection|null
     */
    protected $section = null;
    /**
     * @var string - скрипты для запоминания активной вкладки в сессии при переключении вкладок
     */
    protected $_tabScripts = null;
    /**
     * @var string
     */
    protected $_assetUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.extensions.ESearchScopes.models.*');
        
        // Подключаем CSS для оформления
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('catalog.extensions.CatalogData.assets') . DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/catalog.css');
        
        if ( $section = CatalogSection::model()->findByPk($this->sectionid) )
        {
            $this->section = $section;
        }
        if ( $activeTab = CatalogModule::getNavigationParam('tab') )
        {
            $tab = $activeTab;
        }
    }
    
    /**
     * Отображет список анкет в каталоге или список разделов каталога
     * @see CWidget::run()
     */
    public function run()
    {
        $this->widget('bootstrap.widgets.TbTabs', array(
            'type'        => 'tabs',
            'encodeLabel' => false,
            'tabs'        => $this->getTabs(),
            'events'      => array(
                'click' => $this->getTabScripts(),
            )
        ));
    }
    
    /**
     * Получить вкладки внутри раздела с анкета
     * (В виде, пригодном для виджета)
     * 
     * @return array
     */
    protected function getTabs()
    {
        $tabs = array();
        // первая вкладка - всегда название раздела
        $tabs[] = $this->getMainTab();
        
        // ищем все вкладки, прикрепленные к разделу
        // @todo вынести в init
        $instances = CatalogTabInstance::model()->findAll(
            'sectionid = :sectionid AND visible = 1', 
            array(':sectionid' => $this->sectionid)
        );
        
        foreach ( $instances as $instance )
        {
            $tabs[] = $this->getTab($instance->tab);
        }
        
        // @todo последняя вкладка - всегда поиск по фильтрам
        if ( $this->section->id != 1 )
        {
            $tabs[] = $this->getSearchTab();
        }
        
        return $tabs;
    }
    
    /**
     * Получить содержимое всего раздела (другие разделы или анкеты)
     * 
     * @return array массив (вкладка для виджета 'bootstrap.widgets.TbTabs' - название + описание)
     */
    protected function getMainTab()
    {
        $tab = array();
        $tab['label']  = $this->section->name;//$this->getTabLabel();
        $tab['id']     = $this->section->shortname;
        if ( ! $this->tab )
        {// если специально не выбрана какая-то особенная вкладка - то активируем по умолчанию первую
            $tab['active'] = true;
        }
        
        if ( $this->section->content == 'sections' )
        {// раздел содержит другие разделы
            $criteria = new CDbCriteria();
            $criteria->addCondition('parentid = :parentid');
            $criteria->addCondition('visible = 1');
            $criteria->params[':parentid'] = $this->section->id;
            $criteria->order = " `order` ASC";
            
            $dataProvider = new CActiveDataProvider('CatalogSection', array( 
                    'criteria' => $criteria,
                    'pagination' => array('pageSize' => self::MAX_SECTION_ITEMS),
                )
            );
        }else
        {// раздел содержит анкеты
            $criteria = $this->section->scope->getCombinedCriteria();
            
            // @todo прописать активный статус в условиях самих разделов
            $criteria->compare('status', 'active');
            $dataProvider = new CActiveDataProvider('Questionary', array(
                    'criteria'   => $criteria,
                    'pagination' => array('pageSize' => self::MAX_SECTION_ITEMS),
                )
            );
        }
        
        // получаем содержимое раздела в виде плитки с картинками
        $tab['content'] = $this->getPageContent($dataProvider, $this->section->content);
        
        return $tab;
    }
    
    /**
     * Получить содержимое вкладки (список разделов или список анкет)
     * Возвращает html-код виджета TbThumbinails
     * 
     * @param CatalogTab $tab - вкладка внутри раздела или null если отображаются все анкеты раздела
     * @return array массив (вкладка для виджета 'bootstrap.widgets.TbTabs' - название + описание)
     */
    protected function getTab($tab)
    {
        $tabData = array();
        $tabData['label'] = $tab->name;//$this->getTabLabel();
        $tabData['id']    = $tab->shortname;
        
        // добавляем скрипт, который запоминает выбранную в каталоге вкладку в сессию
        // каждый раз когда на нее переключается пользователь
        $this->_tabScripts .= $this->getTabNavigationUpdate($tab->shortname);
        
        // получаем критерий выборки по разделу
        $sectionCriteria = $this->section->scope->getCombinedCriteria();
        // получаем критерий выборки по вкладке
        $criteria = $tab->scope->getCombinedCriteria($sectionCriteria);
        $criteria->addCondition("`status` = 'active'");
        
        $dataProvider = new CActiveDataProvider('Questionary', array(
                'criteria'   => $criteria,
                'pagination' => array('pageSize' => self::MAX_SECTION_ITEMS),
            )
        );
        
        // получаем содержимое раздела в виде плитки с картинками
        $tabData['content'] = $this->getPageContent($dataProvider, 'users', $tab->shortname);
        
        return $tabData;
    }
    
    /**
     * Получить содержимое раздела с учетом условий поиска
     * 
     * @return array массив (вкладка для виджета 'bootstrap.widgets.TbTabs' - название + описание)
     * 
     * @todo сделать возврат к странице каталога при использовании поиска
     */
    protected function getSearchTab()
    {
        $tab['label']  = '<div id="search_tab">Поиск</div>';
        $tab['id']     = 'search';
        if ( $this->section->content == 'sections' )
        {// раздел содержит другие разделы: искать внутри него мы не можем, покажем большую форму поиска
            $tab['content'] = '&nbsp;(форма поиска)';
        }else
        {// раздел содержит анкеты - покажем всплывающее меню с фильтрами поиска
            $tab['content'] = $this->render('_search', array(), true);
            // добавляем скрипт, который запоминает выбранную в каталоге вкладку в сессию
            // и перезагружает содержимое вкладки "поиск" каждый раз когда на нее переключается пользователь
            $this->_tabScripts .= $this->getTabNavigationUpdate('search_tab');
            // $this->getSearchFormLoadScript();
        }
        
        return $tab;
    }
    
    /**
     * Получить ссылку для отображания названия вкладки
     * 
     * @param CatalogTab $tab
     * @return string
     */
    protected function getTabLabel($tab=null)
    {
        $label = $this->section->name;
        $urlparams = array('sectionid' => $this->sectionid);
        if ( ! $tab )
        {
            $label = $tab->name;
        }else
       {
           $urlparams['tab'] = $tab->shortname;
        }
        
        $url = Yii::app()->createUrl('/catalog', $urlparams);
        
        $link = CHtml::link($label, $url);
        
        return $link;
    }
    
    /**
     * Отобразить список анкет или разделов при помощи виджета 
     * 
     * @param CActiveDataProvider $dataProvider
     * @param string $type - что отображать: разделы или анкеты
     * @param string $tabName - короткое название отображаемой вкладки с кользователями
     * 
     * @return string - html-код списка анкет или разделов
     */
    protected function getPageContent($dataProvider, $type, $tabName='')
    {
        switch ( $type )
        {
            case 'users':
                // в разделе содержатся участники
                $view     = '/_user';
                $template = "{summary}{items}{pager}";
            break;
            case 'sections':
                // в разделе содержаться другие разделы
                // @todo мы отказались от этой идеи - сейчас просто выводим список всех услуг
                //       вместо списка подразделдов
                return $this->widget('ext.ECMarkup.EServiceList.EServiceList', array(), true);
            break;
        }
        
        return $this->widget('bootstrap.widgets.TbThumbnails', array(
            'dataProvider'    => $dataProvider,
            'template'        => $template,
            'itemView'        => $view,
            'ajaxUpdate'      => $tabName.'_tab',
            'id'              => $tabName.'_tab',
            'afterAjaxUpdate' => $this->getPagerAfterAjaxUpdate(),
        ), true);
    }
    
    /**
     * Получить JS-функцию которая при помощи AJAX-запроса устанавливает в сессию
     * номер текущей страницы каталога, на которой находится пользователь
     * (чтобы из анкеты можно было вернуться обратно)
     * @return string
     */
    protected function getPagerAfterAjaxUpdate()
    {
        $url = Yii::app()->createUrl('/catalog/catalog/setNavigationParam');
        return 'function(id)
        {
            var tabName = id;
            var tabSelector  = "#" + id;
            var pageNumSelector = tabSelector + " .pagination li.active";
            var pageNum = $(pageNumSelector).text();
            
            var ajaxData = {
                '.Yii::app()->request->csrfTokenName.': "'.Yii::app()->request->csrfToken.'",
                tab : tabName,
                page : pageNum,
            };
            var settings = {
                url  : "'.$url.'",
                data : ajaxData,
                type : "post"
            };
            jQuery.ajax(settings);
        }';
    }
    
    /**
     * Получить JS-функцию которая при помощи AJAX-запроса устанавливает в сессию
     * имя текущей вкладки каталога, на которой находится пользователь
     * (чтобы из анкеты можно было вернуться обратно)
     * @param string $tab - короткое название вкладки
     * @return string
     * 
     * @todo переписать этот JS более элегантным способом
     */
    protected function getTabNavigationUpdate($tab)
    {
        $url = Yii::app()->createUrl('/catalog/catalog/setNavigationParam');
        return '
        if ( e.target.attributes[0].nodeValue == "#'.$tab.'" ){
            var pageNumSelector = "#'.$tab.' .pagination li.active";
            var pageNum = $(pageNumSelector).text();
            
            var ajaxData = {
                '.Yii::app()->request->csrfTokenName.': "'.Yii::app()->request->csrfToken.'",
                tab : "'.$tab.'",
                page : pageNum,
            };
            var settings = {
                url  : "'.$url.'",
                data : ajaxData,
                type : "post"
            };
            jQuery.ajax(settings);
        };';
    }
    
    /**
     * Получить JS для вывода формы с фильтрами поиска и запомнить выбранную вкладку в сессии
     * 
     * @return string
     * 
     * @todo разобраться с сессией
     * @todo получить код формы через ajax корректным образом, заставив работать все скрипты
     */
    protected function getSearchFormLoadScript()
    {
        // Получаем html с формой всех фильтров поиска через AJAX (для ускорения загрузки страницы)
        $url = Yii::app()->createUrl('/catalog/catalog/getFilterForm', array('sectionId' => $this->section->id));
        $title = "<h4>Поиск в разделе &quot;{$this->section->name}&quot;</h4>";
        $js = 'function ec_init_search_filters(){
            var ajaxData = {
                '.Yii::app()->request->csrfTokenName.': "'.Yii::app()->request->csrfToken.'",
                sectionId : '.$this->section->id.',
            };
            var ajaxOptions = {
                url  : "'.$url.'",
                data : ajaxData,
                type : "post"
            };
                
            jQuery.ajax(ajaxOptions).done(function( formHtml ) {
                var popoverOptions = {
                    html : true,
                    content : formHtml,
                    title: "'.$title.'",
                    placement: "right",
                };
                console.log(formHtml);
                $("#search_tab").popover(popoverOptions);
            });
        };
        ec_init_search_filters();';
        
        Yii::app()->clientScript->registerScript('_ecLoadSearchFilterFormJS#', $js, CClientScript::POS_END);
    }
    
    /**
     * Получить все скрипты, работающие при переключении вкладок в виде одной функции
     * @return string
     */
    protected function getTabScripts()
    {
        return 'js:function(e) {'.$this->_tabScripts.'}';
    }
}