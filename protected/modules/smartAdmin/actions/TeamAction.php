<?php

/** 
 * Страница команды в админке
 */
class TeamAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        //echo 'TEAM<br>';
        Yii::app()->getComponent('carma');
        
        
    }
}

//$this->controller->renderTemplate();
        /*$arTemplate = new ArTemplate;
        $arTemplate->name         = 'testname';
        $arTemplate->title        = 'test title';
        $arTemplate->content      = '<h1>$myData</h1>';
        $arTemplate->save();
        
        $arWidget = new ArWidget;
        $arWidget->name         = 'test widget';
        $arWidget->description  = 'test description';
        $arWidget->templateid   = $arTemplate->id;
        $arWidget->save();
        
        $arAttr1 = new ArAttribute;
        $arAttr1->name             = 'stringattr1';
        $arAttr1->title            = 'Свойство 1 (строка)';
        $arAttr1->valuetypemodelid = 13;
        $arAttr1->multiple         = 0;
        $arAttr1->save();
        
        $arValue1 = new ArValueString;
        $arValue1->value = 'Значение свойства 1 (строка)';
        $arValue1->save();
        
        $arAttr2 = new ArAttribute;
        $arAttr2->name             = 'stringattr2';
        $arAttr2->title            = 'Свойство 2 (число)';
        $arAttr2->valuetypemodelid = 12;
        $arAttr2->multiple         = 0;
        $arAttr2->save();
        
        $arValue2 = new ArValueInt;
        $arValue2->value = 2222;
        $arValue2->save();

        $arModelAttr1 = new ArModelAttribute;
        $arModelAttr1->modelid     = 5;
        $arModelAttr1->objectid    = $arWidget->id;
        $arModelAttr1->attributeid = $arAttr1->id;
        $arModelAttr1->save();
        
        $arModelAttr2 = new ArModelAttribute;
        $arModelAttr2->modelid     = 5;
        $arModelAttr2->objectid    = $arWidget->id;
        $arModelAttr2->attributeid = $arAttr2->id;
        $arModelAttr2->save();
        
        $arAttrValue1 = new ArAttributeValue;
        $arAttrValue1->attributeid  = $arAttr1->id;
        $arAttrValue1->valuemodelid = $arAttr1->valuetypemodelid;
        $arAttrValue1->valueid      = $arValue1->id;
        $arAttrValue1->save();
        
        $arAttrValue2 = new ArAttributeValue;
        $arAttrValue2->attributeid  = $arAttr2->id;
        $arAttrValue2->valuemodelid = $arAttr2->valuetypemodelid;
        $arAttrValue2->valueid      = $arValue2->id;
        $arAttrValue2->save();
        */
        
        //CVarDumper::dump($arTemplate, 10, true);
        //CVarDumper::dump($arWidget, 10, true);
        //CVarDumper::dump($arWidget->getMetadata(), 10, true);
        //CVarDumper::dump($arTemplate->relations(), 10, true);
        //CVarDumper::dump($arWidget->template, 10, true);
        //CVarDumper::dump($arWidget->arAttributes, 10, true);
        //CVarDumper::dump($arAttr2, 10, true);
        //CVarDumper::dump($arAttr2->getMetaData(), 10, true);
        //CVarDumper::dump($arAttr2->relations(), 10, true);
        
        /*$this->controller->widget('app.components.carma.widgets.ArTemplateWidget', array(
            'arTemplate' => $arTemplate,
            'data'       => array('myData' => 'Hello, template!'),
        ));
        */
        //Yii::import('ext.mustache.lib.CMustacheViewRenderer');
        
        //$obj = new StdClass;
        //$obj->mario = 'Mario';
        //$template = '{{planet}} 1. Hello, {{obj.mario}}!';
        //$data     = array('planet' => 'Word', 'obj' => $obj);
        //$renderer = new CMustacheViewRenderer;
        //$renderer->renderTemplate($template, $data);
        