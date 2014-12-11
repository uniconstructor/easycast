<?php

class m131228_091600_addOrderMegaplanId extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{fast_orders}}';
        
        $this->addColumn($table, 'megaplanid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_megaplanid', $table, 'megaplanid');
    }
}