<?php

/**
 * Добавляет возможность создавать поисковые критерии по связанным моделям
 */
class m130403_094400_upgradeSearchScopes extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    public function safeUp()
    {
        $table = "{{search_scopes}}";
        $this->dropIndex('idx_model', $table);
        $this->dropColumn($table, 'model');
        $this->addColumn($table, 'modelid', "int(11) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_modelid', $table, 'modelid');
        unset($table);
        
        
        $table = "{{scope_conditions}}";
        $this->addColumn($table, 'issingle', "tinyint(1) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_issingle', $table, 'issingle');
        $this->addColumn($table, 'join', "enum('inner', 'left', 'right', 'cross', 'natural') DEFAULT NULL");
        $this->createIndex('idx_join', $table, 'join');
        $this->addColumn($table, 'jointable', "varchar(255) DEFAULT NULL");
        $this->createIndex('idx_jointable', $table, 'jointable');
        $this->addColumn($table, 'joincondition', "varchar(4095) DEFAULT NULL");
        $this->createIndex('idx_joincondition', $table, 'joincondition');
        $this->addColumn($table, 'joinparams', "varchar(4095) DEFAULT NULL");
        $this->createIndex('idx_joinparams', $table, 'joinparams');
        
        unset($table);
        
        
        $table = "{{allowed_comparison_types}}";
        $this->dropIndex('idx_model', $table);
        $this->dropColumn($table, 'model');
        $this->addColumn($table, 'modelid', "int(11) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_modelid', $table, 'modelid');
        unset($table);
        
        $table = "{{search_scope_models}}";
        $fields = array(
            "id" => "pk",
            // scope-owner of this condition
            "class" => "varchar(255) NOT NULL",
            );
        $this->createTable($table, $fields, $this->MySqlOptions);
    }
}