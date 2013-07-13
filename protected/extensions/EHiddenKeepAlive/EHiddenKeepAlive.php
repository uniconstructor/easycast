<?php

/**
 * Small invisible widget for preventing user session termination
 * (useful for long forms)
 */
class EHiddenKeepAlive extends CWidget
{
    /**
     * @var string url for ajax request 
     */
    public $url;
    
    /**
     * @var int refresh period (in seconds)
     */
    public $period = 60;
    
    public $scriptId = 'EHiddenKeepAlive';
    
    public $divId = 'EHiddenKeepAlive';
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $period = $this->period * 1000;
        $script = 'jQuery(function($){
            var keepAliveElement = $("#'.$this->divId.'");
            
            function eHiddenKeepAliveUpdate()
            {
                $.ajax("'.$this->url.'");
            }
            
            eHiddenKeepAliveUpdate();
            setInterval(function(){eHiddenKeepAliveUpdate();}, '.$period.');
        });';
        Yii::app()->clientScript->registerScript($this->scriptId, $script, CClientScript::POS_END);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo '<div style="display:none;" id="'.$this->divId.'"></div>';
    }
}