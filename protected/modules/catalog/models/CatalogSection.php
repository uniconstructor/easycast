<?php

/**
 * This is the model class for table "{{catalog_sections}}".
 *
 * The followings are the available columns in table '{{catalog_sections}}':
 * @property integer $id
 * @property string $parentid
 * @property string $scopeid
 * @property string $name
 * @property string $shortname
 * @property string $lang
 * @property string $galleryid
 * @property string $content
 * @property string $order
 * @property string $count
 * @property integer $visible
 * 
 * @property SearchScope $scope
 */
class CatalogSection extends CActiveRecord
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.extensions.ESearchScopes.models.*');
        parent::init();
    }
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatalogSection the static model class
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
		return '{{catalog_sections}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    Yii::import('ext.galleryManager.*');
	    Yii::import('ext.galleryManager.models.*');
	    // настройки сохранения логотипа
	    $logoSettings = array(
	        'class' => 'GalleryBehaviorS3',
	        'idAttribute' => 'galleryid',
	        'limit' => 1,
	        // картинка проекта масштабируется в трех размерах
	        'versions' => array(
	            'small' => array(
	                'centeredpreview' => array(150, 150),
	            ),
	        ),
	        // галерея будет без имени
	        'name'        => false,
	        'description' => false,
	    );
	    
	    return array(
	        // логотип
	        'galleryBehavior' => $logoSettings,
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
			array('name, shortname, content', 'required'),
			array('visible', 'numerical', 'integerOnly'=>true),
			array('parentid, scopeid, galleryid, count', 'length', 'max'=>11),
			array('name, shortname', 'length', 'max'=>128),
			array('lang', 'length', 'max'=>5),
			array('content', 'length', 'max'=>8),
			array('order', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parentid, scopeid, name, shortname, lang, galleryid, content, order, count, visible', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 * 
	 * @todo переименовать instances в tabinstances
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		    'scope' => array(self::BELONGS_TO, 'SearchScope', 'scopeid'),
		    'parent' => array(self::BELONGS_TO, 'CatalogSection', 'parentid'),
		    'instances' => array(self::HAS_MANY, 'CatalogTabInstance', 'sectionid'),
		    'filterinstances' => array(self::HAS_MANY, 'CatalogFilterInstance', 'sectionid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parentid' => 'Родительская категория',
			'scopeid' => 'Условие выборки',
			'name' => 'Название',
			'shortname' => 'Короткое название (для ссылок)',
			'lang' => 'Язык',
			'galleryid' => 'Изображение',
			'content' => 'Содержимое (анкеты или другие категории)',
			'order' => 'Вес (чем больше, тем ниже отображается категория)',
			'count' => 'Количество анкет',
			'visible' => 'Видимая категория',
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
		$criteria->compare('parentid',$this->parentid,true);
		$criteria->compare('scopeid',$this->scopeid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('shortname',$this->shortname,true);
		$criteria->compare('lang',$this->lang,true);
		$criteria->compare('galleryid',$this->galleryid,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('order',$this->order,true);
		$criteria->compare('count',$this->count,true);
		$criteria->compare('visible',$this->visible);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Получить ссылку на картинку с аватаром категории
	 *
	 * @return string - url картинки или пустая строка, если у пользователя нет аватара
	 */
	public function getAvatarUrl($size='small')
	{
	    $nophoto = Yii::app()->createAbsoluteUrl('//images/group.png');
	    if ( ! $avatar = $this->getGalleryCover() )
	    {// пользователь еще не загрузил аватар
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