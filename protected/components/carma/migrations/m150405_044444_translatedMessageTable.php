<?php

/**
 * 
 */
class m150405_044444_translatedMessageTable extends CDbMigration
{
    /**
     * @var string - name of CDbMessageSource component in config.php
     */
    public $dbMessageSourceComponent = 'dbMessages';
    /**
     * @var string default table options
     */
    public $_mysqlTableOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
    
    /**
     * @see parent::safeUp()
     */
    public function safeUp()
    {
        //set system language as default for new translated messages
        $language = Yii::app()->language;
        /* @var $msgComponent CDbMessageSource */
        if ( ! $msgComponent = Yii::app()->getComponent($this->dbMessageSourceComponent) )
        {
            throw new CException('Error: CDbMessageSource component with name "'.
                $this->dbMessageSourceComponent.'" not found: check "components" section in your config.php');
        }
        // get table names from config
        $sourceMessageTable = $msgComponent->sourceMessageTable;
        $messageTable       = $msgComponent->translatedMessageTable;
        
        // source messages
        $this->createTable($sourceMessageTable, array(
            'id'           => 'bigpk',
            'category'     => "string NOT NULL DEFAULT 'app'",
            'message'      => 'string NOT NULL',
            'timecreated'  => 'bigint',
            'timemodified' => 'bigint',
        ), $this->_mysqlTableOptions);
        $this->createIndex('idx_category', $sourceMessageTable, 'category');
        $this->createIndex('idx_message', $sourceMessageTable, 'message');
        $this->createIndex('idx_timecreated', $sourceMessageTable, 'timecreated');
        $this->createIndex('idx_timemodified', $sourceMessageTable, 'timemodified');
        
        // translated messages
        $this->createTable($messageTable, array(
            'pk'              => 'bigpk',
            'id'              => 'bigint',
            'language'        => "varchar(16) NOT NULL DEFAULT '{$language}'",
            'translation'     => 'text NOT NULL',
            'timecreated'     => 'bigint',
            'timemodified'    => 'bigint',
        ), $this->_mysqlTableOptions);
        $this->createIndex('idx_id', $messageTable, 'id');
        $this->createIndex('idx_language', $messageTable, 'language');
        $this->createIndex('idx_timecreated', $messageTable, 'timecreated');
        $this->createIndex('idx_timemodified', $messageTable, 'timemodified');
        
        $this->addForeignKey(
            'FK_message_sourcemessage',
            $messageTable,
            'id',
            $sourceMessageTable,
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }
    
    /**
     * @see parent::safeDown()
     */
    public function safeDown()
    {
        if ( ! $msgComponent = Yii::app()->getComponent($this->dbMessageSourceComponent) )
        {
            throw new CException('Error: CDbMessageSource component with name '.$this->dbMessageSourceComponent.
                ' not found: check your config.php');
        }
        $sourceMessageTable = $msgComponent->sourceMessageTable;
        $messageTable       = $msgComponent->translatedMessageTable;
        
        $this->dropForeignKey('FK_message_sourcemessage', $messageTable);
        $this->dropTable($sourceMessageTable);
        $this->dropTable($messageTable);
    }
}