<?php

/**
 * @see http://www.yiiframework.com/doc/api/1.1/CDbMessageSource#c13630
 */
class m150405_044444_translatedMessageTable extends CDbMigration
{
    /**
     * @var string
     */
    public $dbMessageSourceComponent = 'dbMessage';
    
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
            throw new CException('Error: CDbMessageSource component with name '.$this->dbMessageSourceComponent.
                ' not found: check your config.php');
        }
        $sourceMessageTable = $msgComponent->sourceMessageTable;
        $messageTable       = $msgComponent->translatedMessageTable;
        
        // message sources
        $this->createTable($sourceMessageTable, array(
            'id'           => 'bigpk',
            'category'     => 'string',
            'message'      => 'text',
            'timecreated'  => 'bigint',
            'timemodified' => 'bigint',
        ));
        $this->createIndex('idx_category', $sourceMessageTable, 'category');
        $this->createIndex('idx_timecreated', $sourceMessageTable, 'timecreated');
        $this->createIndex('idx_timemodified', $sourceMessageTable, 'timemodified');
        
        // messages
        $this->createTable($messageTable, array(
            'id'              => 'bigpk',
            'sourcemessageid' => 'bigint',
            'language'        => "varchar(16) NOT NULL DEFAULT '{$language}'",
            'translation'     => 'text',
            'timecreated'     => 'bigint',
            'timemodified'    => 'bigint',
        ));
        $this->createIndex('idx_sourcemessageid', $messageTable, 'sourcemessageid');
        $this->createIndex('idx_language', $messageTable, 'language');
        $this->createIndex('idx_timecreated', $messageTable, 'timecreated');
        $this->createIndex('idx_timemodified', $messageTable, 'timemodified');
        
        $this->addPrimaryKey('PK_message_sourcemessage', $messageTable, 'sourcemessageid, language');
        $this->addForeignKey(
            'FK_message_sourcemessage',
            $messageTable,
            'sourcemessageid',
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

