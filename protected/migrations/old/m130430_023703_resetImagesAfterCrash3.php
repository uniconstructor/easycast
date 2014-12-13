<?php

/**
 * Спасаем загруженные фотографии после падения 27.04.2013
 * Восстанавливает те фотографии которые можно спасти и удаляет из базы те, которые спасти нельзя
 */
class m130430_023703_resetImagesAfterCrash3 extends CDbMigration
{
    public function safeUp()
    {
        ini_set("max_execution_time","300000");
        
        Yii::import('application.extensions.galleryManager.models.*');
        Yii::import('application.extensions.galleryManager.*');
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'id >= 300 AND id < 400';
        
        $yiiRoot = dirname(Yii::app()->basePath);
        $galleries = GalleryS3::model()->findAll($criteria);
        $restored = 0;
        $deleted  = 0;
        
        foreach ( $galleries as $gallery )
        {
            echo 'processing gallery'.$gallery->id."\n";
            if ( $photos = $gallery->galleryPhotos )
            {// фотографии есть в базе. Проверим, существуют ли они реально на жестком диске
                foreach ( $photos as $photo )
                {// проверяем каждое изображение: сохранился ли оригинал, миниатюра и версии
                    $original = true;
                    $preview  = true;
                    $versions = true;
                    
                    $originalPath = $yiiRoot.'/'.$photo->galleryDir.'/'.$photo->id.'.'.$photo->galleryExt;
                    $previewPath  = $yiiRoot.'/'.$photo->galleryDir.'/_'.$photo->id.'.'.$photo->galleryExt;
                    $versionsData = $photo->gallery->versions;
                    
                    // проверяем сохранность оставшихся файлов
                    
                    if ( ! file_exists($originalPath) )
                    {// оригинал не сохранился
                        $original = false;
                    }
                    if ( ! file_exists($previewPath) )
                    {// миниатюра не сохранилась
                        $preview = false;
                    }
                    
                    foreach ( $versionsData as $version => $actions )
                    {// смотрим, все ли версии на месте
                        $versionPath = $yiiRoot.'/'.$photo->galleryDir.'/'.$photo->id.$version.'.'.$photo->galleryExt;
                        if ( ! file_exists($versionPath) )
                        {// хотя бы одна версия изображения не сохранилась - пересоздаем все
                            $versions = false;
                            break;
                        }
                    }
                    
                    // определяем, пересоздавать изображение или удалять
                    if ( $original )
                    {// оригинал сохранился - определяем, нужно ли что-то пересоздать
                        if ( ! $preview OR ! $versions )
                        {// не сохранилась миниатюра или версии - пересоздадим их
                            $photo->setImage($originalPath);
                            // обновляем историю изменений. Помечаем, что файлы должны быть загружены на S3
                            $photo->save();
                            echo 'Изображение id='.$photo->id.' [Восстановлено]'."\n";
                            $restored++;
                        }
                    }else
                 {// Оригинал не сохранился - оставляем изображение только в том случае 
                        // если миниатюра и все версии в порядке 
                        if ( ! $preview OR ! $versions )
                        {// не сохранилась миниатюра или одна из версий. Ее не из чего пересоздать 
                            // изображение испорчено, удаляем его
                            $photo->delete();
                            echo 'Испорченное изображение id='.$photo->id.' [Удалено]'."\n";
                            $deleted++;
                        }
                    }
                    
                    unset($versionsData);
                }
            }
        }
        
        echo 'Восстановлено: '.$restored."\n";
        echo 'Удалено: '.$deleted."\n";
    }
}