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
        if ( $this->questionary->video )
        {// есть хотя бы одно видео - отобразим его
            $tabs[] = array(
                'label'   => 'Видео',
                'content' => $this->getVideoTab(),
                // делаем изначально активной вкладку видео только когда нет фото
                'active'  => empty($tabs),
            );
        }
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
        // Список фотографий пользователя
        $result .= $this->widget('ext.ECMarkup.EThumbCarousel.EThumbCarousel', array(
            'previews'    => $this->questionary->getBootstrapPhotos('small'),
            'photos'      => $this->questionary->getBootstrapPhotos('medium'),
            'largePhotos' => $this->questionary->getBootstrapPhotos('large'),
            'emptyText'   => $this->render('_message', null, true),
            'echoScripts' => $this->echoScripts,
            'id'          => 'ethumbcarousel'.$this->questionary->id,
        ), true);
        if ( ! $this->questionary->getGalleryPhotos() AND $this->canEditUser() )
        {
            $result .= $this->widget('GalleryManager', array(
                'gallery' => $questionary->galleryBehavior->getGallery(),
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
        $result = '';
        
        $result .= $this->widget('ext.ECMarkup.ECVideoList.ECVideoList', array(
            'objectType' => 'questionary',
            'objectId'   => $this->questionary->id,
        ), true);
        
        return $result;
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