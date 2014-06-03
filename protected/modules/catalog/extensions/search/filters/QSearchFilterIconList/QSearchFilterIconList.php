<?php

/**
 * Фильтр формы поиска содержащий только список иконок с разделами каталога
 * Отображается как длинная горизонтальная полоска с названиями разделов каталога 
 * (при отображении результатов поиска)
 * или как вертикальный столбец с разделами каталога
 * 
 * @todo возможно стоит добавить иконку для каждого раздела, желательно векторную
 */
class QSearchFilterIconList extends QSearchFilterBase
{
    /**
     * @var string - префикс для названия всех input-полей
     *               в этом виджете вручную устанавливается как 'sections' для того чтобы названия полей
     *               для обработчика QSearchHandlerSections совпадали
     */
    public $namePrefix      = 'sections';
    /**
     * @var bool - очищать ли выбранные категории по событию очистки формы? 
     */
    public $clearByEvent    = false;
    /**
     * @var string - как располагать кнопки с разделами?
     *               horizontal - горизонтально, по 6 элементов в строке
     *               vertical   - вертикально, в один столбец по всей ширине столбца
     * @todo вместо этого параметра использовать $this->display со значением 'helper'
     *       когда в класс QSearchFilterBase будет добавлен такой режим просмотра
     */
    public $buttonAlignment = 'horizontal';
    
    /**
     * @var string
     */
    protected $_iconsAssetUrl;
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('sections');
    
    /**
     * @see SearchFilters::init()
     */
    public function init()
    {
        parent::init();
        $refreshJs = '';
        // Подключаем картинки, стили и скрипты для оформления
        $this->_iconsAssetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('catalog.extensions.search.filters.QSearchFilterIconList.assets').DIRECTORY_SEPARATOR);
        
        if ( $this->refreshDataOnChange )
        {// если нужно сразу же обновлять результаты поиска при изменении критериев поиска
            // то добавляем скрипт который создает jQuery-событие "критерии поиска изменены"
            // виджет результатов поиска (QSearchResult) перехватит это событие и обновить свое
            // содержимое через AJAX 
            $refreshJs = '$("body").trigger("'.$this->refreshDataEvent.'");';
        }
        
        foreach ( $this->getCatalogSections() as $section )
        {// устанавливаем скрипт активации кнопки для каждого раздела каталога
            $js = '$("#QSearchsections_button_'.$section->id.'").click(function(){
                $(this).toggleClass("btn-primary");
                if ( $(this).hasClass("btn-primary") )
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
     * @see QSearchFilterBase::getContent()
     */
    protected function getContent()
    {
        // загружаем отображаемые разделы каталога
        $sections = $this->getCatalogSections();
        // вспоминаем, какие разделы были выбраны в последний раз
        $data     = $this->loadLastSearchParams();
        
        $displayOptions = array(
            'sections' => $sections,
            'data'     => $data,
        );
        return $this->render($this->buttonAlignment, $displayOptions, true);
    }
    
    /**
     * @see QSearchFilterBase::getContentClass()
     */
    protected function getContentClass()
    {
        // не включаем выделение цветом полоски с разделами: это отдельный виджет, это только мешает
        return '';
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
        return "function {$this->collectDataJsName}() {
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