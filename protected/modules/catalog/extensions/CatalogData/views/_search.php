<?php
/**
 * отображение страницы поиска по разделу
 */
?>
<div class="row">
    <div class="span9" id="search_results">
        <?php 
        // при первой загрузке страницы попробуем получить данные поиска из сессии
        $data = CatalogModule::getSessionSearchData();
        if ( isset($data['filter']) AND isset($data['filter'][$this->section->id]) )
        {
            $data = $data['filter'][$this->section->id];
        }else
        {
            $data = array();
        }
        // отображение найденных анкет
        $this->widget('catalog.extensions.search.QSearchResults.QSearchResults', array(
            'mode'    => 'filter',
            'section' => $this->section,
            'data'    => $data,
        ));
        ?>
    </div>
    <div class="span3">
        <?php 
        // форма поиска
        $this->widget('catalog.extensions.search.QSearchFilters.QSearchFilters', array(
            'section' => $this->section,
        ));
        ?>
    </div>
</div>
<?php 
//CVarDumper::dump($_SESSION, 10, true);
//Yii::import('');
/*
$criteria = new CDbCriteria();
$criteria->with = 'addchars';
$criteria->join = 'INNER JOIN';
$criteria->condition = "addchars.value = 'doubles'";

$criteria2 = new CDbCriteria();
$criteria2->with = 'addchars';
$criteria2->join = 'INNER JOIN';

$criteria->mergeWith($criteria2);
CVarDumper::dump($criteria, 10, true);

$records = Questionary::model()->findAll($criteria);
CVarDumper::dump(array_keys($records), 10, true);*/
//CVarDumper::dump($records, 10, true);
?>