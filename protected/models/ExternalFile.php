<?php

/**
 * Модель для работы с внешними файлами (внешними файлами считаются те которые хранятся
 * на других серверах, в основном на Amazon S3)
 *
 * Таблица '{{external_files}}':
 * @property integer $id
 * @property string  $originalid  - id оригинала файла
 * @property string  $previousid  - @todo удалить при рефакторинге, не понадобилось
 * @property string  $name        - имя файла при скачивании пользователем (если оно создано заранее)
 * @property string  $title       - отображаемое пользователю название файла 
 *                                  (например "Видео с танцевального конкурса")
 * @property string  $description - описание файла
 * @property string  $oldname - имя файла на компьютере пользователя, при загрукзке
 * @property string  $newname - безопасное имя файла на сервере, подходящая для любых операций 
 *                              (случайно сгенерированная строка 10 симвлолов) 
 * @property string  $storage - тип внешнего хранилища (как правило Amazon S3 но в будущем 
 *                              рассматривается потенциальная возможность расширитьо набор 
 *                              используемых внешних хранилищ)
 * @property string  $timecreated
 * @property string  $timemodified
 * @property string  $lastupload   - время последней загрузки файла на веб-сервер
 * @property string  $lastsync     - время последней загрузки файла во внешнее хранилище
 * @property string  $bucket       - Amazon S3 bucket
 * @property string  $path         - относительный путь во внешнем файловом хранилище
 * @property string  $mimetype     - mime-тип файла проверенный сервером
 * @property string  $size         - размер файла в байтах
 * @property string  $md5          - контрольная сумма файла (для поиска копий)
 * @property string  $updateaction - действие при загрузке новой версии файла 
 *                                   (удалить или сохранить старую версию?)
 * @property string  $deleteaction - действие при удалении записи 
 *                                   (удалить или сохранить старую версию?)
 * @property string  $deleteafter  - удалить после определенного времени (для временных файлов)
 * @property string  $status       - статус файла (см. workflow-класс swExtternalFile)
 * Геттеры:
 * @property string  $extension - (геттер, read-only) расширение файла
 * 
 * Relations:
 * @property ExternalFile   $originalFile - оригинал файла из которого был создан этот файл 
 *                                          (только для уменьшеных/перекодированых версий файлов)
 * @property ExternalFile[] $fileVersions - версии созданные из этого файла
 *                                          (только для уменьшеных/перекодированых версий файлов)
 * 
 * @todo доработать rules()
 * @todo системная настройка "макс/мин количество попыток для операций с файловым хранилищем"
 * @todo настройка "стандартный набор версий для файла"
 */
class ExternalFile extends SWActiveRecord
{
    /**
     * @var string - название input-поля в форме, которое содержит файл
     */
    private $_inputName = 'file';
    /**
     * @var string - название модели формы, которая содержит файл (если форма файла является моделью)
     */
    private $_inputModel;
    
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
			array('name, title, oldname, newname, mimetype, path', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('storage, updateaction, deleteaction', 'length', 'max' => 10),
			array('bucket, md5', 'length', 'max' => 128),
			array('size', 'length', 'max' => 21),
			array('status', 'length', 'max' => 50),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // оригинал файла из которого был создан этот файл 
		    // (только для уменьшеных/перекодированых версий файлов)
		    'originalFile' => array(self::BELONGS_TO, 'ExternalFile', 'originalid'),
		    // версии созданные из этого файла
		    // (только для уменьшеных/перекодированых версий файлов)
		    'fileVersions' => array(self::HAS_MANY, 'ExternalFile', 'originalid'),
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
	 * @see CActiveRecord::beforeDelete()
	 * 
	 * @todo удаление связанных файлов
	 */
	public function beforeDelete()
	{
	    // удаляем все файлы после удаления 
	    $this->deleteFile();
	    return parent::beforeDelete();
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
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return ExternalFile the static model class
	 */
	public static function model($className=__CLASS__)
	{
	    return parent::model($className);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 *
	 * @todo добавить условие по статусу для файлов которые нужно загрузить
	 */
	public function scopes()
	{
	    $alias = $this->owner->getTableAlias(true);
	    // условия поиска по датам создания и изменения
	    $timestampScopes = $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	     
	    // собственные условия поиска модели
	    $modelScopes = array(
	        
	    );
	    return CMap::mergeArray($timestampScopes, $modelScopes);
	}
	
	/**
	 * 
	 * @param  int $limit
	 * @return ExternalFile
	 */
	public function notUploaded($limit=3)
	{
	    $alias = $this->owner->getTableAlias(true);
	    $criteria = new CDbCriteria();
	    // условие для извлечения файлов которые нужно загрузить либо обновить содержимое
	    $criteria->addCondition("{$alias}.`lastupload` < {$alias}.`lastsync` ");
	    $criteria->addCondition("{$alias}.`lastupload` > 0 AND {$alias}.`lastsync` = 0 ", 'OR');
	    if ( (int)$limit )
	    {
	        $criteria->limit = $limit;
	    }
	    
	    $this->getDbCriteria()->mergeWith($criteria, 'AND');
	    
	    return $this;
	}

	/**
	 * Установить максимальное количество попыток загрузки файла на S3
	 * 
	 * @param  int $num - количество попыток если операция не удалась
	 * @return void
	 * 
	 * @todo 
	 */
	public function setMaxAttempts($num)
	{
	    
	}
	
	/**
	 * Получить максимальное количество попыток загрузки файла
	 * 
	 * @return int
	 * 
	 * @todo
	 */
	public function getMaxAttempts()
	{
	    return 3;
	}
	
	/**
	 * 
	 * @param  string $name
	 * @return void
	 */
	public function setInputName($name)
	{
	    $this->_inputName = $name;
	}
	
	/**
	 * 
	 * @param  string $model
	 * @return void
	 */
	public function setInputModel($model)
	{
	    $this->_inputModel = $model;
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
	    // сохраняем все файлы на сервер под случайно созданными именами
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
	 * @return bool
	 * 
	 * @todo сразу загружать файл на S3 и сохранять его локально только если не удалась загрузка
	 */
	public function saveFile()
	{
	    // сначала сохраняем файл на веб-сервере, проверяем его и извлекаем метаданные
	    $this->saveLocal();
	    // определяем путь для загрузки файлов во внешнее хранилище
	    // (в нашем случае это чаще всего Amazon S3)
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
            // удаляем локальную копию - она больше не нужна
            $this->deleteLocal();
        }catch ( Exception $e )
        {// upload failed - no problem: skip it and try later by cron
            Yii::log('File not uploaded: '.$target.'. Message: '.$e->getMessage(), CLogger::LEVEL_ERROR);
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
	 * Удалить файл (внешнюю и внутреннюю копию)
	 * 
	 * @return bool
	 */
	public function deleteFile()
	{
	    return ($this->deleteExternal() AND $this->deleteLocal());
	}
	
	/**
	 * Получить внутренний путь к файлу
	 * 
	 * @return string
	 */
	public function getPath()
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
	 * Получить url для просмотра или загрузки файла
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
	 * Получить расширение файла
	 * 
	 * @return string
	 * 
	 * @todo пробовать определеть расширение по MIME-типу если не удалось получить его из имени файла
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
	 * Сохранить файл локально
	 * 
	 * @param  string $inputName - название поля в которое загружается файл
	 * @return bool
	 * 
	 * @todo удалять старый файл только если новый уже загружен
	 */
	public function saveLocal()
	{
	    if ( $this->_inputModel )
	    {
	        $file = CUploadedFile::getInstance($this->_inputModel, $this->_inputName);
	    }else
	    {
	        $file = CUploadedFile::getInstanceByName($this->_inputName);
	    }
	    //print_r($_FILES);die;
	    //print_r($file);die;
	    if ( $file )
	    {
	        // старый файл удалим, потому что загружаем новый
	        $this->deleteLocal();
	        // извлекаем метаданные
	        $this->oldname    = $file->getName();
	        $this->lastupload = time();
	        $this->mimetype   = $file->getType();
	        $this->size       = $file->getSize();
	        $this->name       = ECPurifier::getRandomString(10).$this->getExtension();
	        
	        if ( ! is_dir($this->getLocalPathPrefix()) )
	        {// создаем локальную директорию если нужно
	            mkdir($this->getLocalPathPrefix(), 0777, true);
	        }
	        if ( ! $file->saveAs($this->getLocalPath()) )
	        {// сохраняем из временной директории в публичную
	            return false;
	        }
	        // загрузка прошла успешно - изменим статус
	        $this->swSetStatus('swExternalFile/saved');
	        // сохраняем модель
	        return $this->save();
	    }else
	    {
	        return false;
	    }
	}
	
	/**
	 * Загрузить файл во внешнее хранилище
	 * 
	 * @return bool
	 * 
	 * @todo удалять из внешнего хранилища старый файл при успешной загрузке нового
	 */
	public function saveExternal($source=null, $target=null)
	{
	    if ( $source === null )
	    {
	        $source = $this->getLocalPath();
	    }
	    if ( $target === null )
	    {
	        $target = $this->getExternalPath();
	    }
	    if ( ! file_exists($source) )
	    {// исходный файл не найден
	        if ( $this->externalCopyExists() )
	        {// файл уже загружен во внешнее хранилище - дальнейших действий не требуется
	            return true;
	        }else
	        {
	            // @todo $this->delete();
	            throw new CException('No source file: upload aborted');
	        }
	    }
	    // параметры для запроса загрузки файла на сервер
	    $request = array();
        $request['Bucket']     = $this->bucket;
        $request['ACL']        = 'public-read';
        $request['Key']        = $target;
        $request['SourceFile'] = $source;
        
        for ( $i = 0; $i < $this->getMaxAttempts(); $i++ )
        {// несколько попыток загрузки файла
            try
            {// @todo check upload result
                $result = $this->getS3()->putObject($request);
                return true;
            }catch ( Exception $e )
            {
                Yii::log('File not uploaded: '.$target.'. Message: '.$e->getMessage(), CLogger::LEVEL_ERROR);
            }
        }
        return false;
	}
	
	/**
	 * Удалить промежуточную локальную копию файла, которая лежит на веб-сервере
	 * 
	 * @return bool
	 * 
	 * @todo возможность указать удаляемый файл через параметр
	 */
	public function deleteLocal()
	{
	    if ( ! $this->localCopyExists() )
	    {// файл уже удален
	        return true;
	    }
	    // удаляем существующий файл если он есть
	    return @unlink($this->getLocalPath());
	}
	
	/**
	 * Удалить файл из внешнего хранилища
	 * 
	 * @return bool
	 * 
	 * @todo возможность указать удаляемый файл через параметр
	 */
	public function deleteExternal()
	{
	    if ( ! $this->externalCopyExists() )
	    {// загруженной копии нет - действия не требуются
	        return true;
	    }
	    for ( $i = 0; $i < $this->getMaxAttempts(); $i++ )
	    {
            try
            {
                $this->getS3()->deleteObject(array(
                    'Bucket' => $this->bucket,
                    'Key'    => $this->getExternalPath(),
                ));
                return true;
            }catch ( Exception $e )
            {
                Yii::log('File not deleted: '.$this->getExternalPath().'. Message: '.$e->getMessage(), CLogger::LEVEL_ERROR);
            }
	    }
	    return false;
	}
	
	/**
	 * Получить системный путь к загруженному файлу на веб-сервере
	 * 
	 * @return string
	 */
	public function getLocalPath()
	{
        return $this->getLocalPathPrefix().$this->getNewFileName();
	}
	
	/**
	 * Получить системный путь к загруженному файлу во внешнем хранилище
	 *
	 * @return string
	 */
	public function getExternalPath()
	{
	    return $this->path.DIRECTORY_SEPARATOR.$this->getNewFileName();
	}
	
	/**
	 * Проверить, существует ли загруженный файл на веб-сервере
	 * 
	 * @return bool
	 */
	public function localCopyExists()
	{
	    $localFile = $this->getLocalPath();
	    if ( file_exists($localFile) AND is_file($localFile) )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Проверить, существует ли файл во внешнем хранилище
	 *
	 * @return bool
	 */
	public function externalCopyExists()
	{
	    for ( $i = 0; $i < $this->getMaxAttempts(); $i++ )
	    {
	        try
	        {// все запросы к Amazon API оборачиваем в try-catch
	            return $this->getS3()->doesObjectExist($this->bucket, $this->getExternalPath());
	        }catch ( Exception $e )
    	    {
    	        Yii::log('Request failed. Message: '.$e->getMessage(), CLogger::LEVEL_ERROR);
    	    }
	    }
	    return false;
	}
	
	/**
	 * Получить URL для загрузки или просмотра файла который пока еще находится на веб-сервере
	 * и еще не загружен во внешнее хранилище
	 * 
	 * @return string
	 */
	public function getLocalUrl()
	{
	    $route = $this->path.DIRECTORY_SEPARATOR.$this->name.'.'.$this->extension;
	    return Yii::app()->createAbsoluteUrl($route);
	}
	
	/**
	 * Получить URL для загрузки или просмотра файла во внешнем хранилище
	 * 
	 * @param  int  $expires - до какого времени действует ссылка (unixtime)
	 * @param  bool $forceDownload - 
	 * @return string
	 */
	public function getExternalUrl($expires=null, $forceDownload=false)
	{
	    return $this->getS3()->getObjectUrl($this->bucket, $this->getExternalPath(), $expires);
	}
	
	/**
	 * Определить, загружен ли файл 
	 * 
	 * @return bool
	 */
	public function getIsUploaded()
	{
	    if ( $this->isNewRecord )
	    {// не сохраненные AR-записи не могут быть загруженными файлами
	        return false;
	    }
	    return $this->notUploaded(1)->exists('id='.$this->id);
	}
	
	/**
	 * @return Aws\S3\S3Client
	 */
	protected function getS3()
	{
	    return Yii::app()->getComponent('ecawsapi')->getS3();
	}
	
	/**
	 * Получить директорию по умолчанию, куда будут загружаться файлы
	 * 
	 * @return string
	 */
	protected function getLocalPathPrefix()
	{
	    return Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.
	       'gallery'.DIRECTORY_SEPARATOR.$this->path.DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Получить новое имя файла (случайная строка + расширение)
	 * 
	 * @return string
	 */
	protected function getNewFileName()
	{
	    return $this->name.'.'.$this->extension;
	}
}