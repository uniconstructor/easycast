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
 */
class Video extends SWActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
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
	 * Отправить событие о начале рабочего процесса (workflow)
	 * @param SWEvent $event
	 * @return void
	 */
	public function onEnterWorkflow($event)
	{
	    $this->raiseEvent('onEnterWorkflow', $event);
	}
	
	/**
	 * Обработать событие "начало работы с объектом"
	 * @param SWEvent $event
	 * @return void
	 */
	public function enterWorkflow($event)
	{
	    if ( Yii::app()->getModule('user')->user() )
	    {// запоминаем того кто загрузил видео
	        $this->uploaderid = Yii::app()->getModule('user')->user()->id;
	    }
	    // определяем тип видео
	    $this->type = $this->defineVideoType($this->link);
	    
	    if ( ! $this->externalid )
	    {// определяем id видео на портале, чтобы потом генерировать правильные ссылки на него
	        $this->extractExternalId();
	    }
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
		    array('link, name', 'filter', 'filter' => 'trim'),
		    //array('name', 'required'),
		    //array('videofile', 'file', 'types' => 'avi, mov, flv, mpg, mpeg, mkv, wmv'),
		    
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
	        // подключаем расширение для работы со статусами
	        'swBehavior' => array(
	            'class' => 'ext.simpleWorkflow.SWActiveRecordBehavior',
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
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'name' => Yii::t('coreMessages', 'db_video_name'),
			'type' =>  Yii::t('coreMessages', 'db_video_type'),
			'description' => Yii::t('coreMessages', 'db_video_description'),
			'link' => Yii::t('coreMessages', 'db_video_link'),
			'timecreated' => Yii::t('coreMessages', 'timecreated'),
			'uploaderid' => 'Uploaderid',
			'md5' => 'Md5',
			'size' => Yii::t('coreMessages', 'db_video_size'),
			'status' => Yii::t('coreMessages', 'db_video_status'),
		    'timemodified' => Yii::t('coreMessages', 'timemodified'), 
		    'visible' => 'Отображение', 
		);
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
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('uploaderid',$this->uploaderid,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Именованная группа условий: все видео привзязанные к анкете
	 * @param string $objectType
	 * @param int $objectId
	 * @return Video
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->compare('objectid', $objectId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Определить тип видео по ссылке
	 * @param string $link - ссылка на видео
	 * @return string|bool - тип видео или false в случае ошибки
	 */
	public function defineVideoType($link)
	{
	    if ( ! $link OR mb_ereg('video.easycast.ru', $link) )
	    {
	        return 'file';
	    }
	    if ( mb_ereg('youtube.com', $link) )
	    {
	        return 'youtube';
	    }elseif ( mb_ereg('vk.com', $link) OR mb_ereg('vkontakte.ru', $link) )
	    {
	        return 'vkontakte';
	    }elseif ( mb_ereg('vimeo.com', $link) )
	    {
	        return 'vimeo';
	    }else
        {
            return 'link';
        }
	}
	
	/**
	 * Получить из ссылки id видео на портале (youtube, vimeo) при сохранении видео
	 * @return void
	 */
	public function extractExternalId()
	{
        switch ( $this->type )
        {
            case 'youtube':
                $this->externalid = $this->getYoutubeId($this->link);
            break;
            default: $this->externalid = ''; break;
        }
        return $this->externalid;
	}
	
	/**
	 * Получить id youtube-видео из ссылки
	 * @param string $url
	 * @return string
	 * 
	 * @see http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id
	 */
	protected function getYoutubeId($url)
	{
	    $url = trim($url);
	    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
	    
	    if ( isset($matches[1]) )
	    {
	        return $matches[1];
	    }
	    // не удалось разобрать адрес видео
	    // @todo записать в лог
	    return '';
	}
	
	/**
	 * Получить ссылку на preview-картинку для видео
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
	        default: return $defaultUrl; break;
	    }
	}
	
	/**
	 * Получить url для встраивания видео на странице
	 * (для некоторых сервисов может отличаться от ссылки на видео)
	 * 
	 * @param string|int $expires - время до которого действительна ссылка unixtime или '+22 minutes'
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
	            $s3 = Yii::app()->getComponent('ecawsapi')->getS3();
	            // FIXME разобраться с предварительно подписанными URL: они внезапно перестают открываться
	            // return $s3->getObjectUrl(Yii::app()->params['AWSVideoBucket'], $this->externalid, $expires);
	            // return $this->link;
	            return $s3->getObjectUrl(Yii::app()->params['AWSVideoBucket'], urlencode($this->externalid));
            break;
	        default: return $this->link;
	    }
	}
	
	/**
	 * 
	 * @return array
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
	 */
	public function getVisibleOption()
	{
	    $options = $this->getVisibleOptions();
	    return $options[$this->visible]; 
	}
}