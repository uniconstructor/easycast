<?php

class m130517_031800_enableNews extends CDbMigration
{
    private $_tableName = "{{menu_items}}";
    
    public function safeUp()
    {
        Yii::import('application.extensions.ECMarkup.ECMainMenu.models.ECMenuItem');
    
        $news = ECMenuItem::model()->find("`type`='user' AND `label`='news'");
        $news->visible = 1;
        $news->save();
    }
}