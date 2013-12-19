<?php

/**
 * Модель для работы с одним разделом каталога
 *
 * Таблица '{{catalog_sections}}':
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
 * @property CatalogSection $parent - родительский раздел
 * @property CatalogTab[] $tabs - прикрепленные вкладки
 * @property CatalogFilter[] $searchFilters
 * @property CatalogTabInstance[] $tabInstances
 */
class CatalogSection extends CActiveRecord
{
    /**
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
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( ! $this->content )
	    {
	        $this->content = 'users';
	    }
	    return true;
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    Yii::import('ext.galleryManager.*');
	    Yii::import('ext.galleryManager.models.*');
	    Yii::import('catalog.extensions.search.SearchFiltersBehavior');
	    
	    return array(
	        // настройки сохранения логотипа
	        'galleryBehavior' => array(
    	        'class' => 'GalleryBehavior',
    	        'idAttribute' => 'galleryid',
    	        'limit' => 1,
    	        'versions' => array(
    	            'small' => array(
    	                'centeredpreview' => array(150, 150),
    	            ),
    	        ),
    	        'name'        => false,
    	        'description' => false,
    	    ),
	        // настройки для прикрепляемых фильтров поиска
	        'filtersBehavior' => array(
	            'class' => 'catalog.extensions.search.SearchFiltersBehavior',
	            'linkType' => 'section',
            ),
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, shortname', 'required'),
			array('visible', 'numerical', 'integerOnly'=>true),
			array('parentid, scopeid, galleryid, count', 'length', 'max'=>11),
			array('name, shortname', 'length', 'max'=>255),
			array('searchdata', 'length', 'max'=>4095),
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
		    // эта связь используется при обновлении набора вкладок в разделе
		    // @todo удалить  instances при рефакторинге, оставить только  tabInstances
		    // @todo заменить все старые обращения к этой связи обращением к связи tabs
		    'instances' => array(self::HAS_MANY, 'CatalogTabInstance', 'sectionid'),
		    'tabInstances' => array(self::HAS_MANY, 'CatalogTabInstance', 'sectionid'),
		    
		    // ссылки на фильтры поиска в разделе
		    // эта связь используется при обновлении набора прикрепленных фильтров поиска
		    // @todo удалить filterinstances при рефакторинге, оставить только  filterInstances (camelCase)
		    // @todo заменить все старые обращения к этой связи обращением к связи searchFilters
		    'filterinstances' => array(self::HAS_MANY, 'CatalogFilterInstance', 'linkid',
		        'condition' => "`linktype` = 'section'"),
		    'filterInstances' => array(self::HAS_MANY, 'CatalogFilterInstance', 'linkid',
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
		$criteria = new CDbCriteria;

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
	 * Привязать вкладки к этому разделу каталога
	 * @param CatalogTab[] $tabs
	 * @return void
	 * 
	 * @todo добавить обработку ошибок
	 */
	public function bindTabs($tabs)
	{
	    $this->clearTabInstances();
	    foreach ( $tabs as $tab )
	    {
	        $instance = new CatalogTabInstance();
	        $instance->sectionid = $this->id;
	        $instance->tabid = $tab->id;
	        $instance->save();
	    }
	}
	
	/**
	 * Очистить старый набор вкладок перед добавлением нового
	 * @return void
	 * 
	 * @todo добавить обработку ошибок
	 */
	protected function clearTabInstances()
	{
	    if ( ! $this->tabInstances )
	    {
	        return;
	    }
	    foreach ( $this->tabInstances as $instance )
	    {
	        $instance->delete();
	    }
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
	
	// эти функции должны быть перенесены в расширение searchScopes как и хранение данных формы поиска
	
	/**
	 * Получить все сохраненные данные из формы поиска людей для вакансии
	 *
	 * @return null
	 */
	protected function getSearchData()
	{
	    return unserialize($this->searchdata);
	}
	
	/**
	 * Получить данные для одного поискового фильтра
	 * ! Важно: все функции, предоставляющие данные для формы поиска должны иметь функцию getFilterSearchData
	 *          Это необходимо для совместимости
	 *
	 * @param string $namePrefix - имя ячейки в массиве данных из формы поиска,
	 *                             в которой лежит сохраненное значение фильтра
	 * @return array
	 */
	public function getFilterSearchData($namePrefix)
	{
	    $searchData = $this->getSearchData();
	     
	    if ( ! isset($searchData[$namePrefix]) )
	    {
	        return array();
	    }
	     
	    return $searchData[$namePrefix];
	}
	
	/**
	 * Обновить данные о критерии выборки людей, которые подходят под эту вакансию
	 * @param array|null $newData - новые условия подбора людей на вакансию
	 * @return bool
	 *
	 * @todo обработать ситуацию, когда набор условий есть, но содержит более одного критерия
	 *       или критерий неправильного типа
	 */
	public function setSearchData($newData)
	{
	    $newDataSerialized = serialize($newData);
	    if ( $this->searchdata == $newDataSerialized )
	    {// если условия выборки не изменились - ничего не надо делать
	        return true;
	    }
	
	    if ( ! $this->scope )
	    {// условие для этой вакансии еще не создано - исправим это
	        $this->initObjectScope();
	    }
	
	    // сохраняем новые данные из формы поиска в вакансию
	    $this->searchdata = $newDataSerialized;
	    $this->save();
	     
	    // обновим составленный критерий поиска (ScopeCondition)
	    // (для вакансии он всегда только один в наборе (SearchScope) и всегда является сериализованным массивом)
	    $conditions = $this->scope->scopeConditions;
	    $condition  = current($conditions);
	    // заново получаем из данных формы поиска критерии для выборки участников
	    $criteria  = CatalogModule::createSearchCriteria($newData, CatalogModule::getFullFilterKit());
	    // сериализуем и сохраняем новые критерии выборки
	    $condition->value = serialize($criteria);
	
	    return $condition->save();
	}
	
	/**
	 * Создать стандартную заготовку условия выбора участников.
	 * Используется для новых, только что созданных объектов.
	 * Условие создается не полностью пустым - изначально в него добавляется правило
	 * "искать только анкеты в активном статусе" и несколько других условий, в зависимости от
	 * данных с которыми создана роль
	 * Создает объект SearchScope с сериализованным критерием выборки на борту и JSON-массив для формы поиска людей
	 *
	 * @throws CDbException
	 * @return int - id группы условий (SearchScope) которая содержит один пустой критерий выборки анкет
	 *
	 * @todo предусмотреть возможность отключать изначальное содержание CDbCriteria
	 * @todo если понадобится - сделать настройку "добавлять/не добавлять префикс 't' к полю status"
	 */
	protected function initObjectScope($saveData=false)
	{
	    // получаем все возможные фильтры поиска
	    $filters = CatalogModule::getFullFilterKit();
	    // создаем группу для условий поиска
	    $scope = new SearchScope;
	    $scope->name      = $this->name;
	    $scope->modelid   = SearchScope::QMODEL_ID;
	    $scope->type      = 'section';
	    $scope->save();
	     
	    // Создаем изначально пустое условие поиска
	    $criteria   = CatalogModule::createSearchCriteria(array(), $filters);
	     
	    // создаем само условие
	    $condition = new ScopeCondition();
	    $condition->scopeid = $scope->id;
	    $condition->type    = 'serialized';
	    $condition->value   = serialize($criteria);
	    $condition->combine = 'and';
	    $condition->save();
	     
	    // сохраняем ссылку на поисковый критерий и данные формы поиска
	    $this->scopeid    = $scope->id;
	    $this->searchdata = serialize($searchData);
	     
	    if ( $saveData )
	    {// нужно сохранить модель после инициализации поисковых критериев
	        return (bool)$this->save();
	    }else
	    {// модель сохранять не нужно - это произойдет само автоматически
	        return true;
	    }
	}
}