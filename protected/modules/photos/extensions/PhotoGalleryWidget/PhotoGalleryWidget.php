<?php

/**
 * Виджет отображающий страницу фотогалереи, видео и список галерей
 */
class PhotoGalleryWidget extends CWidget
{
    /**
     * @var int Максимальное количество галерей на одной странице
     */
    const MAX_GALLERIES = 24;
    
    /**
     * @var int Максимальное количество фотографий на одной странице галереи
     */
    const MAX_PHOTOS = 102;
    
    /**
     * @var int Максимальное количество видео на одной странице
     */
    const MAX_VIDEOS = 12;
    
    public $mode = 'photo';
    
    public $galleryId;
    
    protected $galleries = array();
    
    protected $gallery;
    
    protected $photos = array();
    
    protected $videos = array();
    
    public $_assetUrl;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('application.extensions.galleryManager.models.*');
        Yii::import('application.extensions.galleria.*');
        
        $this->_assetUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/PhotoGalleryWidget.css');
        Yii::app()->clientScript->registerScriptFile($this->_assetUrl.'/PhotoGalleryWidget.js');
        
        if ( $this->mode == 'photo' )
        {
            if ( $this->galleryId )
            {// отображаем одну галерею
                // @todo проверять свойство visible
                if ( ! $this->gallery = PhotoGallery::model()->findByPk($this->galleryId) )
                {
                    throw new CHttpException(404, 'Галерея не найдена');
                }
                
                $this->photos = $this->gallery->galleryPhotos;
            }else
          {// отображаем все галереи
                $criteria = new CDbCriteria();
                $criteria->condition = 'visible = 1';
                $criteria->order = '`timecreated` DESC';
                
                $this->galleries = PhotoGallery::model()->findAll($criteria);
            }
        }
        
        if ( $this->mode == 'video' )
        {
            
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->widget('bootstrap.widgets.TbTabs', array(
            'type' => 'tabs',
            'encodeLabel' => false,
            'tabs' => $this->getTabs()
        ));
    }
    
    /**
     * Получить вкладки галереи
     * @return array
     * 
     * @todo не подгружать видео при просмотре фото
     */
    protected function getTabs()
    {
        $tabs = array();
        
        $tabs[] = $this->getPhotoTab();
        //$tabs[] = $this->getVideoTab();
        
        return $tabs;
    }
    
    /**
     * Отобразить вкладку с видео
     * 
     * @return null
     */
    protected function getVideoTab()
    {
        $tab = array();
        $tab['label'] = 'Видео';
        if ( $this->mode == 'video' )
        {
            $tab['active'] = true;
        }
        
        $tab['content'] = $this->displayGalleries();
        
        return $tab;
    }
    
    /**
     * Отобразить вкладку "фото" (с галереями или фотографиями)
     * 
     * @return array
     */
    protected function getPhotoTab()
    {
        $tab = array();
        $tab['label'] = 'Фото';
        if ( $this->mode == 'photo' )
        {
            $tab['active'] = true;
        }
        if ( $this->galleryId )
        {
            $tab['content'] = $this->displayPhotos();
        }else
       {
            $tab['content'] = $this->displayGalleries();
        }
        
        return $tab;
    }
    
    /**
     * Отобразить список галерей
     * @return string
     */
    protected function displayGalleries()
    {
        $elements = array();
        foreach ( $this->galleries as $photoGallery )
        {
            $element  = array();
            // Получаем id обложки галереи
            $coverId  = $photoGallery->getGallery()->getcoverid();
            // Получаем саму обложку
            $cover    = GalleryPhoto::model()->findByPk($coverId);
            $coverUrl = $cover->getUrl('medium');
            
            // Создаем ссылку на галерею
            $galleryUrl = Yii::app()->createUrl('/photos/photos/index', array('galleryid' => $photoGallery->id));
            // создаем картинку со ссылкой
            $element['id'] = $photoGallery->id;
            $element['image'] = CHtml::link(CHtml::image($coverUrl, $photoGallery->name), $galleryUrl);
            $elements[] = $element;
        }
        
        $dataProvider = new CArrayDataProvider($elements, array( 
            'pagination' => array('pageSize'=>self::MAX_GALLERIES))
        );
        
        return $this->widget('bootstrap.widgets.TbThumbnails', array(
            'dataProvider' => $dataProvider,
            'template'     => "{pager}{items}{pager}",
            'itemView'     => '/_galleryPreview',
        ), true);
    }
    
    /**
     * Отобразить все фотографии одной галереи
     * 
     * @return string
     */
    protected function displayPhotos()
    {
        if ( $gallery = $this->gallery->getGallery() )
        {
            $title   = $this->gallery->name;
            return $this->render('_gallery', array(
                'gallery' => $gallery,
                'title'   => $title,
            ), true);
        }
    }
    
    /**
     * Отобразить список видео
     * 
     * @return string
     */
    protected function displayVideos()
    {
        
    }
}