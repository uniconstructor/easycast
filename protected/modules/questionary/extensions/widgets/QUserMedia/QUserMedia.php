<?php

/**
 * Виджет для отображения списка фото и видео в анкете участника 
 */
class QUserMedia extends CWidget
{
    /**
     * @var Questionary
     */
    public $questionary;
    /**
     * @var bool - выводить ли скрипты рядом с кодом? (необходимо для загрузки по ajax)
     */
    public $echoScripts = false;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $tabs = array();
        if ( $this->questionary->getGalleryPhotos() )
        {// есть хотя бы одна фотография - отображаем ее
            $tabs[] = array(
                'label'   => 'Фото',
                'content' => $this->getPhotoTab(),
                'active'  => true,
            );
        }
        /*if ( $this->questionary->video )
        {// есть хотя бы одно видео - отобразим его
            $tabs[] = array(
                'label'   => 'Видео',
                'content' => $this->getVideoTab(),
                // делаем изначально активной вкладку видео только когда нет фото
                'active'  => empty($tabs),
            );
        }*/
        $this->widget('bootstrap.widgets.TbTabs', array(
                'type' => 'pills',
                'tabs' => $tabs,
            )
        );
    }
    
    /**
     * 
     * @return string
     */
    protected function getPhotoTab()
    {
        $result = '';
        $items = array();
        
        $videoCriteria = new CDbCriteria();
        $videoCriteria->compare('objecttype', 'questionary');
        $videoCriteria->compare('objectid', $this->questionary->id);
        $videoCriteria->addInCondition('type', array('youtube', 'vimeo'));
        if ( $records = Video::model()->findAll($videoCriteria) )
        {
            foreach ( $records  as $record )
            {
                $items[] = array('video' => $record->link);
            }
        }
        
        $criteria = new CDbCriteria();
        $criteria->compare('gallery_id', $this->questionary->galleryid);
        if ( $records = GalleryPhoto::model()->findAll($criteria) )
        {
            foreach ( $records  as $record )
            {
                $items[] = array(
                    'image'       => $record->getUrl('medium'),
                    'thumb'       => $record->getUrl('small'),
                    'big'         => $record->getUrl(''),
                    'title'       => $record->name,
                    'description' => $record->description,
                    //'link' => 'http://domain.com',
                );
            }
        }
        $result .= $this->widget('Galleria', array(
            'options' => array(
                'transition' => 'fade',
                'responsive' => true,
                'lightbox'   => true,
                'dataSource' => $items,
                'keepSource' => true,
            ),
        ), true);
        
        if ( ! $this->questionary->getGalleryPhotos() AND $this->canEditUser() )
        {
            $result .= $this->widget('GalleryManager', array(
                'gallery' => $this->questionary->galleryBehavior->getGallery(),
                'controllerRoute' => '/questionary/gallery'
            ), true);
        }
        return $result;
    }
    
    /**
     * 
     * @return string
     */
    protected function getVideoTab()
    {
        return '';
    }
    
    /**
     * Узнать, может ли текущий пользователь редактировать анкету
     * (только владелец анкеты или админ)
     * @param int $userId - id пользователя редактируемой анкеты
     * @return bool
     * 
     * @todo временное решение. Заменить эту функцию обращением к RBAC
     */
    protected function canEditUser()
    {
        $userId = $this->questionary->user->id;
        if ( ! Yii::app()->user->isGuest AND
        ( Yii::app()->user->checkAccess('Admin') OR Yii::app()->user->id == $userId ) )
        {
            return true;
        }
        return false;
    }
}