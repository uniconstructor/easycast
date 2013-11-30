<?php

class m131128_120000_addProjectRating extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{projects}}';
        
        // рейтинг  проекта
        $this->addColumn($table, 'rating', 'int(6) NOT NULL DEFAULT 0');
        $this->createIndex('idx_rating', $table, 'rating');
    }
}