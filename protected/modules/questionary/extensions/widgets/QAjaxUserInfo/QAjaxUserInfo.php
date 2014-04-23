<?php

/**
 * Виджет для получения информации об участнике при помощи AJAX-запросов
 * Используется для того чтобы асинхронно подгрузить на страницу какую-либо информацию из анкеты
 */
class QAjaxUserInfo extends CWidget
{
    /**
     * @var string - Как отображать данные. Зависит от того, откуда запрашивается информация.
     */
    public $displayType;
    /**
     * @var array - какие части информации из анкеты отобразить
     *              По умолчанию array('photo', 'info') - фотографии и всю информацию 
     */
    public $sections = array();
    /**
     * @var array - список тех вкладок с информацией, которые нужно отобразить 
     *              (для виджета QUserInfo)
     *              Если не задано - отображаются все доступные для просмотра разделы анкеты
     */
    public $tabNames = array();
    /**
     * @var int - id анкеты по которой отображается информация
     */
    public $id;
    
    /**
     * @var Questionary - данные отображаемой анкеты
     */
    protected $questionary;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->displayType )
        {
            throw new CHttpException('404', 'Не указан тип отображения');
        }
        if ( ! $this->id )
        {
            throw new CHttpException('404', 'Не указан id анкеты');
        }
        
        $this->sections    = $this->getSectionsByDisplayType($this->displayType);
        $this->questionary = Questionary::model()->findByPk($this->id);
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        switch ( $this->displayType )
        {
            case 'myChoice':      $this->displayMyChoice(); break;
            case 'searchResults': $this->displaySearchResults(); break;
            default: $this->displaySearchResults();
        }
    }
    
    /**
     * Отобразить информацию для раздела "мой выбор"
     * 
     * @return null
     */
    protected function displayMyChoice()
    {
        $this->render('myChoice', array(
            'questionary'   => $this->questionary,
        ));
    }
    
    /**
     * Отобразить информацию для раздела "мой выбор"
     * 
     * @return null
     */
    protected function displaySearchResults()
    {
        $this->render('searchResults', array(
            'questionary' => $this->questionary,
        ));
    }
    
    /**
     * Определить, какую информацию из анкеты отобразить в зависимости от типа отображения
     * @param string $displayType - тип отображения
     * @return array
     */
    protected function getSectionsByDisplayType($displayType)
    {
        switch ( $displayType )
        {
            case 'myChoice':      return array('photo', 'info');
            case 'searchResults': return array('photo', 'info');
            default: return array('photo', 'info');
        }
    }
}