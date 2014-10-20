<?php

Yii::import('xupload.actions.XUploadAction');

/**
 * This action connects original Yii xupload plugin with Amazon Web Services (AWS)
 * All files uploaded by this action will be stored in Amazon S3 Bucket
 * 
 * @see http://www.yiiframework.com/extension/xupload (original Yii extension) 
 * @see https://gist.github.com/tim-peterson/8172999 (original S3 uploader class)
 * @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-s3.html#amazon-s3-stream-wrapper (AWS Docs)
 * 
 * @todo вынести всю логику в beforeReturn, вместо handleUploading
 * @todo добавить возможность множественной загрузки
 * @todo вынести в разные классы функционал загрузки на S3 и функционал специфический для easycast
 * @todo добавить проверку MIME-типа
 * @todo убрать изначальное значение из поля bucket, 
 *       устанавливать его в контроллере извне при помощи Yii::app()->params['AWSVideoBucket']
 */
class S3UploadAction extends XUploadAction
{
    /**
     * @var string path to the original S3 upload handler
     */
    public $s3HandlerClass = 'application.components.aws.S3UploadHandler';
    /**
     * @var string
     */
    public $bucket     = 'video.easycast.ru';
    /**
     * @var string - тип объекта к которому привязывается видео
     */
    public $objectType = 'questionary';
    
    /**
     * Uploads file to temporary directory
     *
     * @throws CHttpException
     */
    protected function handleUploading()
    {
        $this->init();
        $objectId = Yii::app()->request->getParam('objectId', '0');
        
        if ( $objectType = Yii::app()->request->getParam('objectType') )
        {
            $this->objectType = $objectType;
        }
        $model = $this->formModel;
        $model->{$this->fileAttribute} = CUploadedFile::getInstance($model, $this->fileAttribute);
        
        if ( $model->{$this->fileAttribute} !== null )
        {
            $model->{$this->mimeTypeAttribute}    = $model->{$this->fileAttribute}->getType();
            $model->{$this->sizeAttribute}        = $model->{$this->fileAttribute}->getSize();
            $model->{$this->displayNameAttribute} = $model->{$this->fileAttribute}->getName();
            $model->{$this->fileNameAttribute}    = $model->{$this->displayNameAttribute};
    
            if ( $model->validate() )
            {
                Yii::import($this->s3HandlerClass);
                $options = array(
                    'param_name' => 'XUploadForm',
                    'prefix'     => $this->objectType.'/'.$objectId.'/',
                    'bucket'     => $this->bucket,
                );
                $handler = new S3UploadHandler($options, false);
                
                $path        = $this->getPath();
                $returnValue = $this->beforeReturn();
                if ($returnValue === true)
                {
                    $uploadResult = $handler->post(false);
                    $video  = $this->saveVideoData($model, $objectId, $uploadResult);
                    
                    echo json_encode(array(array(
                        "name" => strip_tags($model->{$this->displayNameAttribute}),
                        "type" => $model->{$this->mimeTypeAttribute},
                        "size" => $model->{$this->sizeAttribute},
                        "url"  => $this->createPreSignedUrl($objectId, $uploadResult['XUploadForm'][0]->name, '+24 hours'),
                        "thumbnail_url" => $model->getThumbnailUrl($this->getPublicPath()),
                        "delete_url" => '#',/*$this->getController()->createUrl($this->getId(), array(
                            "_method" => "delete",
                            "file"    => $model->{$this->fileNameAttribute},
                        )),*/
                        "delete_type" => "POST"
                    )));
                }else
                {
                    echo json_encode(array(array("error" => $returnValue,)));
                    Yii::log("S3UploadAction: " . $returnValue, CLogger::LEVEL_ERROR, "xupload.actions.S3UploadAction");
                }
            }else
            {
                $this->afterValidateError($model);
            }
        }else
        {
            throw new CHttpException(500, "Could not upload file");
        }
    }
    
    /**
     * Сохранить запись видео в БД если файл был успешно загружен
     * @param XUploadForm $xUploadForm
     * @param int $objectId
     * @param array $uploadResult
     * 
     * @return Video
     */
    protected function saveVideoData($xUploadForm, $objectId, $uploadResult)
    {
        $newFileName = $uploadResult['XUploadForm'][0]->name;
        $externalId  = $this->objectType.'/'.$objectId.'/'.$newFileName;
        
        $video             = new Video;
        $video->objecttype = $this->objectType;
        $video->objectid   = $objectId;
        $video->externalid = $externalId;
        $video->type       = 'file';
        $video->size       = $uploadResult['XUploadForm'][0]->size;
        $video->name       = $xUploadForm->{$this->displayNameAttribute};
        $video->link       = $this->createPreSignedUrl($objectId, $newFileName);
        $video->visible    = 0;
        $video->save();
        
        return $video;
    }
    
    /**
     * Создать постоянный или временный url для просмотра видео
     * 
     * @param int $objectId
     * @param string $newFileName
     * @param int|string $expires
     * @return string
     */
    protected function createPreSignedUrl($objectId, $newFileName, $expires=null)
    {
        /* @var $s3 Aws\S3\S3Client */
        $s3 = Yii::app()->getComponent('ecawsapi')->getS3();
        // получаем ссылку на скачивание
        return $s3->getObjectUrl($this->bucket, $this->objectType.'/'.$objectId.'/'.$newFileName, $expires);
    }
}