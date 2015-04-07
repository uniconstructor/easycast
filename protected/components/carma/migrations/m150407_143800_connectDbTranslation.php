<?php

/**
 * 
 */
class m150407_143800_connectDbTranslation extends EcMigration
{
    /**
     * @var string
     */
    public $dbMessageSourceComponent = 'dbMessages';
    
    /**
     * @see parent::safeUp()
     */
    public function safeUp()
    {
        // получаем модуль CDbMessageSource
        /* @var $msgComponent CDbMessageSource */
        if ( ! $msgComponent = Yii::app()->getComponent($this->dbMessageSourceComponent) )
        {
            throw new CException('Error: CDbMessageSource component with name "'.
                $this->dbMessageSourceComponent.'" not found: check "components" section in your config.php');
        }
        // регистрируем языковые таблицы как собственные AR-классы
        $arModelIds = array();
        // языковая строка
        $this->insert("{{ar_models}}", array(
            'model'         => 'ArI18nMessage',
            'table'         => $msgComponent->sourceMessageTable,
            'timecreated'   => time(),
            'timemodified'  => 0,
            'title'         => 'Языковая строка',
            'description'   => 'Служебный объект',
            'system'        => 1,
            'defaultformid' => 0,
        ));
        $arModelIds['ArI18nMessage'] = $this->getDbConnection()->lastInsertID;
        // перевод
        $this->insert("{{ar_models}}", array(
            'model'         => 'ArI18nTranslation',
            'table'         => $msgComponent->translatedMessageTable,
            'timecreated'   => time(),
            'timemodified'  => 0,
            'title'         => 'Перевод языковой строки',
            'description'   => 'Служебный объект',
            'system'        => 1,
            'defaultformid' => 0,
        ));
        $arModelIds['ArI18nTranslation'] = $this->getDbConnection()->lastInsertID;
        
        // добавляем связи между таблицами перевода
        $relations = array(
            // связываем перевод с языковой строкой
            array(
                'modelid'       => $arModelIds['ArI18nTranslation'],
                'name'          => 'sourceMessage',
                'type'          => CActiveRecord::BELONGS_TO,
                'fk0'           => 'id',
                'relatedmodel'  => 'ArI18nMessage',
                'title'         => 'Языковая строка',
                'description'   => 'Языковая строка которая переводится, указывается на английском',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
            // прикрепляем к языковой строке все ее переводы (по одному переводу на каждый язык)
            array(
                'modelid'       => $arModelIds['ArI18nMessage'],
                'name'          => 'translations',
                'type'          => CActiveRecord::HAS_MANY,
                'fk0'           => 'id',
                'relatedmodel'  => 'ArI18nTranslation',
                'title'         => 'Переведенный текст языковой строки',
                'description'   => 'Все выполненные переводы этой строки (один язык - один перевод)',
                'timecreated'   => time(),
                'timemodified'  => 0,
            ),
        );
    }
}