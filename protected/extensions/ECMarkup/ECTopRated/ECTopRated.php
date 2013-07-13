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
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
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
            $element['full']    = $user->getAvatarUrl('full');
            $element['name']    = $user->fullname;
            $element['link']    = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $user->id));
            
            $elements[] = $element;
        }
        
        $this->dataProvider = new CArrayDataProvider($elements, array(
            'pagination' => array('pageSize'=>count($elements)))
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $topRated = $this->widget('ext.JCarousel.JCarousel', array(
            // загружаем данные всех актеров
            'dataProvider' => $this->dataProvider,
            // устанавливаем ссылку на perview-картинку
            'thumbUrl' => '$data["preview"]',
            'imageUrl' => '$data["link"]',
            // устанавливаем описание для каждой картинки
            'text'     => '"&nbsp;"',
            'altText'  => '$data["name"]',
            'target'   => 'big-gallery-item',
            // количество загружаемых анкет всегда берется из настроек 
            'size'     => $this->dataProvider->itemCount,
            // Пролистываем по 10 актеров за клик
            'scroll'   => $this->getDisplayCount(), 
            // Устанавливаем собственное событие при клике на фотографию актера
            // (просто показываем его анкету)
            'clickCallback' => 'return true;',
            // показываем строго по 10 элементов
            'visible' => $this->getDisplayCount(),
            // показываем изображения по кругу
            'wrap'    => 'circular',
            // если картинку не удалось загрузить - все равно выделяем под нее 150px
            'itemFallbackDimension' => 150,
            // @todo подключить виджет TBThumbnails чтобы выводились черные подсказки
            'linkClass' => '"thumbnail"',
            'dataTitle' => '$data["name"]',
        ), true);
        
        $this->widget('bootstrap.widgets.TbTabs', array(
            'type'        => 'tabs',
            'placement'   => 'above',
            'tabs'        => array(
                array(
                    'label'   => 'Ассорти',
                    'content' => $topRated,
                    'active'  => true,
                ),
            ),
        ));
        echo '<div id="big-gallery-item" style="display:none;"></div>';
    }
    
    /**
     * Определить максимальное количество актеров, показываемых в ассорти
     * 
     * @return int
     * 
     * @todo брать значение из настроек
     */
    protected function getTotalCount()
    {
        return 30;
    }
    
    /**
     * Получить количество отображаемых участников
     * 
     * @return number
     * 
     * @todo брать значение из настроек
     */
    protected function getDisplayCount()
    {
        return 8;
    }
}