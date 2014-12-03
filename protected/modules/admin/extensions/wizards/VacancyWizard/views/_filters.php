<?php
/**
 * Шаг редактирования фильтров поиска
 */
/* @var $this VacancyWizard */

$this->render('_progress');

// виджет расширенной формы поиска (по всей базе)
$this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
    'searchObject' => $this->vacancy,
    'mode'         => 'vacancy',
    'dataSource'   => 'db',
    'searchUrl'    => '/admin/eventVacancy/setSearchData',
    'clearUrl'     => '/admin/eventVacancy/clearFilterSearchData',
    'countUrl'     => '/admin/eventVacancy/setSearchData',
    'countResultPosition' => 'bottom',
    'refreshDataOnChange' => true,
    //'refreshDataOnChange' => false,
    'searchButtonTitle'      => 'Сохранить',
    'clearButtonHtmlOptions' => array(
        'class' => 'btn btn-danger btn-large',
        'id'    => 'clear_search',
    ),
    'countContainerHtmlOptions' => array(
        'class' => 'well text-center',
    ),
));
?>
