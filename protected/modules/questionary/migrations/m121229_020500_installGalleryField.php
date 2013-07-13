<?php
/**
 * Устанавливает поле  "id галереи", для работы модуля загрузки изображений
 */
class m121229_020500_installGalleryField extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_tableName = "{{questionaries}}";

    public function safeUp()
    {
        $this->addColumn($this->_tableName, 'galleryid', "int(11) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_galleryid', $this->_tableName, 'galleryid');
    }
}