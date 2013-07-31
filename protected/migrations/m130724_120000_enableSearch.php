<?php

/**
 * Эта миграция включает раздел "поиск" на главной странице
 */
class m130724_120000_enableSearch extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::import('application.extensions.ECMarkup.ECMainMenu.models.ECMenuItem');
        
        $item = ECMenuItem::model()->find("`type`='customer' AND `label`='search'");
        $item->visible = 1;
        $item->save();
    }
}