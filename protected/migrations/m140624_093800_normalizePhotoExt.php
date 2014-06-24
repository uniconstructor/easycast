<?php

class m140624_093800_normalizePhotoExt extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{gallery_photo}}";
        $photos = $this->dbConnection->createCommand()->select('id, file_name')->from($table)->queryAll();
        foreach ( $photos as $photo )
        {
            $info = pathinfo($photo['file_name']);
            if ( isset($info['extension']) AND $info['extension'] === 'jpg' )
            {
                continue;
            }
            $fileName = $info['filename'].'.jpg';
            $this->update($table, array('file_name' => $fileName), 'id='.$photo['id']);
        }
    }
}