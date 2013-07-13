<?php
/**
 * Renders a simple yes/no button with custom settings
 * @todo move all html to external file
 */
class EToggleBox extends CInputWidget
{
    /**
     * @var bool - in debug mode publishes normal version of js and css files, 
     *             if debug is false,publishes minified versions
     */
    public $debug = false;
    
    /**
     * @var array the plugin options
     * [on_label]		Text used for the left-side (on) label. Defaults to "On"
     * [off_label]		Text used for the right-side (off) label. Defaults to "Off"
     * [on_bg_color]		Hex background color for On state
     * [off_bg_color]	Hex background color for Off state
     * [skin_dir]		Document relative (or absolute) path to the skin directory
     * [bypass_skin]		Flags whether to bypass the inclusion of the skin.css file.  
     *                      Used if you've included the skin styles somewhere else already.
     * 
     * @todo [bypass_skin] doesn't work now
     */
    public $options = array();

    /**
     * @var CModel - form model
     */
    public $model     = null;
    
    /**
     * @var string - model attribute
     */
    public $attribute = null;
    
    /**
     * @var string - html-id of the form element
     */
    public $elementId;

    /**
     * Performs some tasks before running the plugin
     */
    public function init()
    {
        parent::init();
        if ( ! $this->elementId )
        {
            $this->elementId = get_class($this->model).'_'.$this->attribute;
        }
        $this->registerClientScript();
    }
    
    /**
     * Executes the widget.
     * This method is called by {@link CBaseController::endWidget}.
     */
    public function run()
    {
        $this->render('string');
    }
    
    /**
     * Registers the javascript code.
     */
    public function registerClientScript()
    {
        $baseUrl = CHtml::asset(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
        $id = __CLASS__ . '#' . $this->getId();
        
        $clientScript = Yii::app()->clientScript;
        //register javascript
        $clientScript->registerCoreScript('jquery');

        if ( YII_DEBUG )
        {
            $toggleScript = '/js/jquery.toggle.js';
        }else
       {
            $toggleScript = '/js/jquery.toggle.min.js';
        }
        // register main js lib
        $clientScript->registerScriptFile($baseUrl . $toggleScript);

        $jsOptions =  CJavaScript::encode($this->options);
        
        $javascript = '';
        $javascript .= "jQuery('$this->elementId').echeckToggle($jsOptions);";

        // register CSS skin
        //Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets/js/skin');
        $clientScript->registerCssFile($baseUrl . DIRECTORY_SEPARATOR . 'js/skin/skin.css');
        
        // register js-init script
        $clientScript->registerScript($id, $javascript, CClientScript::POS_END);
    }

    /**
     * 
     * @return string
     */
    protected function getLabelHtml()
    {
        
    }
    
    /**
     * Get HTML of the hidden checkbox
     * @return string
     */
    protected function getCheckBoxHtml()
    {
        
    }
}