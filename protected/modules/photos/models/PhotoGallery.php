<?php

/**
 * This is the model class for table "{{photo_galleries}}".
 *
 * The followings are the available columns in table '{{photo_galleries}}':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $timecreated
 * @property string $timemodified
 * @property string $galleryid
 * @property integer $visible
 */
class PhotoGallery extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PhotoGallery the static model class
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
		return '{{photo_galleries}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    // Добавление галереи к модели
	    Yii::import('ext.galleryManager.*');
	    Yii::import('ext.galleryManager.models.*');
	    // настройки сохранения галереи
	    $photoGallerySettings = array(
	        'class' => 'GalleryBehaviorS3',
	        'idAttribute' => 'galleryid',
	        // масштабируем картинку
	        'versions' => array(
	            'small' => array(
	                'centeredpreview' => array(150, 150),
	            ),
	            'medium' => array(
	                'centeredpreview' => array(300, 300),
	            ),
	            'full' => array(
	                'resize' => array(1280, 1280),
	            ),
	        ),
	        'name'        => true,
	        'description' => true,
	    );
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	        // сама галерея
	        'galleryBehavior' => $photoGallerySettings,
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('visible', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('name', 'required'),
			array('description', 'length', 'max'=>4095),
			array('timecreated, timemodified, galleryid', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description, timecreated, timemodified, galleryid, visible', 'safe', 'on'=>'search'),
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
	 * @todo перенести строки в языковой файл
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название',
			'description' => 'Описание',
			'timecreated' => 'Дата создания',
			'timemodified' => 'Последнее обновление',
			'galleryid' => 'Galleryid',
			'visible' => 'Отображать галерею на сайте?',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('galleryid',$this->galleryid,true);
		$criteria->compare('visible',$this->visible);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Получить ссылку на картинку с обложкой галереи
	 *
	 * @return string - url картинки или пустая строка, если нет аватара
	 */
	public function getAvatarUrl($size='small')
	{
	    $nophoto = '';//Yii::app()->getModule('projects')->_assetsUrl.'/images/nophoto.png';
	    if ( ! $avatar = $this->getGalleryCover() )
	    {// изображения проекта нет
	        return $nophoto;
	    }
	
	    // Изображение загружено - получаем самую маленькую версию
	    if ( ! $avatar = $avatar->getUrl($size) )
	    {
	        return $nophoto;
	    }
	
	    return $avatar;
	}
}