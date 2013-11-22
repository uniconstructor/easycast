<?php

class m131120_131000_fillFilmsData extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::import('questionary.models.*');
        Yii::import('questionary.models.complexValues.*');
        $count = 0;
        
        $criteria = new CDbCriteria();
        $criteria->addCondition("`name` = ''");
        $criteria->limit = 100;
        while ( $items = QFilmInstance::model()->findAll($criteria) )
        {
            foreach ( $items as $item )
            {
                if ( ! $film = $item->film )
                {
                    throw new CException('Film not found. itemid='.$item->id.', filmid='.$item->filmid);
                }
                if ( ! $film->name )
                {
                    $item->name = '-';
                }else
                {
                    $item->name = $film->name;
                }
                $item->director = $film->director;
                $item->date     = $film->date;
                
                if ( ! $item->save(false) )
                {
                    throw new CException('Item not saved. id='.$item->id);
                }
                //echo 'Updating item#'.$item->id."|name:{$item->name}\n";
                $count++;
            }
        }
        
        echo 'Records upgraded:'.$count."\n";
    }
}