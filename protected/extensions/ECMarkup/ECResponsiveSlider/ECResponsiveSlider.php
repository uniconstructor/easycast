<?php

/**
 * 
 */
class ECResponsiveSlider extends CWidget
{
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        //Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/boostrap/js/theme.js', CClientScript::POS_END);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('slider');
    }
}