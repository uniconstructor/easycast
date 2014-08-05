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
     * @deprecated
     */
    public $echoScripts = false;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $result = '';
        $items = array();
        
        $videoCriteria = new CDbCriteria();
        $videoCriteria->compare('objecttype', 'questionary');
        $videoCriteria->compare('objectid', $this->questionary->id);
        $videoCriteria->addInCondition('type', array('youtube'));
        
        if ( $records = Video::model()->findAll($videoCriteria) )
        {
            foreach ( $records  as $record )
            {
                if ( $record->type === 'youtube' AND ! $record->externalid )
                {
                    continue;
                }
                $items[] = array('video' => $record->link);
            }
        }
        
        //$criteria = new CDbCriteria();
        //$criteria->compare('gallery_id', $this->questionary->galleryid);
        if ( $records = $this->questionary->getGalleryPhotos() )
        {
            foreach ( $records  as $record )
            {/* @var $record GalleryPhoto */
                $items[] = array(
                    'image'       => $record->getUrl('large'),
                    'thumb'       => $record->getUrl('small'),
                    'big'         => $record->getUrl('large'),
                    'title'       => $record->name,
                    'description' => $record->description,
                );
            }
        }
        $result .= $this->widget('Galleria', array(
            'options' => array(
                'transition'     => 'fade',
                'plugins'        => false,
                'responsive'     => true,
                'lightbox'       => true,
                'dataSource'     => $items,
                'keepSource'     => true,
                'trueFullscreen' => true,
                //'imagePan'       => true,
                'imageCrop'      => false,
            ),
        ), true);
        
        if ( ! $this->questionary->getGalleryPhotos() AND $this->canEditUser() )
        {
            $result .= $this->widget('GalleryManager', array(
                'gallery' => $this->questionary->galleryBehavior->getGallery(),
                'controllerRoute' => '/questionary/gallery'
            ), true);
        }
        echo $result;
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