<?php

/** Установка дополнительных полей в анкете пользователя:
 * - покрашены ли волосы
 * - если да, то в какой цвет
 * - фотомодель
 * - промо-модель
 * - рейтинг анкеты
 * 
 * @author frost
 *
 */
class m121123_211014_upgradeQuestionaryFields extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_model;
    
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        if ( ! Yii::app()->getModule('questionary') )
        {
            echo "\n\nAdd to console.php :\n"
            ."'modules'=>array(\n"
            ."...\n"
            ."    'questionary'=>array(\n"
            ."        ... # copy settings from main config\n"
            ."    ),\n"
            ."...\n"
            ."),\n"
            ."\n";
            return false;
        }
        
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        // dropping old indexes
        $this->dropIndex('idx_voicetimbre', $table);
        $this->dropIndex('idx_dancerlevel', $table);
        $this->dropIndex('idx_countryid', $table);
        $this->dropIndex('idx_vkprofile', $table);
        $this->dropIndex('idx_mobilephone', $table);
        $this->dropIndex('idx_homephone', $table);
        $this->dropIndex('idx_addphone', $table);
        
        // this index is dropped because of change field type
        $this->dropIndex('idx_country', $table);
        
        // removing old profile fields
        $this->dropColumn($table,'dancerlevel');
        $this->dropColumn($table,'voicetimbre');
        $this->dropColumn($table,'countryid');
        
        // modify "country" field (only country code will be stored now) 
        $this->alterColumn($table, 'country', "varchar(3) DEFAULT NULL");
        
        // adding new profile fiedls
        $this->addColumn($table, 'isphotomodel', "tinyint(1) DEFAULT NULL");
        $this->addColumn($table, 'ispromomodel', "tinyint(1) DEFAULT NULL");
        $this->addColumn($table, 'iscoloredhair', "tinyint(1) DEFAULT NULL");
        $this->addColumn($table, 'newhaircolor', "enum('brunet', 'fair', 'blond', 'ginger', 'brown', 'ashen', 'hoar', 'darkfair') DEFAULT NULL");
        $this->addColumn($table, 'rating', "int(6) DEFAULT NULL");
        
        // create indexes for new fields
        $this->createIndex('idx_isphotomodel', $table, 'isphotomodel');
        $this->createIndex('idx_ispromomodel', $table, 'ispromomodel');
        $this->createIndex('idx_iscoloredhair', $table, 'iscoloredhair');
        $this->createIndex('idx_newhaircolor', $table, 'newhaircolor');
        $this->createIndex('idx_rating', $table, 'rating');
        $this->createIndex('idx_country', $table, 'country');
    }
    
    /** 
     * (non-PHPdoc)
     * @see CDbMigration::safeDown()
     */
    public function safeDown()
    {
        $table = Yii::app()->getModule('questionary')->questionaryTable;
        
        $this->dropIndex('idx_isphotomodel', $table);
        $this->dropIndex('idx_ispromomodel', $table);
        $this->dropIndex('idx_iscoloredhair', $table);
        $this->dropIndex('idx_newhaircolor', $table);
        $this->dropIndex('idx_rating', $table);
        $this->dropIndex('idx_country', $table);
        
        $this->dropColumn($table,'isphotomodel');
        $this->dropColumn($table,'ispromomodel');
        $this->dropColumn($table,'iscoloredhair');
        $this->dropColumn($table,'newhaircolor');
        $this->dropColumn($table,'rating');
        
        $this->addColumn($table, 'country', "varchar(255) DEFAULT NULL");
        $this->createIndex('idx_country', $table, 'country');
        $this->addColumn($table, 'countryid', "int(11) DEFAULT NULL");
        $this->createIndex('idx_countryid', $table, 'countryid');
    }
}