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
 * Relations:
 * @property SearchScope $scope
 * @property CatalogSection $parent - вкладка верхнего уровня
 * @property CatalogTab[] $tabs
 * @property CatalogFilter[] $searchFilters
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
	        'class' => 'GalleryBehavior',
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
		return array(
		    // Условия выборки анкет в раздел
		    'scope'     => array(self::BELONGS_TO, 'SearchScope', 'scopeid'),
		    // Родительский раздел
		    'parent'    => array(self::BELONGS_TO, 'CatalogSection', 'parentid'),
		    // вкладки внутри раздела
		    'tabs' => array(self::MANY_MANY, 'CatalogTab',
		        "{{catalog_tab_instances}}(sectionid, tabid)"),
		    
		    // Прикрепленные к разделу фильтры поиска (связь типа "мост")
		    'searchFilters' => array(self::MANY_MANY, 'CatalogFilter', 
		        "{{catalog_filter_instances}}(linkid, filterid)", 
		        'condition' => "`linktype` = 'section'"),
		    
		    // ссылки на вкладки в разделе
		    // @deprecated использовалось пока я не умел писать связи типа "мост"
		    // @todo удалить при рефакторинге
		    'instances' => array(self::HAS_MANY, 'CatalogTabInstance', 'sectionid'),
		    
		    // ссылки на фильтры поиска в разделе
		    // @deprecated использовалось пока я не умел писать связи типа "мост"
		    // @todo удалить при рефакторинге, вместо нее использовать связь searchFilters
		    'filterinstances' => array(self::HAS_MANY, 'CatalogFilterInstance', 'linkid',
		        'condition' => "`linktype` = 'section'"),
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
	 * 
	 * @todo удалить все поля, которые не используются при поиске в админке
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('parentid',$this->parentid,true);
		$criteria->compare('scopeid',$this->scopeid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('shortname',$this->shortname,true);
		$criteria->compare('lang',$this->lang,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('order',$this->order,true);
		$criteria->compare('count',$this->count,true);
		$criteria->compare('visible',$this->visible);

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
		    'pagination' => false,
		));
	}
	
	/**
	 * Получить ссылку на картинку с аватаром категории
	 *
	 * @return string - url картинки или пустая строка, если у раздела нет аватара
	 */
	public function getAvatarUrl($size='small')
	{
	    $nophoto = Yii::app()->createAbsoluteUrl('//images/group.png');
	    if ( ! $avatar = $this->getGalleryCover() )
	    {// Картинка раздела еще не загружена
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