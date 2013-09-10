<?php

class ShareAccessWidget extends CWidget
{
    public $objectType;
    
    public $objectId;
    
    public $selector;
    
    protected $script;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $options = array(
            'html' => true,
            'placement' => 'left',
            //'trigger' => 'click',
            'title' => 'Предоставить доступ к отбору участников',
            'content' => $this->getForm(),
        );
        $options = CJSON::encode($options);
        $this->script = "$('{$this->selector}').popover({$options});";
        Yii::app()->clientScript->registerScript($this->getId().'#append', $this->script, CClientScript::POS_END);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo $this->script;
    }
    
    /**
     * 
     * @return stringS
     */
    protected function getForm()
    {
        $result = '';
        $result .= CHtml::beginForm('/admin/admin/shareAccess', 'POST', array('class' => 'form-horizontal'));
        //$result .= '<div class="control-group">';
        $result .= CHtml::label('e-mail', 'email');
        //$result .= '<div class="controls">';
        $result .= CHtml::textField('email'); //inputField('email', 'email', '', array('placeholder' => 'email'));
        $result .= '<br/>';
        $result .= '<br/>';
        //$result .= '</div>';
        //$result .= '</div>';
        $result .= $this->widget('bootstrap.widgets.TbButton',array(
        	'type' => 'success',
            'buttonType' => 'ajaxButton',
        	//'size' => 'large',
        	'icon' => 'envelope large',
        	'label' => 'Отправить письмо',
            //'toggle' => true,
            'loadingText' => 'Отправка...',
        	'completeText' => 'Письмо отправлено',
        	'url' => '/admin/admin/shareAccess',
        	'ajaxOptions' => array(),
        ), true); 
        
        $result .= CHtml::endForm();
        return $result;
    }
}