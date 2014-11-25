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
 * @todo добавить возможность множественной загрузки
 * @todo вынести в разные классы функционал загрузки на S3 и функционал специфический для easycast
 * @todo вынести в отдельный класс действие для загрузки видео
 * @todo убрать изначальное значение из поля bucket, 
 *       устанавливать его в контроллере извне при помощи Yii::app()->params['AWSVideoBucket']
 */
class S3XUploadAction extends XUploadAction
{
    /**
     * XUploadForm (or subclass of it) to be used.  Defaults to XUploadForm
     * 
     * @see   XUploadAction::init()
     * @var   string
     * @since 0.5
     */
    public $formClass            = 'xupload.models.S3XUploadForm';
    /**
     * Name of the model attribute referring to the uploaded file.
     * Defaults to 'file', the default value in XUploadForm
     * 
     * @var   string
     * @since 0.5
     */
    public $fileAttribute        = 'file';
    /**
     * Name of the model attribute used to store mimeType information.
     * Defaults to 'mime_type', the default value in XUploadForm
     * 
     * @var   string
     * @since 0.5
     */
    public $mimeTypeAttribute    = 'mime_type';
    /**
     * Name of the model attribute used to store file size.
     * Defaults to 'size', the default value in XUploadForm
     * 
     * @var   string
     * @since 0.5
     */
    public $sizeAttribute        = 'size';
    /**
     * Name of the model attribute used to store the file's display name.
     * Defaults to 'name', the default value in XUploadForm
     * 
     * @var   string
     * @since 0.5
     */
    public $displayNameAttribute = 'name';
    /**
     * Name of the model attribute used to store the file filesystem name.
     * Defaults to 'filename', the default value in XUploadForm
     * 
     * @var   string
     * @since 0.5
     */
    public $fileNameAttribute    = 'filename';
    /**
     * The query string variable name where the subfolder name will be taken from.
     * If false, no subfolder will be used.
     * Defaults to null meaning the subfolder to be used will be the result of date("mdY").
     *
     * @see   XUploadAction::init().
     * @var   string
     * @since 0.2
     */
    public $subfolderVar;
    /**
     * Path of the main uploading folder.
     * 
     * @see   XUploadAction::init()
     * @var   string
     * @since 0.1
     */
    public $path;
    /**
     * Public path of the main uploading folder.
     * 
     * @see   XUploadAction::init()
     * @var   string
     * @since 0.1
     */
    public $publicPath;
    /**
     * @var boolean - dictates whether to use sha1 to hash the file names
     *                along with time and the user id to make it much harder for malicious users
     *                to attempt to delete another user's file
     */
    public $secureFileNames = true;
    /**
     * Name of the state variable the file array is stored in
     * 
     * @see   XUploadAction::init()
     * @var   string
     * @since 0.5
     */
    public $stateVariable   = 'xuploadFiles';
    /**
     * @var string
     */
    public $bucket;
    /**
     * @var string - тип объекта к которому привязывается видео
     */
    public $objectType     = 'questionary';
    /**
     * @var string - url of the default icon for the uploaded fule
     */
    public $defaultIconUrl = '/images/video_placeholder.svg';
    /**
     * @var string - адрес для запроса удаления видео
     */
    //public $deleteUrl;
    
    /**
     * @var string|int
     */
    protected $objectId = '0';
    
    /**
     * The resolved subfolder to upload the file to
     * 
     * @var   string
     * @since 0.2
     */
    //private $_subfolder = "";
    /**
     * The form model we'll be saving our files to
     * 
     * @var   CModel (or subclass)
     * @since 0.5
     */
    private $_formModel;
    
    /**
     * Initialize the propeties of pthis action, if they are not set.
     *
     * @since 0.1
     */
    public function init()
    {
        if ( ! $this->path )
        {
            $this->path = Yii::app()->getRuntimePath()."/uploads";
        }
        if ( ! $this->bucket )
        {
            $this->bucket = Yii::app()->params['AWSVideoBucket'];
        }
        if ( ! is_dir($this->path) )
        {
            mkdir($this->path, 0777, true);
            chmod($this->path , 0777);
            //throw new CHttpException(500, "{$this->path} does not exists.");
        }elseif ( ! is_writable( $this->path ) )
        {
            chmod($this->path, 0777);
            //throw new CHttpException(500, "{$this->path} is not writable.");
        }
        /*if ( $this->subfolderVar !== null AND $this->subfolderVar !== false )
        {
            $this->_subfolder = Yii::app()->request->getQuery($this->subfolderVar, date("mdY"));
        }elseif ( $this->subfolderVar !== false )
        {
            $this->_subfolder = date("mdY");
        }*/
        if( ! $this->_formModel )
        {
            $this->formModel  = Yii::createComponent(array('class' => $this->formClass));
        }
        if ( $this->secureFileNames )
        {
            $this->formModel->secureFileNames = true;
        }
    }
    
    /**
     * Uploads file to temporary directory
     *
     * @throws CHttpException
     */
    protected function handleUploading()
    {
        /* @var $api EcAwsApi */
        $api = Yii::app()->getComponent('ecawsapi');
        // определяем куда прикрепить загруженный файл
        $this->objectType = Yii::app()->request->getParam('objectType', $this->objectType);
        $this->objectId   = Yii::app()->request->getParam('objectId', $this->objectId);
        // определяем путь для загрузки файлов
        $this->init();
        
        // создаем модель формы и сохраняем в нее файл
        /* @var $model S3XUploadForm */
        $model = $this->formModel;
        $model->{$this->fileAttribute} = CUploadedFile::getInstance($model, $this->fileAttribute);
        // название POST-переменной с информацией о файле совпадает с классом формы
        $paramName = get_class($model);
        
        if ( $model->{$this->fileAttribute} !== null )
        {// загрузка файла началась
            // mime-тип определяем на стороне сервера
            $mimeType = CFileHelper::getMimeType($model->{$this->fileAttribute}->getTempName());
            // заполняем модель данными формы
            $model->{$this->mimeTypeAttribute}    = $mimeType;
            $model->{$this->sizeAttribute}        = $model->{$this->fileAttribute}->getSize();
            $model->{$this->displayNameAttribute} = $model->{$this->fileAttribute}->getName();
            $model->{$this->fileNameAttribute}    = $model->{$this->displayNameAttribute};
            
            if ( $model->validate() )
            {// проверки формы прошли - сохраняем информацию о файле и возвращаем результат
                $videoBucket = $api->settings['transcoder']['defaultVideoBucket'];
                $newFileData = array(
                    'bucket'  => $videoBucket,
                    'path'    => $this->objectType.'/'.$this->objectId,
                    'oldname' => $model->{$this->displayNameAttribute},
                );
                // создаем модель файла
                $newFile            = new ExternalFile;
                $newFile->storage   = 's3';
                $newFile->bucket    = $videoBucket;
                // объясняем модели откуда брать данные файла
                $newFile->setInputModel($this->formModel);
                $newFile->setInputName($this->fileAttribute);
                // загружаем файл на S3
                $newFile->prepareSync($newFileData);
                $newFile->saveFile();
                
                if ( $newFile->save() )
                {// сохраняем информацию о видео
                    $video                 = new Video();
                    $video->objecttype     = $this->objectType;
                    $video->objectid       = $this->objectId;
                    $video->externalid     = $newFile->path.'/'.$newFile->name;
                    $video->type           = 'file';
                    $video->size           = $newFile->size;
                    $video->name           = $newFile->oldname;
                    $video->link           = $api->s3->getObjectUrl(Yii::app()->params['AWSVideoBucket'], $newFile->path.'/'.$newFile->name);
                    $video->visible        = 0;
                    $video->externalfileid = $newFile->id;
                    if ( $video->save() )
                    {// сохраняем видео и запускаем оцифровку
                        if ( Yii::app()->user->checkAccess('Admin') )
                        {// автоматически помечаем видео как проверенное если его загрузил администратор
                            $video->setStatus(swVideo::APPROVED);
                            $video->save();
                        }
                        if ( ! $video->transcode() )
                        {// не удалось создать задачу оцифровки
                            echo json_encode(array(array("error" => "Не удалось создать задачу оцифровки видео")));
                            // удаляем загруженный файл
                            // @todo сохранять загруженный файл и предлагать заново запустить задачу оцифровки
                            $newFile->delete();
                            return;
                        }
                    }else
                    {// не удалось сохранить видео
                        $errors = implode(', ', current($video->getErrors()));
                        echo json_encode(array(array("error" => $errors)));
                        Yii::log(get_class($this).": ".$errors, CLogger::LEVEL_ERROR, "xupload.actions.".get_class($this));
                        return;
                    }
                }else
                {// не удалось сохранить модель файла
                    $errors = implode(', ', current($newFile->getErrors()));
                    echo json_encode(array(array("error" => $errors)));
                    Yii::log(get_class($this).": ".$errors, CLogger::LEVEL_ERROR, "xupload.actions.".get_class($this));
                    return;
                }
                
                // отправляем JSON с результатами сохранения
                echo json_encode(array(
                    array(
                        "name" => strip_tags($model->{$this->displayNameAttribute}),
                        "type" => $model->{$this->mimeTypeAttribute},
                        "size" => $model->{$this->sizeAttribute},
                        "url"  => $video->link,
                        // @todo preview-иконка в зависимости от загруженного файла
                        "thumbnail_url" => $this->defaultIconUrl,
                        "delete_url"    => $this->getController()->createUrl($this->getId(), array(
                            "_method" => "delete",
                            "id"      => $video->id,
                        )),
                        "delete_type" => "POST",
                    ),
                ));
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
     * Removes temporary file from its directory and from the session
     *
     * @return bool Whether deleting was meant by request
     */
    protected function handleDeleting()
    {
        $method = Yii::app()->request->getParam('_method');
        $id     = Yii::app()->request->getParam('id', 0);
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {// @todo сделать более продуманную проверку прав при удалении
            return false;
        }
        if ( $method === "delete" AND $video = Video::model()->findByPk($id) )
        {
            $success = $video->delete();
            echo json_encode($success);
            return true;
        }
        return false;
    }
    
    /**
     * We store info in session to make sure we only delete files we intended to
     * Other code can override this though to do other things with state, thumbnail generation, etc.
     * 
     * @since  0.5
     * @author acorncom
     * @return boolean|string Returns a boolean unless there is an error, in which case it returns the error message
     */
    protected function beforeReturn()
    {
        $path      = $this->getPath();
        // Now we need to save our file info to the user's session
        $userFiles = Yii::app()->user->getState($this->stateVariable, array());
        
        $userFiles[$this->formModel->{$this->fileNameAttribute}] = array(
            "path"     => $path.$this->formModel->{$this->fileNameAttribute},
            // @todo взять иконку для предпросмотра файла из анкеты или оригинала видео
            "thumb"    => $this->defaultIconUrl,
            "filename" => $this->formModel->{$this->fileNameAttribute},
            'size'     => $this->formModel->{$this->sizeAttribute},
            'mime'     => $this->formModel->{$this->mimeTypeAttribute},
            'name'     => $this->formModel->{$this->displayNameAttribute},
        );
        Yii::app()->user->setState($this->stateVariable, $userFiles);
    
        return true;
    }
    
    /**
     * Создать постоянный или подписанный токеном url для просмотра видео
     * 
     * @param  int        $objectId
     * @param  string     $newFileName
     * @param  int|string $expires
     * @return string
     */
    protected function createPreSignedUrl($objectId, $newFileName, $expires=null)
    {
        /* @var $s3 Aws\S3\S3Client */
        $s3 = Yii::app()->getComponent('ecawsapi')->getS3();
        // получаем ссылку на скачивание
        return $s3->getObjectUrl($this->bucket, $this->objectType.'/'.$objectId.'/'.$newFileName, $expires);
    }
    
    /**
     * Получить URL для удаления загруженного видео
     * 
     * @param  Video $video
     * @return string
     */
    /*protected function createDeleteUrl($video)
    {
        
    }*/
}