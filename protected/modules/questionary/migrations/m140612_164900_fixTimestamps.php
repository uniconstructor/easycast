<?php

class m140612_164900_fixTimestamps extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{q_activities}}';
        $this->addColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        $this->alterColumn($table, 'timecreated', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        
        $table = '{{q_activity_types}}';
        $this->addColumn($table, 'timecreated', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_timecreated', $table, 'timecreated');
        $this->addColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        
        $table = '{{q_awards}}';
        $this->alterColumn($table, 'timecreated', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        
        $table = '{{q_films}}';
        $this->alterColumn($table, 'date', 'int(11) UNSIGNED DEFAULT NULL');
        
        $table = '{{q_film_instances}}';
        $this->alterColumn($table, 'date', 'int(11) UNSIGNED DEFAULT NULL');
        
        $table = '{{q_recording_conditions}}';
        $this->addColumn($table, 'timecreated', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_timecreated', $table, 'timecreated');
        $this->addColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        
        $table = '{{q_theatre_instances}}';
        $this->alterColumn($table, 'timecreated', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->alterColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        
        $table = '{{q_tvshow_instances}}';
        $this->alterColumn($table, 'timestart', 'int(11) UNSIGNED DEFAULT NULL');
        $this->alterColumn($table, 'timeend', 'int(11) UNSIGNED DEFAULT NULL');
        
        $table = '{{q_university_instances}}';
        $this->addColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        $this->alterColumn($table, 'timecreated', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
    }
}