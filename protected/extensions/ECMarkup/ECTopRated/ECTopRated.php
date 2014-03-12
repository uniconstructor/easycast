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
     * @var string
     */
    protected $viewAll;
    
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
        
        if ( Yii::app()->getModule('user')->getViewMode() === 'user' )
        {
            $elements = $this->getProjectList();
            $this->viewAll  = CHtml::link('Все проекты &gt;', Yii::app()->createUrl('//projects'), array(
                //'target' => '_blank',
                'class'  => 'all_people',
            ));
        }else
        {
            $elements = $this->getUserList();
            $this->viewAll  = CHtml::link('Все участники &gt;', Yii::app()->createAbsoluteUrl('//catalog/catalog/faces'), array(
                //'target' => '_blank',
                'class'  => 'all_people',
            ));
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
     * Получить список участников (при просмотре страницы заказчиком)
     * @return array
     */
    protected function getUserList()
    {
        // Подключаем все классы, которые нужны для отображения анкеты
        Yii::import('questionary.models.*');
        // Получаем пользователей по рейтингу
        $criteria = new CDbCriteria();
        $criteria->compare('status', 'active');
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
        
        return $elements;
    }
    
    /**
     * Получить список последних проектов (при просмо ре страницы участником)
     * @return array
     */
    protected function getProjectList()
    {
        // Подключаем все классы, которые нужны для отображения проекта
        Yii::import('projects.models.*');
        // Получаем пользователей по рейтингу
        $criteria = new CDbCriteria();
        $criteria->compare('status', 'active');
        $criteria->order = '`timemodified` DESC, `rating` DESC ';
        $criteria->limit = $this->getTotalCount();
        $projects = Project::model()->findAll($criteria);
        
        $elements = array();
        foreach ( $projects as $project )
        {
            $element = array();
            $element['id']      = $project->id;
            $element['preview'] = $project->getAvatarUrl('small');
            $element['name']    = CHtml::encode($project->name);
            $element['link']    = Yii::app()->createUrl('//projects/project/view', array('id' => $project->id));
        
            $elements[] = $element;
        }
        
        return $elements;
    }
    
    /**
     * Определить количество актеров в списке
     * @return int
     * 
     * @todo брать значение из настроек
     */
    protected function getTotalCount()
    {
        return 28;
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