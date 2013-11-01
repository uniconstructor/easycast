<?php

// Подключаем виджет, от которого все наследуется
Yii::import('catalog.extensions.search.SearchFilters.SearchFilters');

/**
 * Виджет, позволяющий задавать условия отбора участников в вакансию
 * 
 * @todo вынести весь JS во внешние файлы
 * @todo выводить количество подходящих людей при сохранении критериев
 * @todo выводить количество подходящих людей в сообщении при неактивной форме поиска
 */
class VacancyFilters extends SearchFilters
{
    /**
     * @var string - режим отображения фильтров:
     *               filter - фильтр в разделе каталога
     *               search - большая форма поиска
     *               vacancy - критерии подбора участников для вакансии
     */
    public $mode = 'vacancy';
    
    /**
     * @var string - источник данных для формы (откуда будут взяты значения по умолчанию)
     *               Возможные значения:
     *               'session' - данные берутся из сессии (используется во всех формах поиска)
     *               'db' - данные берутся из базы (используется при сохранении критериев вакансии и т. п.)
     */
    public $dataSource = 'db';
    
    /**
     * @var EventVacancy - вакансия для которой отображаются фильтры поиска
     */
    public $vacancy;
    
    /**
     * @var string - по какому адресу отправлять поисковый ajax-запрос
     */
    public $searchUrl = '/admin/eventVacancy/setSearchData';
    
    /**
     * @var string - по какому адресу отправлять запрос на очистку данных формы
     */
    public $clearUrl = '/admin/eventVacancy/clearFilterSearchData';
    
    /**
     * @var string надпись на кнопке поиска в обычном состоянии
     */
    public $searchButtonTitle = 'Задать условия';
    
    /**
     * @var string надпись на кнопке поиска во время выполнения поиска
     */
    public $searchProgressTitle = 'Сохраняем...';
    
    /**
     * (non-PHPdoc)
     * @see SearchFilters::init()
     */
    public function init()
    {
        if ( $this->vacancy instanceof EventVacancy )
        {
            $this->filterInstances = $this->vacancy->filterinstances;
        }
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see SearchFilters::displayButtons()
     */
    protected function displayButtons()
    {
        if ( $this->vacancy->status == EventVacancy::STATUS_DRAFT )
        {// разрешаем менять критерии подбора людей только для вакансий в статусе "черновик"
            // Кнопка "Найти"
            $this->displaySearchButton();
            echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            // Кнопка "Очистить"
            $this->displayClearButton();
        }else
        {
            echo '<div class="alert alert-info">
            <strong>Критерии поиска заморожены</strong>
            Вакансия уже опубликована или закрыта. Участники оповещены.
            Критерии подбора людей можно изменять только для вакансий в статусе "Черновик".
            </div>';
        }
        echo '<br>';
        echo '<br>';
        // количество подходящих участников
        echo '<div class="alert" id="potential_applicants_count">';
        echo 'Подходящих участников: ';
        echo $this->countPotentialApplicants();
        echo '</div>';
    }
    
    /**
     * (non-PHPdoc)
     * @see SearchFilters::getFilterTitle()
     */
    protected function getFilterTitle()
    {
        return "<h4>Критерии отбора для вакансии</h4>";
    }
    
    /**
     * (non-PHPdoc)
     * @see SearchFilters::getAjaxSearchParams()
     */
    protected function getAjaxSearchParams()
    {
        return array(
            'id'   => $this->vacancy->id,
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken);
    }
    
    /**
     * (non-PHPdoc)
     * @see SearchFilters::getDisplayFilterOptions()
     */
    protected function getDisplayFilterOptions($filter)
    {
        return array(
            'vacancy' => $this->vacancy,
            'filter'  => $filter,
            'display' => $this->mode,
            'dataSource' => $this->dataSource,
            'clearUrl' => $this->clearUrl,
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see SearchFilters::createSuccessSearchJs()
     */
    protected function createSuccessSearchJs()
    {
        return "function(data, status){
            $('#search_button').attr('class', 'btn btn-success');
            $('#search_button').val('{$this->searchButtonTitle}');
            $('#potential_applicants_count').html(data);
        }";
    }
    
    /**
     * Подсчитать количество участников, которые подходят по условиям вакансии (потенциальных соискателей)
     *
     * @param bool $excludeApproved - исключить из выборки тех, кто уже утвержден на эту вакансию
     * @return int
     */
    protected function countPotentialApplicants($excludeApproved=false)
    {
        return $this->vacancy->countPotentialApplicants($excludeApproved);
    }
}