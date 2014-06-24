<?php

/**
 * Контроллер загрузки изображений (для участников)
 * Следит за тем, чтобы участники могли редактировать и удалять только свои фотографии
 * (оригинальный galleryManager не поддерживает такое разделение прав)
 * 
 * @todo сделать проверку прав через роль с bizRule
 */
class QGalleryController extends GalleryController
{
    /**
     * @see GalleryController::actionDelete()
     */
    public function actionDelete()
    {
        $id = Yii::app()->request->getPost('id', 0);
        if ( $photo = $this->loadGalleryPhoto($id) )
        {
            $this->checkPhotoOwner($photo);
        }
        parent::actionDelete();
        
    }
    /**
     * @see GalleryController::actionAjaxUpload()
     */
    public function actionAjaxUpload($gallery_id = null)
    {
        if ( $gallery = $this->loadGallery($gallery_id) )
        {
            $this->checkGalleryOwner($gallery);
        }
        parent::actionAjaxUpload($gallery_id);
    }
    
    /**
     * @see GalleryController::actionSetCoverId()
     */
    public function actionSetCoverId()
    {
        $id = intval(Yii::app()->request->getPost('coverid', 0));
        if ( $photo = $this->loadGalleryPhoto($id) )
        {
            $this->checkPhotoOwner($photo);
        }
        parent::actionSetCoverId();
    }
    
    /**
     * 
     * @return void
     */
    protected function checkGalleryOwner($gallery)
    {
        if ( ! $ownerId = $this->getGalleryOwnerId($gallery) )
        {// владелец отсутствует, галерея создается гостем при регистрации - права проверять не надо
            return true;
        }
        if ( $ownerId != Yii::app()->user->id AND ! Yii::app()->user->checkAccess('Admin')  )
        {
            throw new CHttpException(400, 'Wrong gallery');
            Yii::app()->end();
        }
    }
    
    /**
     * 
     * @param GalleryPhoto $photo
     * @return void
     */
    protected function checkPhotoOwner($photo)
    {
        if ( ! $ownerId = $this->getPhotoOwnerId($photo) )
        {// владелец отсутствует, галерея создается гостем при регистрации - права проверять не надо
            return true;
        }
        if ( $ownerId != Yii::app()->user->id AND ! Yii::app()->user->checkAccess('Admin')  )
        {
            throw new CHttpException(400, 'Wrong photo');
            Yii::app()->end();
        }
    }
    
    /**
     * 
     * @param Gallery $gallery
     * @return int
     */
    protected function getGalleryOwnerId($gallery)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('galleryid', $gallery->id);
        if ( $questionary = Questionary::model()->find($criteria) )
        {
            return 0;
        }
        if ( ! $questionary->user )
        {
            return 0;
        }
        return $questionary->user->id;
    }
    
    /**
     * 
     * @param GalleryPhoto $photo
     * @return int
     */
    protected function getPhotoOwnerId($photo)
    {
        if ( ! $photo->gallery )
        {
            return false;
        }
        return $this->getGalleryOwnerId($photo->gallery);
    }
}