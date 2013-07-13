<?php

/**
 * This is the model class for table "{{video}}".
 *
 * The followings are the available columns in table '{{video}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $name
 * @property string $type
 * @property string $description
 * @property string $link
 * @property string $timecreated
 * @property string $uploaderid
 * @property string $md5
 * @property string $size
 * @property string $status
 */
class Video extends CActiveRecord
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
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    $this->type = $this->defineVideoType($this->link);
	    $this->uploaderid = Yii::app()->getModule('user')->user()->id;
	    
	    return parent::beforeSave();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		    array('link, name', 'filter', 'filter'=>'trim'),
		    array('link, name', 'required'),
		    
			array('objecttype, type, status', 'length', 'max'=>20),
			array('objectid, timecreated, uploaderid, size', 'length', 'max'=>11),
			array('name, description, link', 'length', 'max'=>255),
			array('md5', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, objecttype, objectid, name, type, description, link, timecreated, uploaderid, md5, size, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
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
			'timecreated' => 'Timecreated',
			'uploaderid' => 'Uploaderid',
			'md5' => 'Md5',
			'size' => Yii::t('coreMessages', 'db_video_size'),
			'status' => Yii::t('coreMessages', 'db_video_status'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('uploaderid',$this->uploaderid,true);
		$criteria->compare('md5',$this->md5,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Массив для конфигурации формы добавления нескольких видео со сторонних ресурсов
	 * 
	 * @return array
	 */
	public function formConfig()
	{
	    return array(
	        'elements'=>array(
	            // название
	            'name'=>array(
	                'type'      => 'text',
	                'maxlength' => 255,
	                'visible'   => true,
	            ),
	            // Ссылка на видео
	            'link'=>array(
	                'type'      => 'url',
	                'maxlength' => 255,
	                'visible'   => true,
	            ),
	        )
	    );
	}
	
	/**
	 * Определить тип видео по ссылке
	 * @param string $link - ссылка на видео
	 * @return string|bool - тип видео или false в случае ошибки
	 */
	public function defineVideoType($link)
	{
	    if ( mb_ereg('youtube.com', $link) )
	    {
	        return 'youtube';
	    }elseif ( mb_ereg('vk.com', $link) OR mb_ereg('vkontakte', $link) )
	    {
	        return 'vkontakte';
	    }elseif ( mb_ereg('vimeo', $link) )
	    {
	        return 'vimeo';
	    }else
       {
            return false;
        }
	}
}