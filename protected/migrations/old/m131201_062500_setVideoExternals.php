<?php

class m131201_062500_setVideoExternals extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{video}}";
        // убираем null-значения из всех полей
        $this->alterColumn($table, 'objecttype', "varchar(50) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'name', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'objectid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->alterColumn($table, 'description', "varchar(4095) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'link', "varchar(255) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'timecreated', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->alterColumn($table, 'uploaderid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->alterColumn($table, 'md5', "varchar(128) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'size', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->alterColumn($table, 'timemodified', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        
        // ищем все видео с youtube и определяем для каждого id
        $videos = Video::model()->findAll("`type` = 'youtube'");
        foreach ( $videos as $video )
        {
            if ( ! $video->save() )
            {
                throw new CException('Не удалось сохранить видео');
            }
            if ( ! $video->externalid )
            {
                echo "\n"."video #{$video->id} - no external id from link: {$video->link}";
            }
        }
    }
}