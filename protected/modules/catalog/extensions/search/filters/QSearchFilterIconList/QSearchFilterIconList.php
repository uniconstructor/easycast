<?php

/**
 * Фильтр формы поиска содержащий только список иконок с разделами каталога
 * Отображается как длинная полоска с иконками разделов каталога вверху страницы
 */
class QSearchFilterIconList extends QSearchFilterBase
{
    /**
     * @var string - префикс для названия всех input-полей
     *               в этом виджете вручную устанавливается как 'sections' для того чтобы названия полей
     *               для обработчика QSearchHandlerSections совпадали
     */
    public $namePrefix = 'sections';
    /**
     * @var bool - очищать ли выбранные категории по событию очистки формы? 
     */
    public $clearByEvent = false;
    
    protected $_iconsAssetUrl;
    
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('sections');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
    */
    protected function getTitle()
    {
        return "Разделы каталога";
    }
    
    /**
     * @see QSearchFilterBase::getFullTitle()
     */
    protected function getFullTitle()
    {
        return '';
    }
    
    /**
     * @see SearchFilters::init()
     */
    public function init()
    {
        parent::init();
        // Подключаем картинки, стили и скрипты для оформления
        $this->_iconsAssetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('catalog.extensions.search.filters.QSearchFilterIconList.assets').DIRECTORY_SEPARATOR);
        
        $refreshJs = '';
        if ( $this->refreshDataOnChange )
        {
            $refreshJs = '$("body").trigger("'.$this->refreshDataEvent.'");';
        }
        // устанавливаем скрипт активации кнопки для каждого раздела
        foreach ( $this->getCatalogSections() as $section )
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
                '.$refreshJs.'
            });';
            Yii::app()->clientScript->registerScript('#toggleSearchSectionIcon'.$section->id, $js, CClientScript::POS_END);
        }
    }
    /**
     * @see QSearchFilterBase::getContent()
     */
    protected function getContent()
    {
        return $this->render('sections', array('sections' => $this->getCatalogSections()), true);
    }
    
    /**
     * Получить JS-код, который реагирует на событие собирает все данные из фрагмента формы в JSON-массив
     * (для отправки по AJAX, чтобы тут же обновлять данные поиска в сессии 
     * или динамически обновлять содержимое поиска)
     * (этот метод - индивидуальный для каждого фильтра)
     *
     * @return string
     */
    protected function createCollectFilterDataJs()
    {
        //$selector  = 'input[name="'.$this->getFullInputName('sections').'[]"]';
        $selector  = 'input[name="QSearchsections[sections][]"]';
        return  "function {$this->collectDataJsName}() {
            var data = {};
            var typeValue  = jQuery('{$selector}:checked');
            if ( typeValue.length > 0 )
            {
                var types = [];
                $.each(typeValue, function(index, element){
                    types[index] = element.value;
                });
                
                data.sections = types;
            }
            return data;
        };";
        return $js;
    }
    
    /**
     * Получить js-код для очистки выбранных пользователем значений в фрагменте формы
     * (Этот JS очищает только данные на стороне клиента. Код уникальный для каждого элемента)
     *
     * @return string
     */
    protected function createClearFormDataJs()
    {
        $selector  = 'input[name="'.$this->getFullInputName('sections').'[]"]';
        return "\$('{$selector}').prop('checked', false);".
               "\$('.ec-search-section-icon').removeClass('ec-search-section-active');";
    }
    
    /**
     * Получить список разделов каталога, в которых можно искать
     * @return array
     */
    protected function getCatalogSections()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('parentid', 1);
        $criteria->compare('visible', 1);
        $criteria->compare('content', 'users');
        $criteria->order  = '`name` ASC';
    
        return CatalogSection::model()->findAll($criteria);
    }
}