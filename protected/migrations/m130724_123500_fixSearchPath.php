<?php

class m130724_123500_fixSearchPath extends CDbMigration
{
    public function safeUp()
    {
        Yii::import('application.extensions.ECMarkup.ECMainMenu.models.ECMenuItem');
        
        $item = ECMenuItem::model()->find("`type`='customer' AND `label`='search'");
        $item->visible = 1;
        $item->relpath = 'catalog/catalog/search';
        $item->save();
    }
}