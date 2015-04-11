<?php
/**
 * 
 */
/* @var $this TableConstructor */
$this->widget('booster.widgets.TbWizard', array(
        'type' => 'tabs', // 'tabs' or 'pills'
        'options' => array(
            'class' => '',
            'onTabShow'  => 'js:function(tab, navigation, index) {}',
            'onTabClick' => 'js:function(tab, navigation, index) {return false;}',
        ),
        'tabs' => array(
            array(
                'label'   => 'Home',
                'content' => 'Home Content',
                'active'  => true
            ),
            array('label' => 'Profile', 'content' => 'Profile Content'),
            array('label' => 'Messages', 'content' => 'Messages Content'),
        ),
    )
);
