<?php

class m141121_154400_addVideoExternalFileId extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{video}}";
        $this->addColumn($table, 'externalfileid', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_externalfileid', $table, 'externalfileid');
        $this->refreshTableSchema("{{video}}");
        
        // ищем все видео, которые ссылаются на файлы amazon
        $s3Videos = $this->dbConnection->createCommand()->select('*')->
            from('{{video}}')->where("type='file'")->queryAll();
        foreach ( $s3Videos as $video )
        {// привязываем все существующие записи видео к файлам Amazon
            $path         = pathinfo($video['externalid'], PATHINFO_DIRNAME);
            $fileName     = pathinfo($video['externalid'], PATHINFO_FILENAME);
            $condition    = "bucket = 'video.easycast.ru' AND storage='s3' AND path = '{$path}'";
            
            // ищем созданные файлы в нашей базе
            $externalFile = $this->dbConnection->createCommand()->select('*')->
                from('{{external_files}}')->where($condition)->queryRow();
            if ( $externalFile )
            {// запись о загруженном файле есть - привяжем видео к ней
                $this->update('{{video}}', array('externalfileid' => $externalFile['id']), 'id='.$video['id']);
            }else
            {// записи о файле нет в базе, но сам файл есть на Amazon S3 - так что создадим
                // для него запись в таблице {{extrernal_files}} и привяжем видео к ней
                $newFile = array(
                    'storage'      => 's3',
                    'mimetype'     => '',
                    'title'        => $video['name'],
                    'oldname'      => $fileName,
                    'name'         => $fileName,
                    'description'  => $video['description'],
                    'path'         => $path,
                    'timecreated'  => time(),
                    'timemodified' => time(),
                    'lastupload'   => time(),
                    'lastsync'     => time(),
                    'status'       => 'swExternalFile/active',
                );
                $this->insert('{{external_files}}', $newFile);
                $externalFileId = $this->getDbConnection()->getLastInsertID();
                // привязываем видео к созданной записи файла
                $this->update('{{video}}', array('externalfileid' => $externalFileId), 'id='.$video['id']);
            }
        }
        // удаляем неиспользуемое поле
        $this->dropColumn("{{external_files}}", 'previousid');
    }
}