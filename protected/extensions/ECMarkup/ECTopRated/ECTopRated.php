<?php

/**
 * Виджет для отображения лучших актеров на главной
 * 
 * @todo возможно стоит запихнуть его в orbit slider
 */
class ECTopRated extends CWidget
{
    /**
     * @var CArrayDataProvider
     */
    protected $dataProvider;
    /**
     * @var string - ссылка на папку с ресурсами расширения
     */
    protected $_assetUrl;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->publishAssets();
        // Подключаем все классы, которые нужны для отображения анкеты
        Yii::import('questionary.models.*');
        // Получаем пользователей по рейтингу
        $criteria = new CDbCriteria();
        $criteria->addCondition("`status` = 'active'");
        $criteria->order = '`rating` DESC, `timecreated` DESC';
        $criteria->limit = $this->getTotalCount();
        $users = Questionary::model()->findAll($criteria);
        
        $elements = array();
        foreach ( $users as $user )
        {
            $element = array();
            $element['id']      = $user->id;
            $element['preview'] = $user->getAvatarUrl('catalog');
            $element['name']    = $user->fullname;
            $element['link']    = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $user->id));
            
            $elements[] = $element;
        }
        
        $this->dataProvider = new CArrayDataProvider($elements, array(
            'pagination' => false)
        );
    }
    
    /**
     * 
     * @return void
     */
    protected function publishAssets()
    {
        // загружаем стили и скрипты слайдера
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::app()->extensionPath . DIRECTORY_SEPARATOR .
            'ECMarkup' . DIRECTORY_SEPARATOR .
            'ECTopRated' . DIRECTORY_SEPARATOR .
            'assets'   . DIRECTORY_SEPARATOR);
        
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/css/slider_index.css');
        Yii::app()->clientScript->registerScriptFile($this->_assetUrl.'/js/jquery.carouFredSel-6.2.1.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($this->_assetUrl.'/js/ecslider.js', CClientScript::POS_END);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('slider');
    }
    
    /**
     * Определить количество актеров в списке
     * @return int
     * 
     * @todo брать значение из настроек
     */
    protected function getTotalCount()
    {
        return 24;
    }
    
    /**
     * Получить количество одновременно отображаемых участников
     * @return number
     * 
     * @todo брать значение из настроек
     */
    protected function getDisplayCount()
    {
        return 7;
    }
}