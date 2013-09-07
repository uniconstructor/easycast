<?php

/**
 * 
 */
class QGalleryController extends GalleryController
{
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
            return false;
        }
        if ( ! $questionary->user )
        {
            return false;
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