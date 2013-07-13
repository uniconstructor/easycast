<?php

/**
 * Включает пункты меню "мой выбор" и "галерея"
 */
class m130509_063700_enableMenuItems extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_tableName = "{{menu_items}}";
    
    public function safeUp()
    {
        Yii::import('application.extensions.ECMarkup.ECMainMenu.models.ECMenuItem');
        
        // Включаем галерею
        $customerGallery = ECMenuItem::model()->find("`type`='customer' AND `label`='gallery'");
        $customerGallery->relpath = 'photos';
        $customerGallery->visible = 1;
        $customerGallery->save();
        
        $userGallery = ECMenuItem::model()->find("`type`='user' AND `label`='gallery'");
        $userGallery->relpath = 'photos';
        $userGallery->visible = 1;
        $userGallery->save();
        
        // Включаем мой выбор
        $myChoice = ECMenuItem::model()->find("`type`='customer' AND `label`='my_choice'");
        $myChoice->relpath = 'catalog/catalog/myChoice';
        $myChoice->visible = 1;
        $myChoice->save();
    }
}