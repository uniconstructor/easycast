<?php

/**
 * Компонент для загрузки файлов на AmazonS3 с использованием плагина XUpload
 * Это переписанная версия оригинального плагина: https://github.com/blueimp/jQuery-File-Upload
 * Совершен полный рефакторинг, класс нормально совмещен с Yii
 * 
 */
class EcS3UploadHandler extends CApplicationComponent
{
    /**
     * @var Aws\S3\S3Client
     */
    protected $s3;
    
    /**
     * @see CApplicationComponent::init()
     */
    public function init()
    {
        parent::init();
        // подключаем компонент для работы с хранилищем файлов
        $this->s3 = Yii::app()->getComponent('ecawsapi')->getS3();
    }
}