<?php

/**
 * Модель для работы сзагруженными видеофайлами и ссылками на видео
 *
 * Таблица '{{video}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $name
 * @property string $type
 * @property string $description
 * @property string $link
 * @property string $timecreated
 * @property string $timemodified
 * @property string $uploaderid
 * @property string $md5
 * @property string $size
 * @property string $externalid - id видео на внешнем портале
 * @property string $status - статус видеоролика:
 *                         swVideo/pending  - видео загружено участником и ждет проверки
 *                         swVideo/approved - видео проверено администратором и одобрено (или загружено администратором)
 *                         swVideo/rejected - видео отклонено администратором (нельзя такое публиковать)
 * @property int $visible
 * 
 * @todo прописать константы для всех типов видео
 * @todo добавить статус "идет оцифровка" (для загруженных файлов)
 * @todo менять статус модерации для дочерних (перекодированных) видео когда 
 *       исходник видео меняет свой статус
 */
class Video extends SWActiveRecord
{
	/**
	 * Returns the static model of the specified AR class
	 * 
	 * @param string $className active record class name
	 * @return Video the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{video}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
		    array('link, name', 'filter', 'filter' => 'trim'),
		    //array('name', 'required'),
		    
			array('type', 'length', 'max' => 20),
			array('objecttype, status', 'length', 'max' => 50),
			array('timemodified, objectid, timecreated, uploaderid, size, visible', 'length', 'max' => 11),
			array('name, description, link, externalid', 'length', 'max' => 255),
			array('md5', 'length', 'max' => 128),
			
			// The following rule is used by search().
			array('id, objecttype, objectid, name, type, description, link, timecreated, 
			    uploaderid, md5, size, status, visible', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'EcTimestampBehavior' => array(
	            'class' => 'application.behaviors.EcTimestampBehavior',
	        ),
	        // это поведение позволяет изменять набор связей модели в зависимости от того какие данные в ней находятся
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // группы условий поиска для моделей использующих составной ключ objecttype/objectid
	        'CustomRelationSourceBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationSourceBehavior',
	        ),
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationTargetBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
	            'customRelations' => array(),
	        ),
	        // настройки для модели и методы для поиска по этим настройкам
	        'ConfigurableRecordBehavior' => array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
	        ),
	        // подключаем расширение для работы со статусами
	        'swBehavior' => array(
	            'class' => 'ext.simpleWorkflow.SWActiveRecordBehavior',
	        ),
        );
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'name' => Yii::t('coreMessages', 'db_video_name'),
			'type' =>  Yii::t('coreMessages', 'db_video_type'),
			'description' => Yii::t('coreMessages', 'db_video_description'),
			'link' => Yii::t('coreMessages', 'db_video_link'),
			'uploaderid' => 'Uploaderid',
			'md5' => 'Md5',
			'size' => Yii::t('coreMessages', 'db_video_size'),
			'status' => Yii::t('coreMessages', 'db_video_status'),
		    'timecreated' => Yii::t('coreMessages', 'timecreated'),
		    'timemodified' => Yii::t('coreMessages', 'timemodified'), 
		    'visible' => 'Отображение', 
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        if ( Yii::app()->getModule('user')->user() )
	        {// запоминаем того кто загрузил видео
                $this->uploaderid = Yii::app()->user->id;
	        }
	        if ( ! $this->type )
	        {// определяем тип видео
	            $this->type = $this->defineVideoType($this->link);
	        }
	        if ( ! $this->externalid )
	        {// определяем id видео на портале, чтобы потом генерировать правильные ссылки на него
                $this->externalid = $this->extractExternalId();
	        }
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterDelete()
	 */
	public function afterDelete()
	{
	    if ( $this->type === 'file' )
	    {/* @var $s3 Aws\S3\S3Client */
	        $s3 = Yii::app()->getComponent('ecawsapi')->getS3();
	        $s3->deleteObject(array(
	            'Bucket' => Yii::app()->params['AWSVideoBucket'],
	            'Key'    => $this->externalid,
	        ));
	    }
	    parent::afterDelete();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * 
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('type', $this->type, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('link', $this->link, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('timemodified', $this->timemodified, true);
		$criteria->compare('uploaderid', $this->uploaderid, true);
		$criteria->compare('size', $this->size, true);
		$criteria->compare('status', $this->status, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
	
	/**
	 * Условие поиска: все видео с определенным типом
	 * 
	 * @param  string $type
	 * @param  string $operation
	 * @return Video
	 */
	public function withType($type, $operation='AND')
	{
	    if ( ! $type )
	    {// условие не используется
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->owner->getTableAlias(true).'.`type`', $type);
	     
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	     
	    return $this;
	}
	
	/**
	 * Оцифровать видео для воспроизведения на сайте 
	 * Добавляет одну задачу оцифровки видео в очередь сервиса Elastic Transcoder
	 * 
	 * @param  string $preset - id набора настроек для оцифровки видео, если не задан -
	 *                          то используются настройки по умолчанию
	 * @return bool
	 * 
	 * @todo отслеживать статус задачи оцифровки
	 * @todo определять размер файла для модели ExternalFile 
	 *       (когда будем отслеживать процесс перекодировки)
	 * @todo параметр $preset пока что не задействован
	 * @todo проверять результат смены статуса
	 * @todo задавать title для видео в зависимости от модели к которой оно привязано
	 */
	public function transcode($preset=null)
	{
	    if ( $this->type != 'file' )
	    {// перекодировать можно только файлы в нашем хранилище (Amazon S3)
	        $msg = 'Error: wrong video type for encoding: '.$this->type;
	        Yii::log($msg, CLogger::LEVEL_ERROR, 'application.video');
	        return false;
	    }
	    /* @var $api EcAwsApi */
	    $api             = Yii::app()->getComponent('ecawsapi');
	    // параметры для именования файлов
	    $videoBucket     = $api->settings['transcoder']['defaultVideoBucket'];
	    $outputPrefix    = $api->settings['transcoder']['defaultOutputPrefix'];
	    $outputExtension = $api->settings['transcoder']['defaultOutputContainer'];
	    $presetPrefix    = $api->settings['transcoder']['defaultPresetPrefix'];
	    
	    /* @var $file ExternalFile */
	    // получаем оригинал видео
	    $path = pathinfo($this->externalid, PATHINFO_DIRNAME);
	    $file = ExternalFile::model()->inBucket($videoBucket)->withPath($path)->find();
	    if ( ! $file )
	    {// не найден оригинал для перекодирования
	        $msg = 'Error: cannot find original file for encoding. Path:'.$path."\nBucket: ".$videoBucket;
	        Yii::log($msg, CLogger::LEVEL_ERROR, 'application.video');
	        return false;
	    }
	    // ищем обложку для видео
	    if ( $coverSource = $this->getDefaultVideoCoverFile($this->objecttype, $this->objectid) )
	    {// копируем обложку в папку с исходником видео
	        $coverTarget = $file->path.'/'.$file->fileName.'_cover'.'.'.pathinfo($coverSource, PATHINFO_EXTENSION);
	        if ( ! $api->bucketContainsFile($videoBucket, $coverTarget) )
	        {// если ее там еще нет
	            $api->s3CopyFile($coverSource, $videoBucket, $coverTarget);
	        }
	    }
	    // добавляем задачу оцифровки видео
	    $job = $api->addDefaultTranscoderJob($file->path.'/'.$file->name);
	    if ( ! is_array($job) OR ! isset($job['Status']) OR $job['Status'] === $api::ENC_STATUS_ERROR )
	    {// проблема с оцифровкой видео
	        $msg = 'Error: cannot add transcoder job. Details: '.CVarDumper::dumpAsString($job);
	        Yii::log($msg, CLogger::LEVEL_ERROR, 'application.video');
	        return false;
	    }
	    
	    // задача успешно добавлена - создаем в базе модели для будущих файлов
	    // (используем транзакцию чтобы на случай проблем с сохранением моделей)
	    $transaction = $this->getDbConnection()->beginTransaction();
	    
	    // создаем запись для обработанного файла
	    $encodedFile             = new ExternalFile();
	    $encodedFile->storage    = 's3';
	    $encodedFile->bucket     = $videoBucket;
	    $encodedFile->name       = $file->fileName.'_'.$presetPrefix.'.'.$outputExtension;
	    $encodedFile->oldname    = $file->oldname;
	    $encodedFile->title      = $file->title;
	    $encodedFile->path       = $file->path.'/'.$outputPrefix.'/'.$presetPrefix;
	    // запоминаем из какого оригинала было создано это видео
	    $encodedFile->originalid = $file->id;
	    // Amazon сам создаст файл после перекодировки - так что делаем вид что видео уже загружено
	    $encodedFile->lastupload = time();
	    $encodedFile->lastsync   = time();
	    // все перекодированные видео по умолчанию имеют такой mime-тип
	    $encodedFile->mimetype   = 'video/mp4';
	    if ( ! $encodedFile->title )
	    {// название нового файла не должно быть пустым 
	        $encodedFile->title = pathinfo($file->oldname, PATHINFO_FILENAME);
	    }
	    if ( ! $encodedFile->save() )
	    {// ошибка при создании задачи оцифровки
    	    $msg = implode(', ', current($encodedFile->getErrors()));
    	    // откат транзакции
    	    $transaction->rollback();
    	    // ошибку в лог (после транзакции, иначе тоже не сохранится)
    	    Yii::log($msg, CLogger::LEVEL_ERROR, 'application.video');
    	    return false;
	    }
	    // сохраняем модель и сразу помечаем ее как существующий в хранилище файл
	    $encodedFile->setStatus(swExternalFile::ACTIVE);
	    if ( ! $file->fileVersions )
	    {// помечаем исходный файл как оригинал если это еще не было сделано
	        $file->title .= ' (оригинал)';
	        $file->save();
	    }
	     
	    // создаем запись для оцифрованного видео
	    $encodedVideo             = new Video();
	    $encodedVideo->name       = $encodedFile->title.'_480p';
	    $encodedVideo->externalid = $encodedFile->path.'/'.$encodedFile->fileName.'.'.$outputExtension;
	    $encodedVideo->link       = $encodedFile->getUrl();
	    $encodedVideo->objecttype = $this->objecttype;
	    $encodedVideo->objectid   = $this->objectid;
	    $encodedVideo->type       = 'file';
	    $encodedVideo->visible    = $this->visible;
	    if ( ! $encodedVideo->save() )
	    {// ошибка при сохранении перекодированного видео
    	    $msg = implode(', ', current($encodedVideo->getErrors()));
    	    // откат транзакции
    	    $transaction->rollback();
    	    // ошибку в лог (после транзакции, иначе тоже не сохранится)
    	    Yii::log($msg, CLogger::LEVEL_ERROR, 'application.video');
    	    return false;
	    }
	    // перекодированное видео имеет такой же статус модерации как и исходное
	    $encodedVideo->setStatus($this->status);
	    
	    // все модели сохранены успешно: можно завершить транзакцию
	    $transaction->commit();
	    // и сообщить об успешном результате
	    return true;
	}
	
	/**
	 * Определить тип видео по ссылке на него
	 * 
	 * @param  string $link - ссылка на видео
	 * @return string|bool - тип видео или false в случае ошибки
	 */
	public function defineVideoType($link)
	{
	    if ( ! $link OR mb_ereg('video.easycast.ru', $link) )
	    {
	        return 'file';
	    }
	    if ( mb_stristr($link, 'youtube.com') )
	    {
	        return 'youtube';
	    }elseif ( mb_stristr($link, 'vk.com') OR mb_stristr($link, 'vkontakte.ru') )
	    {
	        return 'vkontakte';
	    }elseif ( mb_stristr($link, 'vimeo.com') )
	    {
	        return 'vimeo';
	    }else
        {// ссылка на сторонний ресурс (не можем встроить к себе)
            return 'link';
        }
	}
	
	/**
	 * Получить из ссылки id видео на портале (youtube, vimeo) при сохранении видео
	 * 
	 * @return string
	 * 
	 * @todo извлечь ссылку на vimeo
	 * @todo извлечь ссылку на vk
	 */
	public function extractExternalId()
	{
	    $this->externalid = '';
        switch ( $this->type )
        {
            case 'youtube':
                $this->externalid = $this->getYoutubeId($this->link);
            break;
        }
        return $this->externalid;
	}
	
	/**
	 * Получить ссылку на preview-картинку для видео
	 * 
	 * @return string
	 */
	public function getPreviewUrl($usePlaceholder=true)
	{
	    $defaultUrl = '';
	    if ( $usePlaceholder )
	    {
	        $defaultUrl = Yii::app()->createAbsoluteUrl(Yii::app()->homeUrl.'images/video_placeholder.svg');
	    }
	    switch ( $this->type )
	    {
	        case 'youtube':
                return 'http://img.youtube.com/vi/'.$this->externalid.'/mqdefault.jpg';
            break;
	        // @todo получить превью для наших видео на S3
	        // case 'file': break;
	    }
	    return $defaultUrl;
	}
	
	/**
	 * Получить url для встраивания видео в плеер на странице
	 * (для некоторых сервисов может отличаться от основного url видео)
	 * 
	 * @param  string|int $expires - время до которого действительна ссылка 
	 *                               Формат: метка unixtime или словами (например '+22 minutes')
	 * @return string
	 */
	public function getEmbedUrl($expires='+12 hours')
	{
	    switch ( $this->type )
	    {
	        case 'youtube':
	            return 'http://www.youtube.com/embed/'.$this->externalid.'?rel=0';
            break;
	        case 'file':
	            /* @var $s3 Aws\S3\S3Client */
	            $s3 = Yii::app()->getComponent('ecawsapi')->getS3();
	            // FIXME разобраться с предварительно подписанными URL: они внезапно перестают открываться
	            // return $s3->getObjectUrl(Yii::app()->params['AWSVideoBucket'], $this->externalid, $expires);
	            // return $this->link;
	            return $s3->getObjectUrl(Yii::app()->params['AWSVideoBucket'], $this->externalid);
            break;
	    }
	    return $this->link;
	}
	
	/**
	 * Получить изображение-обложку для видео в зависимости от модели для которой это видео загружено
	 * Используется при оцифровке видео, чтобы сделать красивую обложку для скачанного файла
	 * (например все видио с интервью при просмотре в проводнике будут выглядеть как фото
	 * участников, с которыми оно было записано)
	 * Для заявок или анкет получает аватар участника (в среднем размере)
	 * Для проектов получает лого проекта
	 * Для остальных типов объектов возвращает null
	 * 
	 * @param  CActiveRecord $model
	 * @return string|null - путь к файлу обложки на s3 (bucket/file)
	 * 
	 * @todo возвращать объект ExternalFile когда все изображения будут синхронизированы с таблицей файлов
	 */
	public function getDefaultVideoCoverFile($objectType, $objectId)
	{
	    if ( ! $objectType OR ! $objectId )
	    {// обязательные параметры не указаны
	        return false;
	    }
	    switch ( $objectType )
	    {
	        case 'ProjectMember': 
	            if ( $model = ProjectMember::model()->findByPk($objectId) )
	            {
	                return $this->getQuestionaryCoverS3Path($model->questionary);
	            }
            break;
	        case 'Questionary':
	            if ( $model = Questionary::model()->findByPk($objectId) )
	            {
	                return $this->getQuestionaryCoverS3Path($model);
	            } 
            break;
	    }
	    return false;
	}
	
	/**
	 * Сменить статус объекта
	 * 
	 * @param  string $newStatus
	 * @param  array  $params
	 * @return bool
	 */
	public function setStatus($newStatus, $params=null)
	{
	    return $this->swSetStatus($newStatus, $params);
	}
	
	/**
	 * 
	 * @return array
	 * 
	 * @deprecated использовать системные списки
	 */
	public function getVisibleOptions()
	{
	    return array(
	        '1' => Yii::t('coreMessages', 'yes'),
	        '0' => Yii::t('coreMessages', 'no'),
	    );
	}
	
	/**
	 * 
	 * @return array
	 * 
	 * @deprecated использовать системные списки
	 */
	public function getVisibleOption()
	{
	    $options = $this->getVisibleOptions();
	    return $options[$this->visible]; 
	}
	
	/**
	 * Получить id youtube-видео из ссылки
	 * @see http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id
	 * 
	 * @param  string $url
	 * @return string
	 */
	protected function getYoutubeId($url)
	{
	    $url     = trim($url);
	    $pattern = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
	    preg_match($pattern, $url, $matches);
	    if ( isset($matches[1]) )
	    {
	        return $matches[1];
	    }
	    // регулярные выражения не справились:
        $message = "Не удалось извлечь адрес видео из URL:{$url}\n";
        Yii::log($message, CLogger::LEVEL_ERROR, 'application.video'); 
	    return '';
	}
	
	/**
	 * Получить путь к обложке для оцифрованного видео
	 * 
	 * @return string
	 */
	private function getQuestionaryCoverS3Path($questionary)
	{
	    $cover = null;
	    if ( $gallery = $questionary->getGallery() AND $avatar = $questionary->getGalleryCover() )
	    {
	        $extension = $avatar->getFileExtension();
	        if ( ! in_array($extension, array('jpg', 'png')) )
	        {
	            return;
	        }
	        $cover = 'img.easycast.ru'.'/gallery/'.$gallery->id.'/'.$avatar->id.'medium.'.$extension;
	    }
	    return $cover;
	}
}