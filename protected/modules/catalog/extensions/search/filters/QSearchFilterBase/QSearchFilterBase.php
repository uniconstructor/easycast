<?php

/**
 * Базовый класс для всех виджетов-фильтров поиска по анкете.
 * 
 * Все формы фильтрации и поиска собираются из маленьких частей
 * Для каждого способа отсеивания анкет существует свой виджет
 * Все виджеты пользуются плагинов ESearchScopes для того чтобы составлять критерии поиска
 * Все параметры поиска хранятся в сессии 
 * (данные большой формы и данные фильтров каждого раздела хранятся отдельно)
 * или в базе данных
 * (в случает с критериями вакансии, оповещениями, оналйн-кастингом и т. д.)
 * 
 * ВАЖНО: Все виджеты фильтров используются только для отображения формы.
 * Каждый виджет умеет загружать свои данные из базы или сессии. 
 * Обработка всех поисковых запосов происходит при помощи компонентов. Для каждого виджета
 * поисковых условий должен существовать класс обработки его данных.
 * Классы виджетов поиска должны находится в папке catalog.extensions.search.filters
 * Классы обработчиков фильтров должны находится в папке catalog.extensions.search.handlers
 * 
 * @todo возможно следует добавлять в заголовок выбранные пользователем значения (обсудить)
 * @todo всегда использовать $this->filter->shortName вместо $this->namePrefix
 * @todo перенести все скрипты в assets
 * @todo добавить реакцию на событие "собрать данные только с этого фильтра"
 * @todo прикреплять jQuery-события не к body, а к родительским элементам. Передавать id элемента как параметр виджета
 * @todo перенести общую html-разметку внешнего вида виджета в шаблон (views)
 * @todo убрать поля section и vacancy - заменить их одним более общим объектом - 
 *       ("тот у которого есть данные о предыдущем значении в форме")
 * 
 * @todo убрать использование collectDataVar
 */
class QSearchFilterBase extends CWidget
{
    /**
     * @var string - как отображать фрагмент формы(form/filter): для большой формы или для списка фильтров
     */
    public $display;
    
    /**
     * @var CatalogSection - раздел анкеты в котором отображается фильтр 
     *                       (если фильтры используются в разделах каталога)
     */
    public $section;
    
    /**
     * @var EventVacancy - вакансия, к которой прикремлен фильтр поиска
     */
    public $vacancy;
    
    /**
     * @var CatalogFilter - фильтр в таблице catalog_filters
     */
    public $filter;
    
    /**
     * @var string - префикс для названия всех input-полей
     */
    public $namePrefix;
    
    /**
     * @var string - название jQuery события, посылаемого при очистке всей формы
     */
    public $clearSearchEvent = 'clearSearch';
    
    /**
     * @var string - название jQuery события, посылаемого при очистке только этого фрагмента формы
     *                Не должно пересекаться с именами других фильтров 
     */
    public $clearFilterEvent;
    
    /**
     * @var string - название jQuery события, посылаемого при сборе данных со всей формы
     */
    public $collectDataEvent = 'collectData';
    
    /**
     * @var string - название шлобальной переменной JavaScript, в которую собираются данные со всех фильтров
     */
    public $collectDataVar = 'ecSearchData';
    
    /**
     * @var string - источник данных для формы (откуда будут взяты значения по умолчанию)
     *               Возможные значения:
     *               'session' - данные берутся из сессии (используется во всех формах поиска)
     *               'db' - данные берутся из базы (используется при сохранении критериев вакансии и т. п.)
     */
    public $dataSource = 'session';
    
    /**
     * @var SearchScope|null - критерий поиска из таблицы SearchScopes
     *                         Используется только если данные для формы берутся из базы ($this->dataSource = 'db')
     *                         Если данные берутся из базы, а условие не задано - то оно будет создано
     *                         Правило: для всех критериев поиска, созданных через форму в объекте SearchScope  
     *                         поле shortname => $this->filter->shortname
     *                         поле type => 'filter'
     *                         
     * @todo механизм хранения данных формы в базе изменен - удалить при рефакторинге 
     */
    public $scope;
    
    /**
     * @var string - URL по которому отправляется AJAX-запрос на очистку данных для одного фильтра 
     */
    public $clearUrl = '/catalog/catalog/clearSessionSearchData';
    
    /**
     * @var string - название функции, которая проверяет, используется ли фильтр поиска
     *                (выбрано ли хотя бы одно значение)
     *                Уникально для каждого фильтра
     */
    protected $isEmptyJsName;
    
    /**
     * @var string - название функции, которая собирает все данные из одного фильтра поиска
     *                Уникально для каждого фильтра
     */
    protected $collectDataJsName;
    
    /**
     * @var string - название js-функции, которая включает и отключает подсветку фильтра в зависимости
     *                от того, активирован он или нет
     */
    protected $toggleHighlightJsName;
    
    /**
     * @var string - html-id для элемента, при нажатии на который очищается содержимое этого фрагмента формы
     *                (очищается и форма и сохраненные данные в сессии)
     */
    protected $clearDataPrefix;
    
    /**
     * @var string - id для HTML-элемента отображающего заголовок фрагмента формы
     *                 (используется для сворачивания/разворачивания элемента)
     */
    protected $titleId;
    
    /**
     * @var string - id для HTML-элемента отображающего основной код фрагмента формы
     *                 (используется для сворачивания/разворачивания элемента)
     */
    protected $contentId;
    
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements;
    
    /**
     * @var array - массив jquery-селекторов для элементов формы, при изменении которых
     *               возможно следует поменять цвет фильтра (выделить или снять выделение)
     */
    protected $inputSelectors = array();
    
    /**
     * @var array - допустимые режимы отображения виджета
     */
    protected $displayModes = array('form', 'filter', 'vacancy');
    
    /**
     * @var array - промежуточная переменная для хранения условий поиска 
     * (если условия поиска создаются по данным из формы)
     */
    protected $_conditions;
    
    protected $_assetUrl;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        // Подключаем все необходимые для поиска классы
        
        // Конструктор поисковых запросов
        // @todo возможно не используется здесь, в связи со всеми последними изменениями. Удалить при рефакторинге
        Yii::import('application.extensions.ESearchScopes.models.*');
        // виджет "улучшенный выпадающий список" ("select2")
        Yii::import('application.extensions.select2.ESelect2');
        // все модели используемые в анкете
        Yii::import('questionary.models.*');
        Yii::import('questionary.models.complexValues.*');
        Yii::import('questionary.extensions.behaviors.*');
        // модуль каталога (нужен для работы с функциями сессии)
        Yii::import('application.modules.catalog.CatalogModule');
        
        // Проверяем правильность всех параметров перед созданием виджета
        if ( ! in_array($this->display, $this->displayModes) )
        {
            throw new CHttpException('500', 'Неправильный тип отображения');
        }
        if ( ! $this->filter )
        {
            throw new CHttpException('500', 'Не задана связь виджета с фильтром в базе. $this->filter должен быть задан');
        }
        if ( $this->display == 'filter' AND ! is_object($this->section) )
        {
            throw new CHttpException('500', 'Не указан раздел для фильтра');
        }
        if ( ! $this->namePrefix )
        {// если префикс не задан вручную - то берем его из базы
            $this->namePrefix = $this->filter->shortname;
        }
        if ( ! $this->elements )
        {
            throw new CHttpException('500', 'Не задан список полей фрагмента формы. $this->internalElements должен быть задан');
        }
        
        // Устанавливаем одно общее имя массива для всех input-полей формы фрагмента поиска
        $this->namePrefix = self::defaultPrefix().$this->namePrefix;
        
        // Устанавливаем id элемента, при клике на который данные формы очищаются
        $this->clearDataPrefix = 'clear_search_fragment_'.$this->namePrefix;
        // Устанавливаем id элементов для сворачивания/разворачивания блока с формой
        $this->titleId   = 'filter_title_'.$this->namePrefix;
        $this->contentId = 'filter_content_'.$this->namePrefix;
        
        // определяем, в ответ на какие события очищать форму
        if ( ! $this->clearSearchEvent )
        {// событие очистки всей формы
            $this->clearSearchEvent = 'clearSearch';
        }
        if ( ! $this->clearFilterEvent )
        {// событие очистки именно этого фильтра
            $this->clearFilterEvent = 'clearFilter_'.$this->filter->shortname;
        }
        $this->isEmptyJsName         = $this->filter->shortname.'_filter_is_empty';
        $this->collectDataJsName     = $this->filter->shortname.'_collect_filter_data';
        $this->toggleHighlightJsName = $this->filter->shortname.'_toggle_filter_highlight';
        
        if ( ! $this->inputSelectors )
        {// определяем все селекторы input-элементов, использующихся в форме
            foreach ( $this->elements as $element )
            {
                $this->inputSelectors[$element] = 'input[name="'.$this->getFullInputName($element).'"]';
            }
        }
        
        // Регистрируем на странице все нужные скрипты
        $this->registerPageScripts();
        
        // Подключаем стили
        // Подключаем CSS для оформления
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('catalog.extensions.search.filters.QSearchFilterBase.assets') .
            DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/searchFilter.css');
        
        // Загружаем данные последнего поиска из сессии (если они есть)
        //$this->loadLastSearchParams();
        
        parent::init();
    }
    
    /**
     * Прописать на странице все используемые JS-скрипты
     * 
     * @return null
     * 
     * @todo предусмотреть вариант с распечаткой скриптов внизу виджета, для случая когда 
     *        форма поиска получается через AJAX
     */
    public function registerPageScripts()
    {
        $js = '';
        // Скрипты очистки формы
        // Очистка по событию "очистить всю форму"
        $js .= $this->createClearFilterFormJs($this->clearSearchEvent);
        // Очистка по событию "очистить только этот фильтр"
        $js .= $this->createClearFilterFormJs($this->clearFilterEvent);
        
        // Сбор данных из фильтра (функция)
        $js .= $this->createCollectFilterDataJs();
        // Сбор данных по событию (со всей формы)
        $js .= $this->createCollectDataJs($this->collectDataEvent);
        // Проверка того, активирован ли фильтр
        $js .= $this->createIsEmptyFilterJs();
        // Включение и выключение подсветки, в зависимости от того, активирован фильтр или нет
        $js .= $this->createToggleHighlightJs();
        // присоединяем эту функцию к каждому input-полю
        $js .= $this->createAttachInputToggleJs();
        
        // Добавляем JS который удаляет все данные из одного фильтра при нажатии на иконку удаления
        $js .= "jQuery('#{$this->clearDataPrefix}').click(function(){jQuery('body').trigger('{$this->clearFilterEvent}');return false;})";
        Yii::app()->clientScript->registerScript('_ecSearchFilteScripts#'.$this->namePrefix, $js, CClientScript::POS_END);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo '<div id="'.$this->contentId.'" class="'.$this->getContentClass().'">';
        // Выводим заголовок виджета
        echo $this->getFullTitle();
        // Выводим саму форму
        echo '<div style="padding-left:5px;padding-right:5px;">'.$this->getContent().'</div>';
        echo "</div>";
        
        $collapsedSelector = null;
        if ( $this->collapsedAtStart() )
        {
            $collapsedSelector = '#'.$this->contentId;
        }
        
        // Устанавливаем опции виджета, который сворачивает разделы
        $options = array(
            'titleSelector' => '#'.$this->titleId,
            'itemSelector'  => '#'.$this->contentId,
            // сворачиваем фрагмент, если в нем нет данных
            'collapsed'     => $collapsedSelector,
            'duration'      => 200,
        );
        // создаем сам виджет
        $this->widget('ext.slidetoggle.ESlidetoggle', $options);
    }
    
    /**
     * Определить, сворачивать ли фрагмент условия при загрузке формы
     * (не сворачиваются только те фрагменты, в которых пользователь ранее указал данные,
     * которые запомнились в сессию)
     * 
     * @return null
     */
    protected function collapsedAtStart()
    {
        $data = $this->loadLastSearchParams();
        if ( empty($data) )
        {// значений по умолчанию нет - сворачиваем фрпагмент формы
            return true;
        }
        return false;
    }
    
    /**
     * Найти и загрузить из сессии последние использованные данные поиска для этого фильтра 
     * 
     * @return null
     */
    protected function loadLastSearchParams()
    {
        $data = array();
        if ( $this->dataSource == 'session' )
        {// нужно загрузить данные из сессии
            switch ( $this->display )
            {
                case 'filter': $data = CatalogModule::getFilterSearchData($this->namePrefix, $this->section->id); break;
                case 'form':   $data = CatalogModule::getFormSearchData($this->namePrefix); break;
            }
        }elseif ( $this->dataSource == 'db' )
        {// нужно загрузить данные фильтра из базы
            switch ( $this->display )
            {
                case 'vacancy': $data = $this->vacancy->getFilterSearchData($this->namePrefix); break;
            }
        }
        return $data;
    }
    
    /**
     * Получить полное имя для одного из полей внутри фрагмента формы поиска
     * Все поля внутри фрагмента формы должны получать имя при помощи этого метода
     * Это нужно для того чтобы было удобно собирать условие из частей.
     * Один массив внутри POST = одно условие
     * 
     * @param string $elementName - название поля внутри формы фрагмента условия
     * @return string
     */
    protected function getFullInputName($elementName)
    {
        return $this->namePrefix.'['.$elementName.']';
    }
    
    /**
     *
     * @return string
     */
    public static function defaultPrefix()
    {
        return 'QSearch';
    }
    
    /**
     * Получить заголовок фрагмента со всех необходимой разметкой и кнопкой очистки значения
     * (этот метод общий для всех фрагментов формы)
     * 
     * @return string
     */
    protected function getFullTitle()
    {
        $iconDisplay = 'block';
        if ( $this->collapsedAtStart() )
        {// показываем иконку очистки фильтра только если в нем присутствуют данные
            $iconDisplay = 'none';
        }
        // сама иконка
        $clearIcon = CHtml::image($this->_assetUrl.'/remove.png', 'Очистить', array(
            'title' => 'Очистить',
            'rel'   => 'tooltip',
            'class' => 'pull-right',
            'style' => "display:{$iconDisplay};margin-top:2px;",
            'id'    => $this->clearDataPrefix,
        ));
        
        // весь заголовок
        $title = '<h5 id="'.$this->titleId.'" class="'.$this->getTitleClass().'">'.$this->getTitle();
        $title .= $clearIcon;
        $title .= '</h5>';
        
        return $title;
    }
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        throw new CHttpException('500', 'getTitle() должен быть наследован');
    }
    
    /**
     * получить все html-содержимое виджета фрагмента формы поиска
     * (функция заменяет run() для обычных виджетов)
     *
     * @return string
     */
    protected function getContent()
    {
        throw new CHttpException('500', 'getContent() должен быть наследован');
    }
    
    /**
     * 
     * @return string
     */
    protected function getTitleClass()
    {
        if ( $this->collapsedAtStart() )
        {
            return "ec-search-filter-title";
        }
        //return "ec-search-filter-title ec-search-filter-title-active";
        return "ec-search-filter-title btn-primary";
    }
    
    /**
     * 
     * @return string
     */
    protected function getContentClass()
    {
        if ( $this->collapsedAtStart() )
        {
            return "ec-search-filter-content";
        }
        return "ec-search-filter-content ec-search-filter-content-active";
    }
    
    /**
     * Получить js-код для очистки выбранных пользователем значений в фрагменте формы
     * (очищается и форма и сохраненные данные в сессии, сворачивается блок, изменяются цвета)
     * @param string $eventName - название jQuery-события, которое вызывает очистку данных фильтра
     *                             общее правило: событие, очищающее этот фрагмент формы всегда называется 
     *                             "clear_filter_".$this->filter->shortname
     * @return string
     * @todo обновлять данные в сессии по AJAX
     */
    protected function createClearFilterFormJs($eventName)
    {
        $clearFormJs    = $this->createClearFormDataJs();
        if ( $this->dataSource == 'session' )
        {// нужно очистить данные в сессии
            $clearDataJs = $this->createClearSessionDataJs();
        }else
        {// нужно очистить данные в БД
            $clearDataJs = $this->createClearVacancyDataJs();
        }
        $fadeOutJs      = $this->createFadeOutJs();
        // Код, очищающий данные, реагирует на события очистки всей формы и очистки этого элемента
        // общее правило: событие, очищающее этот фрагмент формы всегда называется 
        // "clear_filter_".$this->namePrefix
        return "$('body').on('{$eventName}', function(event) {
            if ( ! jQuery('#{$this->titleId}').hasClass('slidetoggle-collapsed') ) {
                jQuery('#{$this->titleId}').trigger('click');
            }
            //console.log('trigger_clear');
            {$clearFormJs}
            {$fadeOutJs}
            {$clearDataJs}
        });";
    }
    
    /**
     * Получить js-код для очистки выбранных пользователем значений в фрагменте формы
     * (Этот JS очищает только данные на стороне клиента. Код уникальный для каждого элемента)
     * 
     * @return string
     */
    protected function createClearFormDataJs()
    {
        throw new CHttpException('500', 'clearFormJs() должен быть наследован');
    }
    
    /**
     * Получить JS-код для очистки данных формы поиска для вакансии
     * 
     * @return string
     * 
     * @todo удалить эту функцию и создать всесто нее более общую, которая 
     *       послылает AJAX-запрос с любыми данными по любому адресу
     */
    protected function createClearVacancyDataJs()
    {
        // создаем URL для AJAX-запроса
        $url = Yii::app()->createUrl($this->clearUrl,
        array(
            'namePrefix' => $this->namePrefix,
            'id' => $this->vacancy->id,
        ));
        // Устанавливаем данные для запроса и выполняем его
        return "var ajaxData = {
                namePrefix : '{$this->namePrefix}',
                id  : '{$this->vacancy->id}',
                ".Yii::app()->request->csrfTokenName." : '".Yii::app()->request->csrfToken."'
            };
            var ajaxOptions = {
                url: '$url',
                data : ajaxData,
                type : 'post'
            };
        jQuery.ajax(ajaxOptions);";
    }
    
    /**
     * Js-код для "подсветки" блока (фильтра) в котором что-то выбрано
     * Используется, когда условие поиска активируется
     * 
     * @return string
     */
    protected function createHighlightJs()
    {
        $js = "jQuery('#{$this->titleId}').addClass('btn-primary');";
        $js .= "jQuery('#{$this->contentId}').addClass('ec-search-filter-content-active');";
        $js .= "jQuery('#{$this->clearDataPrefix}').show();";
        
        return $js;
    }
    
    /**
     * JS-код, который убирает подсветку, когда фильтр поиска деактивируется
     * 
     * @return string
     */
    protected function createFadeOutJs()
    {
        $js = "jQuery('#{$this->titleId}').removeClass('btn-primary');";
        $js .= "jQuery('#{$this->contentId}').removeClass('ec-search-filter-content-active');";
        $js .= "jQuery('#{$this->clearDataPrefix}').hide();";
        
        return $js;
    }
    
    /**
     * Создать функцию, которая проверяет, пуст фильтр или нет (при каждом изменении данных в форме)
     * Если фильтр оказывается пуст - убирает подсветку заголовка и полей, и очищает данные в сессии
     * Если фильтр активируется - добавляет подсветку
     * 
     * @return string
     * 
     * @todo добавить сюда функции $clearSessionJs когда будет закончена функция createRefreshSessionDataOnChangeJs()
     */
    protected function createToggleHighlightJs()
    {
        $highlightJs = $this->createHighlightJs();
        $fadeOutJs   = $this->createFadeOutJs();
        
        return "function {$this->toggleHighlightJsName}() {
            if ( {$this->isEmptyJsName}() )
            {
                {$fadeOutJs}
            }else
            {
                {$highlightJs}
            }
        };";
    }
    
    /**
     * Создать функцию, которая проверяет, пуст фильтр или нет 
     * @return string
     * 
     * @todo добавить поддержку всего что ниже IE9 
     * ( http://stackoverflow.com/questions/5533192/how-to-get-object-length-in-jquery )
     */
    protected function createIsEmptyFilterJs()
    {
        $collectDataJs = $this->createCollectFilterDataJs();
        return "function {$this->isEmptyJsName}() {
            var data = {$this->collectDataJsName}();
            //console.log(Object.keys(data).length == 0);
            if ( Object.keys(data).length == 0 ) return true;
            return false;
        };";
    }
    
    /**
     * Получить js-код для очистки сессии выбранных пользователем значений в этом фрагменте формы
     * Данные очищаются AJAX-запросом
     * (этот код общий для всех фрагментов формы)
     *
     * @return string
     */
    protected function createClearSessionDataJs()
    {
        $sectionId = 0;
        if ( is_object($this->section) )
        {
            $sectionId = $this->section->id;
        }
        // создаем URL для AJAX-запроса
        $url = Yii::app()->createUrl($this->clearUrl,
            array(
                'namePrefix' => $this->namePrefix,
                'sectionId'  => $sectionId,
        ));
        // Устанавливаем данные для запроса и выполняем его
        return "var ajaxData = {
            namePrefix : '{$this->namePrefix}',
            sectionId  : '{$sectionId}',
            ".Yii::app()->request->csrfTokenName." : '".Yii::app()->request->csrfToken."'
        };
        var ajaxOptions = {
            url: '$url',
            data : ajaxData,
            type : 'post'
        };
        jQuery.ajax(ajaxOptions);";
    }
    
    /**
     * Получить JS-код, который при изменении данных в форме поиска сразу же изменяет их в сессии
     * Функция собирает данные фрагмента из формы, а затем отправляет их AJAX-запросом на сервер
     * (Этот код общий для всех виджетов поиска. Различаются только методы сбора данных)
     * 
     * @return string
     * 
     * @todo дописать эту функцию
     */
    protected function createRefreshSessionDataOnChangeJs()
    {
        $sectionId = 0;
        if ( is_object($this->section) )
        {
            $sectionId = $this->section->id;
        }
        // создаем URL для AJAX-запроса
        $url = Yii::app()->createUrl('',
        array(
            'namePrefix' => $this->namePrefix,
            'sectionId'  => $sectionId,
        ));
        // @todo конструируем и выполняем сам запрос
        
    }
    
    /**
     * Получить JS-код, который реагирует на событие собирает все данные из фрагмента формы в JSON-массив
     * (для отправки по AJAX, чтобы тут же обновлять данные поиска в сессии или динамически обновлять содержимое поиска)
     * (Этот метод - общий для всех фильтров)
     * @param string $eventName - название jQuery-события, которое вызывает сбор данных с фильтра
     * 
     * @return string
     */
    protected function createCollectDataJs($eventName)
    {
        return "$('body').on('{$eventName}', function(event, data) {
            data.{$this->namePrefix} = {$this->collectDataJsName}();
        });";
    }
    
    /**
     * JS-код, писоединяющий к каждому input-элементу функцию, которая при изменении значения
     * определяет, нужно ли изменить внешний вид виджета или отправить AJAX для изменения данных
     * и производит эти операции если надо
     * 
     * @return string
     */
    protected function createAttachInputToggleJs()
    {
        $js = '';
        foreach ( $this->inputSelectors as $selector )
        {
            $js .= "jQuery('{$selector}').change({$this->toggleHighlightJsName});";
        }
        
        return $js;
    }
    
    /**
     * Получить JS-код, который реагирует на событие собирает все данные из фрагмента формы в JSON-массив
     * (для отправки по AJAX, чтобы тут же обновлять данные поиска в сессии или динамически обновлять содержимое поиска)
     * (этот метод - индивидуальный для каждого фильтра)
     * 
     * @return string
     */
    protected function createCollectFilterDataJs()
    {
        throw new CHttpException('500', 'createCollectFormDataJs() должен быть наследован');
    }
}