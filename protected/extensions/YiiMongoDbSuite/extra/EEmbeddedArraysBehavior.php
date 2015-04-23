<?php
/**
 * EEmbeddedArraysBehavior.php
 *
 * PHP version 5.2+
 *
 * @author		Dariusz Górecki <darek.krk@gmail.com>
 * @author		Invenzzia Group, open-source division of CleverIT company http://www.invenzzia.org
 * @copyright	2011 CleverIT http://www.cleverit.com.pl
 * @license		http://www.yiiframework.com/license/ BSD license
 * @version		1.3
 * @category	ext
 * @package		ext.YiiMongoDbSuite
 * @since		v1.0
 */

/**
 * @since v1.0
 */
class EEmbeddedArraysBehavior extends EMongoDocumentBehavior
{
	/**
	 * Name of property witch holds array od documents
	 *
	 * @var string $arrayPropertyName
	 * @since v1.0
	 */
	public $arrayPropertyName;

    /**
     * Class name of doc in array.
     * If the classField is defined, this will be default class if the value is
     * missing from the embedded document.
     *
     * @var string $arrayDocClassName
     * @since v1.0
     */
    public $arrayDocClassName;

    /**
     * Name of an attribute on the embedded document that should be appended to the
     * classPrefix to form the specific class to use.
     * If the value is not set, arrayDocClassName will be used as a default
     * @var string|null
     * @since v1.4.2
     */
    public $classField;

    /**
     * Class name prefix to be prepended to the $classField (if defined)
     * @var string
     * @since v1.4.2
     */
    public $classPrefix;

	private $_cache;

	/**
	 * This flag shows us if we're connected to an embedded document
	 *
	 * @var boolean $_embeddedOwner
	 */
	private $_embeddedOwner;

	public function events() {
		if (!$this->_embeddedOwner) {
			return parent::events();
		}
		else {
			// If attached to an embedded document these events are not defined
			// and would throw an error if attached to
			$events = parent::events();
			unset($events['onBeforeSave']);
			unset($events['onAfterSave']);
			unset($events['onBeforeDelete']);
			unset($events['onAfterDelete']);
			unset($events['onBeforeFind']);
			unset($events['onAfterFind']);
			return $events;
		}
	}

	/**
	 * @since v1.0
	 * @see CBehavior::attach()
	 */
	public function attach($owner)
	{
		$this->_embeddedOwner = !($owner instanceof EMongoDocument);

		parent::attach($owner);

		$this->parseExistingArray();
	}

	/**
	 * Event: initialize array of embded documents
	 * @since v1.0
	 */
	public function afterEmbeddedDocsInit($event)
	{
		$this->parseExistingArray();
	}

    /**
     * @since v1.0
     */
    protected function parseExistingArray()
    {
        if (is_array($this->getOwner()->{$this->arrayPropertyName})) {
            $arrayOfDocs = array();
            foreach ($this->getOwner()->{$this->arrayPropertyName} as $doc) {
                // Build the class name if dynamic
                if ($this->classField && isset($doc[$this->classField])) {
                    $class = $this->classPrefix . $doc[$this->classField];
                } else {
                    $class = $this->arrayDocClassName;
                }

                // Test if we have correct embedding class
                if (!is_subclass_of($class, 'EMongoEmbeddedDocument')) {
                    $message = $class
                        . ' is not a child class of EMongoEmbeddedDocument';
                    throw new CException(Yii::t('yii', $message));
                }
                $obj = new $class;
                $obj->setAttributes($doc, false);
                $obj->setOwner($this->getOwner());

                // If any EEmbeddedArraysBehavior is attached,
                // then we should trigger parsing of the newly set
                // attributes
                foreach (array_keys($obj->behaviors()) as $name) {
                    $behavior = $obj->asa($name);
                    if ($behavior instanceof EEmbeddedArraysBehavior) {
                        $behavior->parseExistingArray();
                    }
                }
                $arrayOfDocs[] = $obj;
            }
            $this->getOwner()->{$this->arrayPropertyName} = $arrayOfDocs;
        }
    }

    /**
     * Ensure values in the owner attribute have been converted to embedded documents
     * before validating.
     *
     * @param CModelEvent $event event parameter
     *
     * @since v1.4.1
     */
    public function beforeValidate($event)
    {
        parent::beforeValidate($event);
        $docs = $this->getOwner()->{$this->arrayPropertyName};
        if (is_array($docs)) {
            if (is_array(reset($docs))) {
                $this->parseExistingArray();
            } else {
                // Clear errors before validation begins
                foreach ($docs as $doc) {
                    $doc->clearErrors();
                }
           	}
        }
    }

    /**
     * Validate all subdocuments and add errors to the parent object.
     *
     * @param CModelEvent $event event parameter
     *
     * @since v1.0.2
     */
    public function afterValidate($event)
    {
        parent::afterValidate($event);
        if (is_array($this->getOwner()->{$this->arrayPropertyName})) {
            foreach ($this->getOwner()->{$this->arrayPropertyName} as $doc) {
                if (!$doc->validate(null, false)) {
                    $this->getOwner()->addErrors($doc->getErrors());
                }
            }
        }
    }

	public function beforeToArray($event)
	{
		if(is_array($this->getOwner()->{$this->arrayPropertyName}))
		{
			$arrayOfDocs = array();
			$this->_cache = $this->getOwner()->{$this->arrayPropertyName};

			foreach($this->_cache as $doc)
			{
				$arrayOfDocs[] = $doc->toArray();
			}

			$this->getOwner()->{$this->arrayPropertyName} = $arrayOfDocs;
			return true;
		}
		else
			return false;
	}

	/**
	 * Event: re-initialize array of embedded documents which where toArray()ized by beforeSave()
	 */
	public function afterToArray($event)
	{
		$this->getOwner()->{$this->arrayPropertyName} = $this->_cache;
		$this->_cache = null;
	}
}
