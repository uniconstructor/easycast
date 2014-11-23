<?php

/**
 * Модель для работы с внешними файлами (внешними файлами считаются те которые хранятся
 * на других серверах, в основном на Amazon S3)
 * Будьте внимательны при сохранении файлов на Amazon S3: файлы, которые содержат в имени пробелы
 * или национальный алфавит будут иметь проблемы со скачиванием и генерацией ссылок на них.
 * Используйте только латинские буквы, без пробелов.
 * 
 * Файлы, созданные сервисами Amazon автоматически или те файлы у которых возникли проблемы при
 * загрузке в облако из веб-сервера можно отличить от остальных по статусу active, ненулевому
 * значению lastsync и нулевому значению lastupload: это означает что такие файлы ни разу
 * не загружались на веб-сервер, но в облаке присутствуют: для автоматически созданных
 * файлов это нормальная ситуация.
 *
 * Таблица '{{external_files}}':
 * @property integer $id
 * @property string  $originalid  - id оригинала файла
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
 * @property string  $extension    - (геттер, read-only) расширение файла
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
 * @todo проверка уникальности пары path+name при вставке новой записи в s3
 * @todo миграция, которая находит все файлы с пустым mimetype и определяет его по метаданным файла с S3
 * @todo cron: искать все файлы с lastupload = 0 и lastsync > 0 и проверять существуют ли они
 *       на самом деле в s3
 * @todo при удалении файла только менять статус записи на deleted, добавить в defaultScope 
 *       условие которое отсекает все записи со статусом deleted
 */
class ExternalFile extends SWActiveRecord
{
    /**
     * @var int - длина случайного имени для нового файла по умолчанию
     */
    const NEW_NAME_LENGTH = 10;
    
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
			array('originalid, timecreated, timemodified, lastupload, lastsync, deleteafter', 'length', 'max'=>11),
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
	    // удаляем файл при удалении модели
	    if ( ! $this->deleteFile() )
	    {// ошибка при удалении файла
	        return false;
	    }
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
			'name' => 'Name',
			'title' => 'Title',
			'description' => 'Description',
		    'lastupload' => 'lastupload',
		    'lastsync' => 'lastsync',
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
	 * Получить случайное имя для нового файла
	 *
	 * @param  int    $length  - длина имени
	 * @param  string $append  - строка добавляемая в конец имени
	 * @param  string $prepend - строка добавляемая в начало имени
	 * @return string
	 */
	public static function getRandomFileName($length=self::NEW_NAME_LENGTH, $append='', $prepend='')
	{
	    return $prepend.ECPurifier::getRandomString($length).$append;
	}
	
	/**
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'scopes' => array(
    	        'inStorage' => array('s3'),
    	    ),
	    );
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
	 * Условие поиска: только не загруженные файлы
	 * 
	 * @param  int $limit
	 * @return ExternalFile
	 */
	public function notUploaded($limit=3)
	{
	    $alias    = $this->owner->getTableAlias(true);
	    $criteria = new CDbCriteria();
	    // условие для извлечения файлов которые нужно загрузить либо обновить содержимое
	    $criteria->addCondition("{$alias}.`lastupload` < {$alias}.`lastsync` ");
	    $criteria->addCondition("{$alias}.`lastupload` > 0 AND {$alias}.`lastsync` = 0 ", 'OR');
	    if ( (int)$limit )
	    {
	        $criteria->limit = $limit;
	    }
	    $this->getDbCriteria()->mergeWith($criteria);
	    // ищем только файлы в тех статусах, которые присваиваются до загрузки в облако
	    return $this->withStatus(array(swExternalFile::DRAFT, swExternalFile::UPLOADED, swExternalFile::SAVED));
	}
	
	/**
	 * Условие поиска: найти файлы по относительному пути внутри контейнера
	 * 
	 * @param  string $path
	 * @param  string $operator
	 * @return ExternalFile
	 */
	public function withPath($path, $operator='AND')
	{
	    if ( ! $path )
	    {// условие не используется
            return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->owner->getTableAlias(true).'.`path`', $path);
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	    
	    return $this;
	}
	
	/**
	 * Условие поиска: файлы внутри указанного контейнера 
	 * 
	 * @param  string $bucket
	 * @param  string $operator
	 * @return ExternalFile
	 */
	public function inBucket($bucket, $operator='AND')
	{
	    if ( ! $bucket )
	    {// условие не используется
            return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->owner->getTableAlias(true).'.`bucket`', $bucket);
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	    
	    return $this;
	}
	
	/**
	 * Условие поиска: файлы с указанным типом хранилища 
	 * 
	 * @param  string $storage
	 * @param  string $operator
	 * @return ExternalFile
	 */
	public function inStorage($storage, $operator='AND')
	{
	    if ( ! $storage )
	    {// условие не используется
            return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->owner->getTableAlias(true).'.`storage`', $storage);
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	    
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
	    return;
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
	    if ( $attributes AND is_array($attributes) )
	    {
	        $this->setAttributes($attributes);
	    }
	    if ( ! $this->name )
	    {// у амазона проблема с национальными алфавитами в именах файлов, так что по умолчанию
	        // стремимся сохранять все файлы на S3 под случайно созданными именами
	        $this->name = self::getRandomFileName();
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
        }catch ( Exception $e )
        {// ошибка при загрузке файла - запишем ее в лог
            $msg = 'File not uploaded: '.$target.'. Message: '.$e->getMessage().
                ' ('.$e->getFile().':'.$e->getLine().")\n";
            Yii::log($msg, CLogger::LEVEL_ERROR);
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
	 * 
	 * 
	 * @return string
	 */
	public function getBaseName()
	{
	    if ( $baseName = pathinfo($this->name, PATHINFO_BASENAME) )
	    {
	        return $baseName;
	    }
	    return '';
	}
	
	/**
	 * 
	 * 
	 * @return string
	 */
	public function getFileName()
	{
	    if ( $fileName = pathinfo($this->name, PATHINFO_FILENAME) )
	    {
	        return $fileName;
	    }
	    return '';
	}
	
	/**
	 * Получить расширение сохраненного файла
	 * 
	 * @return string
	 * 
	 * @todo пробовать определить расширение по MIME-типу если не удалось получить его из имени файла
	 */
	public function getExtension()
	{
	    if ( $ext = pathinfo($this->oldname, PATHINFO_EXTENSION) )
	    {
	        return mb_strtolower($ext);
	    }
	    if ( $ext = pathinfo($this->name, PATHINFO_EXTENSION) )
	    {
	        return mb_strtolower($ext);
	    }
	    return '';
	}
	
	/**
	 * Получить расширение указанное при загрузке файла
	 * 
	 * @return string
	 * @return string
	 */
	public function getOriginalExtension()
	{
	    if ( $ext = pathinfo($this->oldname, PATHINFO_EXTENSION) )
	    {
	        return mb_strtolower($ext);
	    }
	    return '';
	}
	
	/**
	 * Сохранить файл загруженный на веб-сервер локально
	 * 
	 * @param  string $inputName - название поля в которое загружается файл
	 * @return bool
	 * 
	 * @todo удалять старый файл только если новый уже загружен
	 */
	public function saveLocal()
	{
	    if ( ! $this->_inputModel AND ! $this->_inputName )
	    {// загрузка файла на веб-сервер не планируется, файл можно пометить как загруженный
	        return true;
	    }
	    if ( $this->_inputModel )
	    {
	        $file = CUploadedFile::getInstance($this->_inputModel, $this->_inputName);
	    }else
	    {
	        $file = CUploadedFile::getInstanceByName($this->_inputName);
	    }
	    if ( ! $file )
	    {// загруженный файл не найден на веб-сервере
	        return false;
	    }
	    // если есть старый файл - удалим его, потому что загружаем новый
	    $this->deleteLocal();
	    // извлекаем метаданные
	    $this->oldname    = $file->getName();
	    $this->lastupload = time();
	    $this->mimetype   = $file->getType();
	    $this->size       = $file->getSize();
	    $this->name       = ECPurifier::getRandomString(self::NEW_NAME_LENGTH).'.'.$this->getExtension();
	    if ( ! is_dir($this->getLocalPathPrefix()) )
	    {// создаем локальную директорию если нужно
	        // @todo рассмотреть вариант с 644
            mkdir($this->getLocalPathPrefix(), 0777, true);
	    }
	    if ( ! $file->saveAs($this->getLocalPath()) )
	    {// сохраняем из временной директории в публичную
	       return false;
	    }
	    // загрузка прошла успешно - изменим статус
	    $this->setStatus(swExternalFile::SAVED);
	    // сохраняем модель
	    return $this->save();
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
	    if ( $this->externalCopyExists() )
	    {// файл уже загружен во внешнее хранилище - просто помечаем его загруженным
	        $this->markActive();
            return true;
	    }
	    if ( ! $this->localCopyExists() )
	    {// исходный файл не найден
            $this->markActive();
            // @todo $this->delete();
            throw new CException('No source file: upload aborted');
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
            {// @todo проверить результат загрузки файла
                $result = $this->getS3()->putObject($request);
                // если сохранение удалось - помечаем файл загруженным и готовым к использованию
                $this->swSetStatus(swExternalFile::ACTIVE);
                // запоминаем время загрузки файла
                $this->lastsync = time();
                $this->save();
                // удаляем локальную копию файла - она больше не нужна
                $this->deleteLocal();
                return true;
            }catch ( Exception $e )
            {
                $msg = "File not uploaded: ".$e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n".$e->getTraceAsString()."\n";
                Yii::log($msg, CLogger::LEVEL_ERROR);
            }
        }
        return false;
	}
	
	/**
	 * Удалить промежуточную локальную копию файла, которая лежит на веб-сервере
	 * 
	 * @return bool
	 */
	public function deleteLocal()
	{
	    if ( ! $this->localCopyExists() )
	    {// файл уже удален
	        return true;
	    }
	    // удаляем файл с веб сервера
	    if ( ! @unlink($this->getLocalPath()) )
	    {
	        return false;
	    }
	    return true;
	    // @todo удаляем временную директорию, созданную для загруженного файла если она пуста
	    //$dir  = $this->getLocalPathPrefix();
	    //if ( ! file_exists($dir) )
	    //{
	    //    return true;
	    //}
	    //return @rmdir($dir);
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
                $msg = 'File not deleted: '.$this->getExternalPath().". Exception: ".
                    $e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n".
                    $e->getTraceAsString()."\n";
                $e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n";
                Yii::log($msg, CLogger::LEVEL_ERROR, 'AWS');
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
        return $this->getLocalPathPrefix().DIRECTORY_SEPARATOR.$this->name;
	}
	
	/**
	 * Получить системный путь к загруженному файлу во внешнем хранилище
	 *
	 * @return string
	 */
	public function getExternalPath()
	{
	    return $this->path.DIRECTORY_SEPARATOR.$this->name;
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
	    return Yii::app()->getComponent('ecawsapi')->bucketContainsFile($this->bucket, $this->getExternalPath());
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
	 * Сменить статус (alias для метода simpleWorkflow)
	 * @see SWActiveRecordBehavior::swSetStatus
	 * 
	 * @param  string $newStatus
	 * @param  array  $params
	 * @return bool
	 * @throws SWException
	 */
	public function setStatus($newStatus, $params=null)
	{
	    return $this->swSetStatus($newStatus, $params);
	}
	
	/**
	 * Пометить файл успешно загруженным во внешнее хранилище и обработанным
	 * 
	 * @param  bool $saveNow - сохранить модель сразу же
	 * @return bool
	 */
	public function markActive($saveNow=true)
	{
	    $this->swSetStatus(swExternalFile::ACTIVE);
	    // устанавливаем время загрузки файла
	    $this->lastsync = time();
	    if ( $saveNow )
	    {// сразу же сохраняем данные модели в базу
	        return $this->save();
	    }
	    return true;
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
	       'gallery'.DIRECTORY_SEPARATOR.$this->path;
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