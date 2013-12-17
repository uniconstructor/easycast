<?php

/**
 * Виджет краткой формы поиска содержащий только список иконок с разделами каталога
 * Отображается как длинная полоска с иконками разделов каталога
 */
class QSearchSections extends CWidget//SearchFilters
{
    /**
     * @var bool - очищать ли выбранные категории по событию очистки формы? 
     */
    public $clearByEvent = false;
    /**
     * @var string - по какому адресу отправлять поисковый ajax-запрос
     */
    public $searchUrl = '/catalog/catalog/ajaxSearch';
    /**
     * @var string - по какому адресу отправлять запрос на очистку данных формы
     */
    public $clearUrl = '/catalog/catalog/clearSessionSearchData';
    /**
     * @var string - дополнительные параметры для запроса очистки критериев поиска в сессии
     */
    public $crearParams = array('namePrefix' => 'QSearchsections');
    /**
     * @var CatalogSection[] - список отображаемых разделов каталога
     */
    protected $sections;
    
    protected $_assetUrl;
    
    /**
     * @see SearchFilters::init()
     */
    public function init()
    {
        Yii::import('catalog.models.*');
        // Подключаем картинки, стили и скрипты для оформления
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('catalog.extensions.search.QSearchSections.assets').DIRECTORY_SEPARATOR);
        // получаем список отображаемых разделов
        $this->sections = CatalogSection::model()->findAll("`id` > 1 AND `visible` = 1");
        // устанавливаем скрипт активации кнопки для каждого раздела
        foreach ( $this->sections as $section )
        {
            $js = '$("#QSearchsections_button_'.$section->id.'").click(function(){
                $(this).toggleClass("ec-search-section-active");
                if ( $(this).hasClass("ec-search-section-active") )
                {
                    $("#QSearchsections_hidden_'.$section->id.'").prop("checked", true);
                }else
                {
                    $("#QSearchsections_hidden_'.$section->id.'").prop("checked", false);
                }
                console.log($("[name=\'QSearchsections[sections][]\']:checked"));
            });';
            Yii::app()->clientScript->registerScript('#toggleSearchSection'.$section->id, $js, CClientScript::POS_END);
        }
        parent::init();
    }
    /**
     * @see SearchFilters::run()
     */
    public function run()
    {
        $this->render('sections', array('sections' => $this->sections));
    }
}