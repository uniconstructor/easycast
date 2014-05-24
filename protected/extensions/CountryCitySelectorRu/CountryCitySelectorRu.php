<?php

/**
 * CountryCitySelectorRu
 * ======================
 *
 * This extension allows you set contry/region/city or country/city parameters for your models
 * It contains 105 countries and 10.969 cities
 * Only russian language supported
 * 
 * @author Ilya Smirnov php1602agregator@gmail.com
 * @license GPL v3
 * @category UI
 * @version 1.0
 * 
 * @todo этот устаревший модуль должен быть полностью переписан либо заменен более новым
 *       он используеься в нескольких местах системы (в основном в анкете), все вызовы 
 *       при рефакторинге должны быть проверены и переписаны.
 *       После замены этого виджета на новый следует удалить этот модуль из системы полностью
 */
class CountryCitySelectorRu extends CComponent
{
    /**
     * @var array - default country selector settings 
     *             ['default'] (id or code) required, if counry selector not used in form
     *             ['topKeys'] array of country codes (or ids), 
     *                         set the specified keys at the top of the dropDownList
     *             ['excludeKeys'] array of country codes (or ids), 
     *                             this countries will be excluded from the dropDownList
     *             ['selectKeys'] array - Add only the specified keys
     *             ['model'] - form (or AR) model object. Set only if this field located in different model than others
     *                         Default value is $this->model
     */
    protected $country;
    
    /**
     * @var int - default region selector settings
     *             ['default'] - region id. Required if city will be selected by region, and region selector not used in form
     *             ['topKeys'] array of region ids. 
     *                         Set the specified keys at the top of the dropDownList
     *             ['excludeKeys'] array of region ids, 
     *                             this regions will be excluded from the dropDownList
     *             ['selectKeys'] array - Add only the specified keys
     *             ['model'] - form (or AR) model object. Set only if this field located in different model than others
     *                         Default value is $this->model
     */
    protected $region;
    
    /**
     * @var int - default city autocomplete settings
     *             ['default'] - city id.
     *             ['excludeKeys'] array of city ids.
     *                             This cities will be excluded from the autocomplete result
     *             ['selectKeys'] array of city ids. Only the specified keys will be returned from autocomplete result
     *             ['maxRows'] int - maximum of results returned. Default is $this->maxCities
     *             ['model'] - form (or AR) model object. Set only if this field located in different model than others
     *                         Default value is $this->model
     */
    protected $city;
    
    /**
     * @var CModel - form (or AR) model object for country, city and region fields
     *                (if all fields are belong to same model)
     */
    public $model;
    
    /**
     * 
     * @var CController - current page controller. Used to create autocomplete widget
     */
    public $controller;
    
    /**
     * countries table name
     * @var string
     */
    public $countriesTable = "{{geo_countries}}";
    /**
     * regions table name
     * @var string
     */
    public $regionsTable = "{{geo_regions}}";
    /**
     * cities table name
     * @var string
     */
    public $citiesTable = "{{geo_cities}}";
    
    /**
     * @var CountryCitySelectorRu
     */
    protected static $_countryCitySelectorRu;
    
    /**
     * @var string - full country selector name in the form (ModelClass[fieldname])
     *                used by city autocomplete element
     *                NULL if country selector is not used
     */
    protected $countryField;
    
    /**
     * @var string - full region selector name in the form (ModelClass[fieldname])
     *                used by city autocomplete element
     *                NULL if region selector is not used
     */
    protected $regionField;
    
    /**
     * @var string - full city autocomplete name in the form (ModelClass[fieldname])
     */
    protected $cityField;
    
    /**
     * Maximum autocomplete results, returned by AJAX response
     * @var int
     */
    protected $maxCities = 15;
    
    
    /**
     * 
     * @param array $config initial widget settings
     *                       - $config['counry'] - default country settings (array)
     *                       - $config['region'] - default region settings (array)
     *                       - $config['city'] - default city settings (array)
     *                       - $config['model'] - form (or AR) model object for country, city and region fields
     *                                            (if all fields belongs to same model)
     */
    public function __construct($config=array())
    {
        $this->setConfig($config);
    }
    
    /**
     * 
     * @param array $config initial widget settings
     *                       - $config['counry'] - default country settings (array)
     *                       - $config['region'] - default region settings (array)
     *                       - $config['city'] - default city settings (array)
     *                       - $config['model'] - form (or AR) model object for country, city and region fields
     *                                            (if all fields belongs to same model)
     */
    public function setConfig($config=array())
    {
        if ( isset($config['model']) )
        {
            $this->model = $config['model'];
        }
        
        // setting up field defaults
        $this->country['default']     = null;
        $this->country['topKeys']     = array();
        $this->country['excludeKeys'] = array();
        $this->country['selectKeys']  = array();
        $this->country['model']       = $this->model;
        
        $this->region['default']      = null;
        $this->region['topKeys']      = array();
        $this->region['excludeKeys']  = array();
        $this->region['selectKeys']   = array();
        $this->region['model']        = $this->model;
        
        $this->city['default']        = null;
        $this->city['topKeys']        = array();
        $this->city['excludeKeys']    = array();
        $this->city['selectKeys']     = array();
        $this->city['maxRows']        = $this->maxCities;
        $this->city['model']          = $this->model;
        
        if ( ! empty($config) )
        {// override default config values if needed
            foreach($config as $key=>$value)
            {
                if ( in_array($key, array('country', 'region', 'city')) )
                {
                    $this->$key = CMap::mergeArray($this->$key, $value);
                }else
              {
                    $this->$key = $value;
                }
            }
        }
        
        // including all model classes
        Yii::import('ext.CountryCitySelectorRu.models.CSGeoCountry');
        Yii::import('ext.CountryCitySelectorRu.models.CSGeoRegion');
        Yii::import('ext.CountryCitySelectorRu.models.CSGeoCity');
        
        self::$_countryCitySelectorRu = $this;
    }
    
    /**
     * Static function to create a instance of CountryCitySelectorRu
     *
     * @param array $config
     * @return CountryCitySelectorRu
     * @since 2.1
     */
    public static function instance($config = array())
    {
        if (self::$_countryCitySelectorRu === null)
            self::$_countryCitySelectorRu = new CountryCitySelectorRu($config);
    
        return self::$_countryCitySelectorRu;
    }
    
    /**
     * Static function to get a specific country name
     *
     * Usage:
     * The code
     * $countryName = CountryCitySelectorRu::getCountryName('RU');
     * will return 'Россия', the countryname from countrycode 'RU' in russian
     *
     * If you have a activeDropDown with key 'countryCode',
     * you maybe need the name to save with the model in the a controller action after
     * submitted form:
     *
     * $model->countryname = CountryCitySelectorRu::getCountry('countryCode',$model->countrycode,'countryName')->name;
     *
     *
     * @param string $codeOrId international counry code ('RU' for example) or id in 'geo_countries' table
     * @return string
     */
    public static function getCountry($codeOrId)
    {
        if ( intval($codeOrId) )
        {
            $result = CSGeoCountry::model()->findByPk($codeOrId);
        }else
       {
            $criteria = new CDbCriteria();
            $criteria->condition = 'code=:code';
            $criteria->params    = array(':code' => $codeOrId);
            $result = CSGeoCountry::model()->find($criteria);
        }
        
        return $result;
    }
    
    /**
     * Static function to get a specific region
     *
     * @param string $id in 'geo_regions' table
     * @return string
     */
    public static function getRegion($id)
    {
        return CSGeoRegion::model()->findByPk($id);
    }
    
    /**
     * Static function to get a specific city
     *
     * @param string $id in 'geo_cities' table
     * @return string
     */
    public static function getCity($id)
    {
        return CSGeoCity::model()->findByPk($id);
    }
    
    /**
     * @return array - list of all countries
     */
    public function getCountries()
    {
        // adding some elements on the top if needed
        $countries = $this->getTopKeys('country');
        $topKeys   = array_keys($countries);
        
        // creating exclude and include conditions if any
        $criteria = new CDbCriteria();
        $criteria = $this->getListCriteria('country', $criteria);
        
        $data = CSGeoCountry::model()->findAll($criteria);
        $data = $this->setTopKeys($topKeys, $data);
        
        return $data;
    }
    
    /**
     * List of all country regions
     * 
     * @param string|int $countryNameOrId - country code or id
     * @return array
     */
    public function getRegions($countryNameOrId)
    {
        // adding some elements on the top if needed
        $regions = $this->getTopKeys('region');
        
        if ( $country = self::getCountry($countryNameOrId) )
        {// country not found - cannot load regions
            return $regions;
        }
        
        // selecting regions by country
        $criteria = new CDbCriteria();
        $criteria->condition = 'countryid = :countryid';
        $criteria->params    = array(':countryid' => $country->id);
        
        // creating exclude and include conditions if any
        $criteria = $this->getListCriteria('region', $criteria);
        
        return array_merge($regions, CSGeoRegion::model()->findAll($criteria));
    }
    
    /**
     * List of all cities (by country or by region)
     * 
     * @param string $parentType - witch object city to belongs to. Allowed values 'country' or 'region'
     * @param int|string $parentId - country name or code (if $parentType = 'country') or region id (if $parentType = 'city')
     * 
     * @return array
     */
    public function getCities($parentType, $parentId, $userInput='')
    {
        $cities = array();
        
        $criteria = new CDbCriteria();
        $criteria->limit = $this->city['maxRows'];
        
        // creating exclude and include conditions if any
        $criteria = $this->getListCriteria('city', $criteria);
        
        if ( $parentType == 'country' )
        {
            if ( ! $country = self::getCountry($parentId) )
            {// country not found
                return $cities;
            }
            $criteria->condition = 'countryid = :countryid';
            $criteria->params    = array(':countryid' => $country->id);
        }elseif ( $parentType == 'region' )
        {
            if ( ! $region = self::getRegion($parentId) )
            {// region not found
                return $cities;
            }
            $criteria->condition = 'regionid = :regionid';
            $criteria->params    = array(':regionid' => $region->id);
        }else
       {
            throw new CException(get_class($this).'->getCities(): incorrect $parentType value. Allowed values "country" or "region".');
        }
        
        if ( $userInput )
        {
            $criteria = $criteria->addSearchCondition('name', $userInput.'%', false);
        }
        
        return CSGeoCity::model()->findAll($criteria);
    }
    
    /**
     * Generates a drop down list for countries
     *
     * @param string $name the input name
     * @param string $select the selected value
     * @param array $htmlOptions see CHtml::dropDownList
     * @return string
     */
    public function countryField($name, $select='', $htmlOptions=array())
    {
        $this->setFieldName('country', $name);
        
        $countries = $this->getCountries();
        $data      = $this->getDropDownOptions($countries);
        
        if ( ! $select )
        {
            $select = $this->country['default'];
        }
        
        return CHtml::dropDownList($name, $select, $data, $htmlOptions);
    }
    
    /**
     * Generates a active drop down list for countries
     *
     * @param string $attribute the model attribute
     * @param string $model the data model
     * @param array $htmlOptions see CHtml::activeDropDownList
     * @return string
     */
    public function countryActiveField($attribute, $model=null, $htmlOptions=array())
    {
        if ( ! $model )
        {// model not set - searching it in element config
            if ( ! $this->country['model'] )
            {
                $model = $this->model;
            }else
           {
                if ( ! $this->country['model'] )
                {// model not set externally or internally - cannot create ActiveDropDown
                    throw new CException(get_class($this).'->countryActiveField() - model not set');
                }
                $model = $this->country['model'];
            }
        }
        
        $this->setFieldName('country', $attribute, $model);
        
        // list of all countries
        $countries = $this->getCountries();
        $data      = $this->getDropDownOptions($countries);
        $data      = $this->setTopKeys(array_keys($this->getTopKeys('country')), $data);
        $data      = CMap::mergeArray(array('' => ' '), $data);
        
        return CHtml::activeDropDownList($model, $attribute, $data, $htmlOptions);
    }
    
    /**
     * Generates a drop down list for regions
     *
     * @param string $name the input name
     * @param string $select the selected value
     * @param array $htmlOptions see CHtml::dropDownList
     * @return string
     */
    public function regionField($name, $select='', $htmlOptions=array())
    {
        $this->setFieldName('region', $name);
    }
    
    /**
     * Generates a active drop down list for regions
     *
     * @param string $attribute the model attribute
     * @param string $model the data model
     * @param array $htmlOptions see CHtml::activeDropDownList
     * @return string
     */
    public function regionActiveField($attribute, $model=null, $htmlOptions=array())
    {
        if ( ! $model )
        {// model not set - searching it in element config
            if ( ! $this->region['model'] )
            {
                $model = $this->model;
            }else
           {
                if ( ! $this->region['model'] )
                {// model not set externally or internally - cannot create ActiveDropDown
                    throw new CException(get_class($this).'->regionActiveField() - model not set');
                }
                $model = $this->region['model'];
            }
        }
        
        $this->setFieldName('region', $attribute, $model);
    }
    
    /**
     * Generates an autocomplete field for cities
     *
     * @param string $name the input name
     * @param string $select the selected value
     * @param array $htmlOptions see CHtml::dropDownList
     * @return string
     */
    public function cityField($name, $select='', $htmlOptions=array())
    {
        $this->setFieldName('city', $name);
        
        if ( ! $select )
        {
            $select = $this->country['default'];
        }
    }
    
    /**
     * Generates an active autocomplete field for cities
     *
     * @param string $attribute the model attribute
     * @param string $model the data model
     * @param array $htmlOptions see CHtml::activeDropDownList
     * @return string
     */
    public function cityActiveField($attribute, $model=null, $autocompleteOptions=array())
    {
        if ( ! $model )
        {// model not set - searching it in element config
            if ( ! $this->city['model'] )
            {
                $model = $this->model;
            }else
           {
                if ( ! $this->city['model'] )
                {// model not set externally or internally - cannot create ActiveDropDown
                    throw new CException(get_class($this).'->cityActiveField() - model not set');
                }
                $model = $this->city['model'];
            }
        }
        
        $this->setFieldName('city', $attribute, $model);
        
        // including autocomplete plugin with foreign key support
        Yii::import('ext.EJuiAutoCompleteFkField.EJuiAutoCompleteFkField');
        
        // FIXME - its a hack - delete before release!
        if ( $this->city['default'] AND ! $model->$attribute )
        {
            $model->$attribute = $this->city['default'];
        }
        
        $defaultOptions = array(
            'model'=>$model,
            //the FK field (from CJuiInputWidget)
            'attribute'=>$attribute, 
            // display size of the FK field.  only matters if not hidden.  defaults to 10
            'FKFieldSize'=>10,
            // the relation name defined above
            'relName' => 'cityobj',
            // attribute or pseudo-attribute to display
            'displayAttr' => 'name',
            // length of the AutoComplete/display field, defaults to 50
            'autoCompleteLength'=>$this->city['maxRows'],
            
            'options'=>array(
                // number of characters that must be typed before
                // autoCompleter returns a value, defaults to 2
                'minLength'=>2,
            ),
        );
        
        $options = CMap::mergeArray($defaultOptions, $autocompleteOptions);
        
        $this->controller->widget('EJuiAutoCompleteFkField', $options);
    }
    
    /**
     * Store field name after creation. Country and region field names used by city autocomplete field
     * 
     * @param string $type - field type. Allowed values are 'country', 'region' or 'city'
     * @param string $name - field name or model attribute
     * @param CModel $model[optional] - model class (for active fields)
     */
    protected function setFieldName($type, $name, $model=null)
    {
        if ( ! empty($model) )
        {
            $name = get_class($model).'['.$name.']';
        }
        
        switch ( $type )
        {
            case 'country': $this->countryField = $name; break;
            case 'region':  $this->regionField  = $name; break;
            case 'city':    $this->cityField    = $name; break;
            default: throw new CException(get_class($this).'->setFieldName(): 
                            incorrect $type value. Allowed values "country", "city" or "region".');
        }
    }
    
    /**
     * create DB criteria for excluded and selected values in dropdowns and autocomplete
     * 
     * @param string $objectType - 'country', 'region', or 'city'
     * @param CDbCriteria $criteria
     * 
     * @return CDbCriteria
     */
    protected function getListCriteria($objectType, $criteria)
    {
        $keyField='id';
        // selected or excluded options allowed in any list
        $queryTypes = array('select', 'exclude');
        
        foreach ( $queryTypes as $queryType )
        {
            $object = $this->$objectType;
            $keys = $object[$queryType.'Keys'];
            if ( ! empty($keys) AND is_array($keys) )
            {// we need only specified records, or we need to exclude some of them
                
                if ( ! intval($keys[0]) )
                {// check what we got: integer ids or string codes
                    $keyField = 'code';
                }
                
                switch ( $queryType )
                {// see what shoud we do: select or exclude records
                    case 'select':  $criteria = $criteria->addInCondition($keyField, $keys); break;
                    case 'exclude': $criteria = $criteria->addNotInCondition($keyField, $keys); break;
                }
            }
        }
        
        // all lists are ordered by name
        $criteria->order = 'name';
        
        return $criteria;
    }
    
    /**
     * Move the topKeys to the top of the array
     *
     * @param array $data
     */
    public function setTopKeys($topKeys, &$data)
    {
        if ( ! empty($topKeys) )
        {
            $sorted = array();
            foreach ($topKeys as $key)
            {
                if (array_key_exists($key,$data))
                {
                    $sorted[$key] = $data[$key];
                    unset($data[$key]);
                }
            }
            $data = CMap::mergeArray($sorted, $data);
        }
        
        return $data;
    }
    
    /**
     * Get first dropdown list options for country of city
     * @param string $objectType - 'country' or 'region'
     * 
     * @return array of AR records
     */
    public function getTopKeys($objectType)
    {
        $options = array();
        
        $object = $this->$objectType;
        $keys = $object['topKeys'];
        
        if ( ! empty($keys) AND is_array($keys) )
        {
            foreach ( $keys as $key )
            {
                switch ($objectType)
                {
                    case 'country': $option = $this->getCountry($key); break;
                    case 'region':  $option = $this->getRegion($key); break;
                }
                if ( is_object($option) )
                {
                    $options[$option->id] = $option;
                }
            }
        }
        
        return $options;
    }
    
    /**
     * Transforms AR array into id => name array for dropdowns
     * @param array $records
     * @return array
     */
    public function getDropDownOptions($records)
    {
        foreach ( $records as $id => $record )
        {
            $options[$record->id] = $record->name;
        }
        
        return $options;
    }
    
    /**
     * Transforms AR array into array for autocomplete element
     * @param array $records
     * 
     * @return array
     */
    public function getAutocompleteOptions($records)
    {
        $options = array();
        
        foreach ( $records as $record )
        {
            $options[] = array(
                'id'    => $record->id,
                'label' => $record->name,
                'value' => $record->name,
                );
        }
        
        return $options;
    }
}