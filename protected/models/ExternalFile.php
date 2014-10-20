<?php

/**
 * Модель для работы с внешними файлами (внешними файлами считаются те которые хранятся
 * на других серверах, в основном на Amazon S3)
 *
 * Таблица '{{external_files}}':
 * @property integer $id
 * @property string $originalid
 * @property string $previousid
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $oldname
 * @property string $newname
 * @property string $storage
 * @property string $timecreated
 * @property string $timemodified
 * @property string $lastupload
 * @property string $lastsync
 * @property string $bucket
 * @property string $path
 * @property string $mimetype
 * @property string $size
 * @property string $md5
 * @property string $updateaction
 * @property string $deleteaction
 * @property string $deleteafter
 * @property string $status
 * 
 * @property string $extension - расширение файла (геттер, read-only)
 * 
 * Relations:
 * @property string $originalFile - оригинал файла из которого был создан этот файл 
 *                                  (только для уменьшеных/перекодированых версий файлов)
 * 
 * @todo документировать все поля
 * @todo прописать связи
 * @todo подключить настройки
 * @todo доработать rules()
 * @todo системная настройка "макс/мин количество попыток для операций с файловым хранилищем"
 */
class ExternalFile extends SWActiveRecord
{
    /**
     * @var unknown
     * @deprecated
     */
    protected $file;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{external_files}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('originalid, previousid, timecreated, timemodified, lastupload, lastsync, deleteafter', 'length', 'max'=>11),
			array('name, title, oldname, newname, path', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('storage, mimetype, updateaction, deleteaction', 'length', 'max' => 10),
			array('bucket, md5', 'length', 'max' => 128),
			array('size', 'length', 'max' => 21),
			array('status', 'length', 'max' => 50),
		    /*array('file', 'file', 'types' => 'jpg,gif,png,jpeg',
		        'allowEmpty' => true, 
		        'on'         => 'insert,update',
            ),*/
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			//array('id, originalid, previousid, name, title, description, oldname, newname, storage, timecreated, timemodified, lastupload, bucket, path, mimetype, size, md5, updateaction, deleteaction, deleteafter, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'originalFile' => array(self::BELONGS_TO, 'ExternalFile', 'originalid'),
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
	        // расширение для работы с workflow-паттернами
	        'swBehavior' => array(
	            'class' => 'ext.simpleWorkflow.SWActiveRecordBehavior',
	        ),
	        // это поведение позволяет изменять набор связей модели в процессе выборки
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // поведение для поиска по связанным моделям
            'CustomRelationTargetBehavior' => array(
                'class' => 'application.behaviors.CustomRelationTargetBehavior',
                'customRelations' => array(),
            ),
	        // настройки для модели и методы для поиска по этим настройкам
	        'ConfigurableRecordBehavior' => array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
	            'defaultOwnerClass' => get_class($this),
	        ),
	    );
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	/*public function beforeSave()
	{
	    return parent::beforeSave();
	}*/
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	/*public function beforeDelete()
	{
	    return parent::beforeDelete();
	}*/
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    $alias = $this->owner->getTableAlias(true);
	    // условие для извлечения файлов которые нужно загрузить либо обновить содержимое
	    $notUploadedCondition = new CDbCriteria();
	    $notUploadedCondition->addCondition("{$alias}.`lastupload` < {$alias}.`lastsync` ");
	    $notUploadedCondition->addCondition("{$alias}.`lastupload` > 0 AND {$alias}.`lastsync` = 0 ", 'OR');
	    
	    return array(
	        // файлы, которые ждут загрузки на сервер
	        'notUploaded' => array(
	            'condition' => $notUploadedCondition->condition,
	        ),
        );
	} 

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'originalid' => 'Оригинал файла',
			'previousid' => 'Предыдущая версия файла',
			'name' => 'Name',
			'title' => 'Title',
			'description' => 'Description',
		    'lastupload' => '',
		    'lastsync' => '',
			'oldname' => 'Oldname',
			'newname' => 'Newname',
			'storage' => 'Storage',
			'bucket' => 'Bucket',
			'path' => 'Path',
			'mimetype' => 'Mimetype',
			'size' => 'Size',
			'md5' => 'Md5',
			'updateaction' => 'Updateaction',
			'deleteaction' => 'Deleteaction',
			'deleteafter' => 'Deleteafter',
			'status' => 'Status',
			//'file' => 'Изображение',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	/*public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('originalid',$this->originalid,true);
		$criteria->compare('previousid',$this->previousid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('oldname',$this->oldname,true);
		$criteria->compare('newname',$this->newname,true);
		$criteria->compare('storage',$this->storage,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('lastupload',$this->lastupload,true);
		$criteria->compare('lastsync',$this->lastsync,true);
		$criteria->compare('bucket',$this->bucket,true);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('mimetype',$this->mimetype,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('md5',$this->md5,true);
		$criteria->compare('updateaction',$this->updateaction,true);
		$criteria->compare('deleteaction',$this->deleteaction,true);
		$criteria->compare('deleteafter',$this->deleteafter,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}*/

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExternalFile the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 
	 * @return Aws\S3\S3Client
	 */
	protected function getS3()
	{
	    return Yii::app()->getComponent('ecawsapi')->getS3();
	}
	
	public function setFile($file)
	{
	    $this->file = $file;
	}
	
	public function getFile()
	{
	    return $this->file;
	}
	
	/**
	 * 
	 * 
	 * @param int $num - количество попыток если операция не удалась
	 * @return void
	 * 
	 * @todo
	 */
	public function setMaxAttempts($num)
	{
	    
	}
	
	/**
	 * 
	 * 
	 * @return int
	 * 
	 * @todo
	 */
	public function getMaxAttempts()
	{
	    return 2;
	}
	
	/**
	 * Подготовиться к загрузке файла во внешнее хранилище
	 * Создает запись в статусе "черновик", заполняет ее данными и возвращает созданную запись 
	 * Эта функция нужна для того чтобы можно было узнать id и привязку файла еще до того
	 * как начнется загрузка файла, и использовать эти данные, например, для имени файла
	 * 
	 * @param  array $attributes - изначальные значения для модели файла, они задаются
	 *                             до начала загрузки
	 * @return ExternalFile
	 */
	public function prepareSync($attributes=array())
	{
	    if ( ! $this->isNewRecord )
	    {
	        return false;
	    }
	    $this->name = ECPurifier::getRandomString(10);
	    if ( $attributes AND is_array($attributes) )
	    {
	        $this->setAttributes($attributes);
	    }
	    return $this->save();
	}
	
	/**
	 * Сохранить загруженный файл, выполнив все проверки (mime, размер, успешна ли загрузка, и т. д.)
	 * Изо всех сил старается загрузить файл во внешнее хранилище, но если это не
	 * удается - то временно сохраняет файл на веб-сервере, чтобы потом 
	 * перенести его при следующей синхронизации
	 * 
	 * Если загрузка во внешнее хранилище прошла успешно - удаляет временный файл,
	 * меняет статус записи, заполняет запись данными о файле и сохраняет ее
	 * 
	 * @param  CUploadedFile $tmpFile - загруженный файл во временной директории
	 * @return bool
	 * 
	 * @todo сразу загружать файл на S3
	 */
	public function saveFile()
	{
	    // загружаем файл и извлекаем метаданные
	    $this->saveLocal();
	    
	    $source = $this->getLocalPath();
	    $target = $this->getExternalPath();
	    
        try
        {// загружаем файл во внешнее хранилище
            $this->saveExternal($source, $target);
            // если сохранение удалось - помечаем файл загруженным и готовым к использованию
            $this->swSetStatus('swExternalFile/uploaded');
            $this->swSetStatus('swExternalFile/active');
            // запоминаем время загрузки файла
            $this->lastsync = time();
            $this->save();
        }catch ( Exception $e )
        {// upload failed - no problem: skip it and try later by cron
            Yii::log('Image not uploaded: '.$target, CLogger::LEVEL_ERROR);
            return false;
        }
        return true;
	}
	
	/**
	 * Загрузить файл во внешнее хранилище с веб-сервера
	 * Определяет местоположение текущего файла по содержимому записи
	 *
	 * Возвращает true если загрузка удалась или не требуется и false в случае ошибки
	 * или неудавшейся синхронизации
	 *
	 * @return bool
	 */
	/*public function syncFile()
	{
        
	}*/
	
	/**
	 * Удалить файл из внешнего хранилища
	 * 
	 * @return bool
	 */
	public function deleteFile()
	{
	    
	}
	
	/**
	 * 
	 * 
	 * @return string
	 */
	/*public function getPath()
	{
	    
	}*/
	
	/**
	 * 
	 * 
	 * @return string
	 */
	public function getUrl()
	{
	    if ( $this->lastsync )
	    {
	        return $this->getExternalUrl();
	    }else
	    {
	        return $this->getLocalUrl();
	    }
	}
	
	/**
	 * 
	 * 
	 * @return string
	 */
	public function getExtension()
	{
	    if ( $ext = pathinfo($this->oldname, PATHINFO_EXTENSION) )
	    {
	        return $ext;
	    }
	    return 'jpg';
	}
	
	/**
	 * 
	 * 
	 * @return bool
	 */
	public function saveLocal()
	{
	    if ( $file = CUploadedFile::getInstanceByName('file') )
	    {
	        // старый файл удалим, потому что загружаем новый
	        $this->deleteFile();
	        // извлекаем метаданные
	        $this->oldname    = $file->getName();
	        $this->lastupload = time();
	        $this->mimetype   = $file->getType();
	        $this->size       = $file->getSize();
	        
	        // создаем локальную директорию если нужно
	        if ( ! is_dir($this->getLocalPathPrefix()) )
	        {
	            mkdir($this->getLocalPathPrefix(), 0777, true);
	        }
	        // сохраняем из временной директории в публичную
	        if ( ! $file->saveAs($this->getLocalPath()) )
	        {
	            return false;
	        }
	        // загрузка прошла успешно - изменим статус
	        $this->swSetStatus('swExternalFile/saved');
	        // сохраняем модель
	        return $this->save();
	    }
	    return true;
	}
	
	/**
	 * 
	 * 
	 * @return bool
	 */
	public function saveExternal($source, $target)
	{
	    if ( ! file_exists($source) )
	    {// no source file
            throw new CException('No source file: upload aborted');
	    }
	    $request = array();
        $request['Bucket']     = $this->bucket;
        $request['ACL']        = 'public-read';
        $request['Key']        = $target;
        $request['SourceFile'] = $source;
        // put image into bucket
        // @todo check image upload result
        $result = $this->getS3()->putObject($request);
        
        return true;
	}
	
	/**
	 * Удалить промежуточную локальную копию файла, которая лежит на веб-сервере
	 * 
	 * @return bool
	 */
	public function deleteLocal()
	{
	     
	}
	
	/**
	 * Удалить файл из внешнего хранилища
	 * 
	 * @return bool
	 */
	public function deleteExternal()
	{
	     
	}
	
	/**
	 * 
	 * 
	 * @return string
	 */
	public function getLocalPath()
	{
        return $this->getLocalPathPrefix().$this->getNewFileName();
	}
	
	/**
	 *
	 *
	 * @return string
	 */
	public function getExternalPath()
	{
	    return $this->path.DIRECTORY_SEPARATOR.$this->getNewFileName();
	}
	
	/**
	 * 
	 * 
	 * @return string
	 */
	public function getLocalUrl()
	{
	    $route = $this->path.DIRECTORY_SEPARATOR.$this->name.'.'.$this->extension;
	    return Yii::app()->createAbsoluteUrl($route);
	}
	
	/**
	 * 
	 * @param  int  $expire - до какого времени действует ссылка (unixtime)
	 * @param  bool $forceDownload - 
	 * @return string
	 */
	public function getExternalUrl($expires=null, $forceDownload=false)
	{
	    return $this->getS3()->getObjectUrl($this->bucket, $this->getExternalPath());
	}
	
	protected function getLocalPathPrefix()
	{
	    return Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.
	       'gallery'.DIRECTORY_SEPARATOR.$this->path.DIRECTORY_SEPARATOR;
	}
	
	protected function getNewFileName()
	{
	    return $this->name.'.'.$this->extension;
	}
	
	protected function getUploadedInstance($name='file')
	{
	    
	}
}