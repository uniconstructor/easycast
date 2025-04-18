<?php

/**
 * Базовый класс для всех виджетов-фильтров поиска по анкете.
 * 
 * Все формы фильтрации и поиска собираются из маленьких частей
 * Для каждого способа отсеивания анкет существует свой виджет
 * Все виджеты пользуются плагином ESearchScopes для того чтобы составлять критерии поиска
 * Все параметры поиска хранятся в или сессии 
 * (настройки поиска по всей базе и настройки фильтров каждого раздела пока хранятся отдельно)
 * или в базе данных, как сериализованный JSON-массив
 * (в случае с критериями вакансии, оповещениями, оналйн-кастингом и т. д.)
 * 
 * ВАЖНО: Все виджеты фильтров используются только для отображения формы.
 * Каждый виджет умеет загружать свои данные из базы или сессии. 
 * Обработка и составление всех поисковых запросов происходит при помощи компонентов. Для каждого виджета
 * поисковых условий должен существовать класс обработки его данных.
 * Классы виджетов поиска должны находится в папке:       catalog.extensions.search.filters
 * Классы обработчиков фильтров должны находится в папке: catalog.extensions.search.handlers
 * Несколько виджетов могут ссылаться на один обработчик.
 * 
 * Если вы создаете новый фильтр поиска - то после создания его виджета и обработчика нужно миграцией добавить 
 * запись о них в таблицу {{catalog_filters}}
 * 
 * @todo добавить возможность устанавливать поле $data вручную (нужно для форм поиска, работающих с еще не созданными объектами)
 * @todo всегда использовать $this->filter->shortName вместо $this->namePrefix
 * @todo перенести все скрипты в assets
 * @todo добавить реакцию на событие "собрать данные только с этого фильтра"
 * @todo прикреплять jQuery-события не к body, а к родительским элементам. Передавать id элемента как параметр виджета
 * @todo перенести общую html-разметку внешнего вида виджета в шаблон (views)
 * @todo убрать поля section и vacancy - заменить их одним более общим объектом - 
 *       "тот у которого есть данные о предыдущем значении в форме". Это нужно для того, чтобы прикреплять фильтры
 *       поиска к любым объектам, а не только к вакансиям или разделам
 * @todo убрать использование collectDataVar
 * @todo убрать использование $this->scope (оно нигде не используется, решено хранить критерии как json в базе)
 * @todo добавить настройку "посылать js-событие при изменении значения фильтра" (по умолчанию false).
 * @todo добавить функцию "получить код отключения". По умолчанию false (никогда не отключаем виджет). Возвращает
 *       js-код, который очищает и скрывает фильтр в зависимости от js-событий или значений других фильтров.
 *       (понадобится, чтобы динамически очищать и скрывать "ночные съемки" или "стриптиз" для детей)
 * @todo отображать активные, но запрещенные к изменению фильтры не синим, а серым цветом заголовка
 * @todo написать общую функцию, создающую js для отключения/включения всех полей одного виджета
 *       (выполняется при загрузке виджета)
 * @todo придумать каки не отображать виджет, пока его скрипты не подгрузились и не выполнились
 *       (чтобы недоделанные формы не напрягали пользователя раньше времени). Как вариант - изначально
 *       отображать все div-блоки как display:none 
 * @todo найти удалить из остального кода все упоминания QSearchFilterBase::defaultPrefix() - этот параметр
 *       оказался часто нужен в других частях системы, поэтому он
 *       был перенесен в класс модуля, чтобы его было легче достать
 * @todo добавить новый режим отображения: "helper"
 *       Используется при поиске в разделе каталога или в отдельной вкладке раздела
 *       Фильтр отображается наверху в разделе каталога, рядом с названием раздела
 */
class QSearchFilterBase extends CWidget
{
    /**
     * @var string - как отображать фрагмент формы (form/filter): для большой формы или для списка фильтров
     */
    public $display;
    /**
     * @var CActiveRecord - модель к которой прикреплены критерии поиска
     *                  Этот объект обязательно должен обладать отношением (Relation)
     *                  которое назызывается searchFilters, которое содержит
     *                  используемые фильтры поиска
     */
    public $searchObject;
    /**
     * @var CatalogSection - раздел анкеты в котором отображается фильтр 
     *                       (если фильтры используются в разделах каталога)
     * @deprecated использовать searchObject вместо section и vacancy
     */
    public $section;
    /**
     * @var EventVacancy - вакансия, к которой прикремлен фильтр поиска
     * @deprecated использовать searchObject вместо section и vacancy
     */
    public $vacancy;
    /**
     * @var CatalogFilter - фильтр в таблице catalog_filters
     */
    public $filter;
    /**
     * @var string - префикс для названия всех input-полей (по умолчанию - короткое название фильтра)
     */
    public $namePrefix;
    /**
     * @var string - название jQuery события, посылаемого при очистке всей формы
     */
    public $clearSearchEvent = 'clearSearch';
    /**
     * @var string - название jQuery события, посылаемого при очистке только этого фрагмента формы.
     *               Не должно пересекаться с именами других фильтров, поэтому задается для каждого фильтра свое.
     *               По умолчанию определяется как 'clearFilter_'.$this->filter->shortname
     */
    public $clearFilterEvent;
    /**
     * @var string - название jQuery события, посылаемого при сборе данных со всей формы
     */
    public $collectDataEvent = 'collectData';
    /**
     * @var string - название jQuery события, посылаемого для обновления результатов поиска
     */
    public $refreshDataEvent = 'refreshData';
    /**
     * @var string - название jQuery события, посылаемого для подсчета обновления количества подходящих участников
     */
    public $countDataEvent   = 'countData';
    /**
     * @var string - название шлобальной переменной JavaScript, в которую собираются данные со всех фильтров
     */
    public $collectDataVar   = 'ecSearchData';
    /**
     * @var string - название jQuery события, посылаемого при изменении данных в этом фрагменте формы.
     *               Не должно пересекаться с именами других фильтров, поэтому задается для каждого фильтра свое.
     *               По умолчанию определяется как 'changedFilter_'.$this->filter->shortname
     *               Используется только в тех виджетах, для которых включена отправка события при изменении значения,
     *               т. е. $this->raizeEventOnChange = true;              
     */
    public $changeFilterEvent;
    /**
     * @var string - источник данных для формы (откуда будут взяты значения по умолчанию)
     *               Возможные значения:
     *               'session'  - данные берутся из сессии (используется во всех формах поиска)
     *               'db'       - данные берутся из базы (используется при сохранении критериев вакансии и т. п.)
     *               'external' - данные виджета переданы в параметре $this->data при создании формы 
     */
    public $dataSource = 'session';
    /**
     * @var array - параметры выставленные в этом виджете по умолчанию
     *              пример для фильтра по возрасту (age):
     *              array(
     *                  'agemin' => 18,
     *                  'agemax' => 28,
     *              )
     */
    public $data = array();
    /**
     * @var SearchScope|null - критерий поиска из таблицы SearchScopes
     *                         Используется только если данные для формы берутся из базы ($this->dataSource = 'db')
     *                         Если данные берутся из базы, а условие не задано - то оно будет создано
     *                         Правило: для всех критериев поиска, созданных через форму в объекте SearchScope  
     *                         поле shortname => $this->filter->shortname
     *                         поле type => 'filter'
     *                         
     * @todo механизм хранения данных формы в базе изменен - удалить при рефакторинге 
     * @deprecated
     */
    public $scope;
    /**
     * @var string - URL по которому отправляется AJAX-запрос на очистку данных для одного фильтра 
     */
    public $clearUrl = '/catalog/catalog/clearSessionSearchData';
    /**
     * @var array - post-параметры, отправляемые при запросе очистки данных
     */
    public $clearUrlParams = array();
    /**
     * @var bool - разрешить ли очистку этого фильтра (маленькой круглой кнопочкой внизу)?
     *             По умолчанию все фильтры очищать можно.
     *             Эта настройка нужна для того, чтобы запретить удалять или изменять критерии для уже опубликованной
     *             (активной) вакансии 
     */
    public $allowClear  = true;
    /**
     * @var bool - разрешить ли изменять критерии поиска по этому фильтру?
     *             В большинстве случаев, конечно же, можно.
     *             При значении false фильтр будет отображаться, но все поля в нем будут неактивны (выключены)
     *             Эта настройка нужна для того, чтобы запретить удалять или изменять критерии для уже опубликованной
     *             (активной) вакансии, но при этом отображать установленные ранее значения
     */
    public $allowChange = true;
    /**
     * @var bool - сохранять ли данные поисковой формы в сессию, при каждом изменении значения в форме
     *             (не рекомендуется, особенно для слайдеров)
     *             Полезно для устанавливать для админки, чтобы операторы могли быстро создавать критерии поиска,
     *             или для зарегистрированных заказчиков, чтобы сделать их поиск еще удобнее
     */
    public $refreshDataOnChange = false;
    /**
     * @var bool - отсылать событие при каждом изменении значения виджета
     *             Используйте только для тех критериев поиска, изменения которых ДЕЙСТВИТЕЛЬНО нужно
     *             отслеживать в реальном времени
     * @todo переименовать в raise
     */
    public $raizeEventOnChange = false;
    /**
     * @var bool - обновлять количество найденых участников при каждом изменении фильтра
     */
    public $countDataOnChange = true;
    
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
     *              form    - большая форма поиска: критерии поиска не сворачиваются, список разделов отображается
     *                        как большая полоска со списком кнопок наверху
     *              filter  - набор фильтров поиска для раздела каталога или для поиска по всей базе
     *                        Отображается как вертикальная колонка поисковых условий справа
     *              vacancy - набор фильтров для создания критериев отбора людей на роль
     *                        Используется в админке при создании роли.
     *                        @todo использовать в онлайн-кастинге вместо filter как сейчас
     *              section - набор фильтров для создания/редактирования условий раздела каталога
     *                        Набор фильтров отображается как вертикальная колонка поисковых условий справа
     *              tab     - набор фильтров для создания/редактирования условий вкладки в разделе каталога
     *                        Отображается как вертикальная колонка поисковых условий справа
     */
    protected $displayModes = array('form', 'filter', 'vacancy', 'section', 'tab');
    /**
     * @var array - промежуточная переменная для хранения условий поиска 
     * (если условия поиска создаются по данным из формы)
     */
    protected $_conditions;
    /**
     * @var string - путь к папке со стилями и скриптами
     */
    protected $_assetUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->enabled() )
        {// фильтр отключен - ничего не подгружаем и не показываем
            return;
        }
        if ( ! $this->visible() )
        {// фильтр используется, но скрыт: не отображаем виджет, но будем учитывать его при сборке запроса
            // в классе QSearchCriteriaAssembler
            return;
        }
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
            throw new CException('Неправильный тип отображения');
        }
        if ( ! $this->filter )
        {
            throw new CException('Не задана связь виджета с фильтром в базе. $this->filter должен быть задан');
        }
        if ( $this->dataSource === 'db' AND ! is_object($this->searchObject) )
        {
            throw new CException('Не указан раздел для фильтра');
        }
        if ( ! $this->namePrefix )
        {// если префикс не задан вручную - то берем его из базы
            $this->namePrefix = $this->filter->shortname;
        }
        if ( ! $this->elements )
        {// Не задан список полей для поискового виджета
            throw new CException('Не задан список полей для поискового виджета. $this->internalElements должен быть задан');
        }
        
        // Устанавливаем одно общее имя массива для всех input-полей формы фрагмента поиска
        $this->namePrefix = self::defaultPrefix().$this->namePrefix;
        
        // Устанавливаем id элемента, при клике на который данные формы очищаются
        $this->clearDataPrefix = 'clear_search_fragment_'.$this->namePrefix;
        // Устанавливаем id элементов для сворачивания/разворачивания блока с формой
        $this->titleId   = 'filter_title_'.$this->namePrefix;
        $this->contentId = 'filter_content_'.$this->namePrefix;
        
        // имена событий, которые перехватывает или посылает этот виджет
        if ( ! $this->clearSearchEvent )
        {// очищаем и сворачиваем виджет, если видим событие очистки всей формы
            $this->clearSearchEvent = 'clearSearch';
        }
        if ( ! $this->clearFilterEvent )
        {// очищаем и сворачиваем виджет, если видим событие очистки именно этого фильтра
            $this->clearFilterEvent = 'clearFilter_'.$this->filter->shortname;
        }
        if ( ! $this->changeFilterEvent )
        {// задаем название события, посылаемого при изменении данных в виджете
            $this->clearFilterEvent = 'changedFilter_'.$this->filter->shortname;
        }
        // названия js-функций для проверки пустоты фильтра, сбора всех данных или включения подсветки 
        $this->isEmptyJsName         = $this->filter->shortname.'_filter_is_empty';
        $this->collectDataJsName     = $this->filter->shortname.'_collect_filter_data';
        $this->toggleHighlightJsName = $this->filter->shortname.'_toggle_filter_highlight';
        
        if ( ! $this->inputSelectors )
        {// определяем все css-селекторы input-элементов, использующихся в форме
            foreach ( $this->elements as $element )
            {
                $this->inputSelectors[$element] = 'input[name="'.$this->getFullInputName($element).'"]';
            }
        }
        
        // Регистрируем на странице все нужные скрипты
        $this->registerPageScripts();
        
        // Подключаем стили
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('catalog.extensions.search.filters.QSearchFilterBase.assets').DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/searchFilter.css');
        
        parent::init();
    }
    
    /**
     * Прописать на странице все используемые JS-скрипты
     * 
     * @return null
     * 
     * @todo предусмотреть вариант с распечаткой скриптов внизу виджета, для случая когда 
     *       форма поиска получается через AJAX
     * @todo подключать скрипты очистки виджета только если его разрешено очищать
     *       (нужен тест, а сейчас некогда)
     */
    public function registerPageScripts()
    {
        $js = '';
        // Скрипты очистки данных виджета
        if ( $this->allowClear )
        {// (подключаются только если очистка этого виджета разрешена)
            // Очистка по событию "очистить всю форму"
            $js .= $this->createClearFilterFormJs($this->clearSearchEvent);
            // Очистка по событию "очистить только этот фильтр"
            $js .= $this->createClearFilterFormJs($this->clearFilterEvent);
        }
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
        if ( $this->allowClear )
        {
            $js .= "jQuery('#{$this->clearDataPrefix}').click(function(){jQuery('body').trigger('{$this->clearFilterEvent}');return false;})";
            Yii::app()->clientScript->registerScript('_ecSearchFilteScripts#'.$this->namePrefix, $js, CClientScript::POS_END);
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->enabled() )
        {// фильтр отключен - ничего не подгружаем и не показываем
            return;
        }
        if ( ! $this->visible() )
        {// фильтр используется, но скрыт: не отображаем виджет, но будем учитывать его при сборке запроса
            // в классе QSearchCriteriaAssembler
            return;
        }
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
        
        if ( $this->display === 'filter' )
        {// создаем сам виджет (сворачивающийся блок с фильтром)
            // (для режима отображения "фильтр")
            $this->widget('ext.slidetoggle.ESlidetoggle', $options);
        }
    }
    
    /**
     * Сворачивать ли фрагмент условия при загрузке формы?
     * (Не сворачиваются только те фрагменты, в которых пользователь ранее указал данные,
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
     * Можно ли текущему пользователю видеть этот фильтр?
     * (По умолчанию все фильтры видны. Функция нужна для того чтобы скрыть от посторонних
     * поиск по цене или контактным данным. Также фильтр может быть отключен в зависимости от любых
     * других условий. Например - никогда не показывать фильтр "стриптиз" в разделе "дети", даже если его
     * туда вдруг специально добавили)
     * @return boolean
     */
    public function enabled()
    {
        return true;
    }
    
    /**
     * Отображать ли фильтр в форме?
     * Отличие от enabled: если фильтр выключен - то он не отображается в форме и не используется в 
     * составлении поискового запроса
     * Если фильтр не виден - то он не отображается в форме, но в составлени запроса используется 
     * @return boolean
     * 
     * @todo брать информацию из filter_instances в зависимости от типа привязанного объекта
     */
    public function visible()
    {
        return true;
    }
    
    /**
     * Найти и загрузить последние использованные данные поиска для этого фильтра 
     * 
     * @return array
     * 
     * @todo убрать из этого класса логику загрузки значений по умолчанию и перенести ее в класс SearchFilters
     */
    protected function loadLastSearchParams()
    {
        $data = array();
        if ( $this->dataSource === 'session' )
        {// нужно загрузить данные из сессии
            if ( isset($this->searchObject->id) AND $this->searchObject->id )
            {
                return CatalogModule::getFilterSearchData($this->namePrefix, $this->searchObject->id);
            }else
            {
                return CatalogModule::getFilterSearchData($this->namePrefix, 1);
            }
        }elseif ( $this->dataSource === 'db' )
        {// нужно загрузить данные фильтра из базы
            return $this->searchObject->getFilterSearchData($this->namePrefix);
        }elseif ( $this->dataSource === 'external' )
        {// данные фильтра переданы при создании объекта
            return $this->data;
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
     * Префикс, который добавляется ко всем полям формы поиска, чтобы избежать 
     * конфликта имен input-полей на странице
     *  
     * @return string
     */
    public static function defaultPrefix()
    {
        return CatalogModule::SEARCH_FIELDS_PREFIX;
    }
    
    /**
     * Получить заголовок фрагмента со всех необходимой разметкой и кнопкой очистки значения
     * (этот метод общий для всех фрагментов формы)
     * 
     * @return string
     */
    protected function getFullTitle()
    {
        /*if ( $this->display == 'form' )
        {// не отображать заголовок фильтра если рисуем форму
            return '';
        }*/
        $iconDisplay = 'block';
        if ( $this->collapsedAtStart() OR ! $this->allowClear )
        {// показываем иконку очистки фильтра только если в нем присутствуют данные и очистка данных разрешена
            $iconDisplay = 'none';
        }
        // сама иконка
        $clearIcon = CHtml::tag('i', array(
            'id'          => $this->clearDataPrefix,
            'data-title'  => 'Очистить',
            'data-toggle' => 'tooltip',
            'class'       => 'pull-right icon icon-times-circle',
            'style'       => "display:{$iconDisplay};margin:3px;cursor:pointer;transition: color 0.1s linear 0s;",
        ), '&times');
        
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
        throw new CException('getTitle() должен быть наследован');
    }
    
    /**
     * получить все html-содержимое виджета фрагмента формы поиска
     * (функция заменяет run() для обычных виджетов)
     *
     * @return string
     */
    protected function getContent()
    {
        throw new CException('getContent() должен быть наследован');
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
        $clearFormJs = $this->createClearFormDataJs();
        $clearDataJs = $this->createClearSessionDataJs();
        $fadeOutJs   = $this->createFadeOutJs();
        
        // Код, очищающий данные, реагирует на события очистки всей формы и очистки этого элемента
        // общее правило: событие, очищающее этот фрагмент формы по умолчанию всегда называется 
        // "clear_filter_".$this->namePrefix
        $checkEmptyJs = '';
        
        if ( $this->refreshDataOnChange )
        {// не очищает те фильтры, которые и так пусты, если используется обновление результатов а процессе поиска
            // (возвращает true, не давая вызвать дальнейшие обработчики)
            $checkEmptyJs = "if ( {$this->isEmptyJsName}() ){return true;}";
        }
        return "$('body').on('{$eventName}', function(event) {
            if ( ! jQuery('#{$this->titleId}').hasClass('slidetoggle-collapsed') ) {
                jQuery('#{$this->titleId}').trigger('click');
            }
            {$checkEmptyJs}
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
        throw new CException('clearFormJs() должен быть наследован');
    }
    
    /**
     * Js-код для "подсветки" блока (фильтра) в котором что-то выбрано
     * Используется, когда условие поиска активируется.
     * Изменяет цвет заголовка фильтра и добавляет в заголовок кнопку "очистить" (если этот фильтр разрешено очищать)
     * 
     * @return string
     */
    protected function createHighlightJs()
    {
        $js  = "jQuery('#{$this->titleId}').addClass('btn-primary');";
        $js .= "jQuery('#{$this->contentId}').addClass('ec-search-filter-content-active');";
        if ( $this->allowClear )
        {
            $js .= "jQuery('#{$this->clearDataPrefix}').show();";
        }
        return $js;
    }
    
    /**
     * JS-код, который убирает подсветку, когда фильтр поиска деактивируется
     * @return string
     */
    protected function createFadeOutJs()
    {
        $js  = "jQuery('#{$this->titleId}').removeClass('btn-primary');";
        $js .= "jQuery('#{$this->contentId}').removeClass('ec-search-filter-content-active');";
        if ( $this->allowClear )
        {// скрываем иконку "очистить"
            $js .= "jQuery('#{$this->clearDataPrefix}').hide();";
        }
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
     * @todo переименовать в changeFilterJS - так как эта функция запускается при каждом изменении фильтра
     */
    protected function createToggleHighlightJs()
    {
        $highlightJs = $this->createHighlightJs();
        $fadeOutJs   = $this->createFadeOutJs();
        $refreshJs   = '';
        
        if ( $this->refreshDataOnChange )
        {// нужно обновить результаты поиска при изменении фильтра
            $refreshJs .= '$("body").trigger("'.$this->refreshDataEvent.'");';
        }
        if ( $this->countDataOnChange )
        {// обновить количество подходящих участников при изменении значения
            $refreshJs .= '$("body").trigger("'.$this->countDataEvent.'");';
        }
        return "function {$this->toggleHighlightJsName}() {
            if ( {$this->isEmptyJsName}() )
            {
                {$fadeOutJs}
            }else
            {
                {$highlightJs}
            }
            {$refreshJs}
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
            if ( Object.keys(data).length == 0 )
            {
                return true;
            }
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
        $searchObjectId = 0;
        if ( is_object($this->searchObject) )
        {
            $searchObjectId = $this->searchObject->id;
        }
        // создаем URL для AJAX-запроса
        $url = Yii::app()->createUrl($this->clearUrl, array(
            'namePrefix'     => $this->namePrefix,
            'searchObjectId' => $searchObjectId,
        ));
        // Устанавливаем данные для запроса и выполняем его
        return "var ajaxData = {
            namePrefix :     '{$this->namePrefix}',
            searchObjectId : '{$searchObjectId}',
            ".Yii::app()->request->csrfTokenName." : '".Yii::app()->request->csrfToken."'
        };
        var ajaxOptions = {
            url  : '{$url}',
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
        /*$sectionId = 0;
        if ( is_object($this->section) )
        {
            $sectionId = $this->section->id;
        }
        // создаем URL для AJAX-запроса
        $url = Yii::app()->createUrl('',
        array(
            'namePrefix' => $this->namePrefix,
            'sectionId'  => $sectionId,
        ));*/
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
        throw new CException('createCollectFormDataJs() должен быть наследован');
    }
}