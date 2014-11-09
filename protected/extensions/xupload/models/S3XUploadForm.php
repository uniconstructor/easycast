<?php

Yii::import('xupload.models.XUploadForm');

/**
 * Модель формы загрузки файлов на Amazon S3 через виджет XUpload
 */
class S3XUploadForm extends XUploadForm
{
    /**
     * @var string
     */
    public $file;
    /**
     * @var string
     */
    public $mime_type;
    /**
     * @var string
     */
    public $size;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $filename;
    /**
     * @var boolean - dictates whether to use sha1 to hash the file names
     *                along with time and the user id to make it much harder for malicious users
     *                to attempt to delete another user's file
     */
    public $secureFileNames = true;
    
    /**
     * @see XUploadForm::rules()
     */
    /*public function rules()
    {
        return array(
            array('file', 'file'),
        );
    }*/
    
    /**
     * Declares attribute labels.
     */
    /*public function attributeLabels()
    {
        return array(
            'file' => 'Загрузка файлов',
        );
    }*/
    
    /*public function getReadableFileSize($retstring=null)
    {
        // adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
        $sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $lastsizestring = end($sizes);
        if ( $retstring === null )
        {
            $retstring = '%01.2f %s';
        }
        foreach ( $sizes as $sizestring )
        {
            if ( $this->size < 1024 )
            {
                break;
            }
            if ( $sizestring != $lastsizestring )
            {
                $this->size /= 1024;
            }
        }
        if ( $sizestring == $sizes[0] )
        {// Bytes aren't normally fractional
            $retstring = '%01d %s';
        }
        return sprintf($retstring, $this->size, $sizestring);
    }*/
    
    /**
     * A stub to allow overrides of thumbnails returned
     * 
     * @since 0.5
     * @author acorncom
     * @return string thumbnail name (if blank, thumbnail won't display)
     */
    public function getThumbnailUrl($publicPath)
    {
        return $publicPath.$this->filename;
    }
    
    /**
     * Change our filename to match our own naming convention
     * 
     * @return bool
     */
    public function beforeValidate()
    {
        return parent::beforeValidate();
    }
}