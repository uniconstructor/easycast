<?php

class m121102_025332_installQuestionary extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_model;

    public function safeUp()
    {
        if ( ! Yii::app()->getModule('questionary') ) {
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
        //Yii::import('questionary.models.Questionary');
        
        // большинство полей модет быть NULL - так мы определяем,
        // заполнял из пользователь это поле или еще нет
        $fields = array(
            "id" => "pk",
            "userid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "mainpictureid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "firstname" => "varchar(128) DEFAULT NULL",
            "lastname" => "varchar(128) DEFAULT NULL",
            "middlename" => "varchar(128) DEFAULT NULL",
            "birthdate" => "int(11) DEFAULT NULL",
            "gender" => "enum('male', 'female') DEFAULT NULL",
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timefilled" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "height" => "int(6) UNSIGNED DEFAULT NULL",
            "weight" => "int(6) UNSIGNED DEFAULT NULL",
            "wearsizemin" => "int(2) UNSIGNED DEFAULT NULL",
            "wearsizemax" => "int(2) UNSIGNED DEFAULT NULL",
            "shoessize" => "int(2) UNSIGNED DEFAULT NULL",
            "city" => "varchar(128) DEFAULT NULL",
            "cityid" => "int(11) NOT NULL DEFAULT 0",
            "mobilephone" => "varchar(32) DEFAULT NULL",
            "homephone" => "varchar(32) DEFAULT NULL",
            "addphone" => "varchar(32) DEFAULT NULL",
            "vkprofile" => "varchar(255) DEFAULT NULL",
            "looktype" => "enum('slavonic', 'east', 'semitic', 'caucasian', 'asian', 'african', 'baltic', 'medit', 'european') DEFAULT NULL",
            "haircolor" => "enum('brunet', 'fair', 'blond', 'ginger', 'brown', 'ashen', 'hoar', 'hairless', 'darkfair') DEFAULT NULL",
            "eyecolor" => "enum('gray', 'azure', 'blue', 'green', 'hazel', 'yellow', 'grayazure', 'graygreen', 'hazelgreen') DEFAULT NULL",
            "physiquetype" => "enum('slim', 'model', 'normal', 'sport', 'dense', 'verydense') DEFAULT NULL",
            "isactor" => "tinyint(1) DEFAULT NULL",
            "hasfilms" => "tinyint(1) DEFAULT NULL",
            "isemcee" => "tinyint(1) DEFAULT NULL",
            "isparodist" => "tinyint(1) DEFAULT NULL",
            "istwin" => "tinyint(1) DEFAULT NULL",
            "ismodel" => "tinyint(1) DEFAULT NULL",
            "titsize" => "int(6) UNSIGNED DEFAULT NULL",
            "chestsize" => "int(6) UNSIGNED DEFAULT NULL",
            "waistsize" => "int(6) UNSIGNED DEFAULT NULL",
            "hipsize" => "int(6) UNSIGNED DEFAULT NULL",
            "isdancer" => "tinyint(1) DEFAULT NULL",
            "dancerlevel" => "enum('amateur', 'professional') DEFAULT NULL",
            "hasawards" => "tinyint(1) DEFAULT NULL",
            "isstripper" => "tinyint(1) DEFAULT NULL",
            "striptype" => "enum('topless', 'full') DEFAULT NULL",
            "striplevel" => "enum('amateur', 'professional') DEFAULT NULL",
            "issinger" => "tinyint(1) DEFAULT NULL",
            "singlevel" => "enum('amateur', 'professional') DEFAULT NULL",
            "voicetimbre" => "varchar(32) DEFAULT NULL",
            "ismusician" => "tinyint(1) DEFAULT NULL",
            "musicianlevel" => "enum('amateur', 'professional') DEFAULT NULL",
            "issportsman" => "tinyint(1) DEFAULT NULL",
            "isextremal" => "tinyint(1) DEFAULT NULL",
            "isathlete" => "tinyint(1) DEFAULT NULL",
            "hasskills" => "tinyint(1) DEFAULT NULL",
            "hastricks" => "tinyint(1) DEFAULT NULL",
            "haslanuages" => "tinyint(1) DEFAULT NULL",
            "wantsbusinesstrips" => "tinyint(1) DEFAULT NULL",
            "country" => "varchar(255) DEFAULT NULL",
            "countryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "hasinshurancecard" => "tinyint(1) DEFAULT NULL",
            "inshurancecardnum" => "varchar(128) DEFAULT NULL",
            // загранпаспорт
            "hasforeignpassport" => "tinyint(1) DEFAULT NULL",
            "passportexpires" => "int(11) UNSIGNED DEFAULT NULL",
            // Обычный паспорт
            "passportserial" => "int(10) UNSIGNED DEFAULT NULL",
            "passportnum" => "int(10) UNSIGNED DEFAULT NULL",
            "passportdate" => "int(11) UNSIGNED DEFAULT NULL",
            "passportorg" => "varchar(255) DEFAULT NULL",
            // адрес
            "addressid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "policyagreed" => "tinyint(1) NOT NULL DEFAULT 0",
            "status" => "varchar(12) NOT NULL DEFAULT ''",
            "encrypted" => "tinyint(1) NOT NULL DEFAULT 0",
        );
        
        $this->createTable(Yii::app()->getModule('questionary')->questionaryTable, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        $fieldNames = array_keys($fields);
        
        // перечисляем поля, которые мы не будем индексировать
        // (по ним не планируется производить сортировку и поиск)
        $noIndex = array('inshurancecardnum', 'passportserial', 'passportnum', 'passportdate', 'passportorg');
        
        // оставляем только те поля, которые будем индексировать
        $indexedFields = array_diff($fieldNames, $noIndex);
        
        
        foreach ( $indexedFields as $field )
        {
            $this->createIndex('idx_'.$field, Yii::app()->getModule('questionary')->questionaryTable, $field);
        }
        
        $fields = array(
            "id" => "pk",
            "root" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "lft" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "rgt" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "level" => "int(5) UNSIGNED NOT NULL DEFAULT 0",
            // Имя поля в форме, к которому принадлежит значение
            "fieldname" => "varchar(255) NOT NULL DEFAULT ''",
            // id анкеты к которой принадлежит значение
            "questionaryid" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            // тип сложного поля (только для корневых записей)
            "type" => "varchar(16) DEFAULT NULL",
            // Название значения (или имя поля сложного объекта)
            "name" => "varchar(255) DEFAULT NULL",
            // Само значение (значения поля сложного объекта)
            "value" => "varchar(255) DEFAULT NULL",
            // время создания и изменения (для индексации)
            "timecreated" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            "timemodified" => "int(11) UNSIGNED NOT NULL DEFAULT 0",
            // является ли это значение корневым объектом, т. е. указателем на другие значения
            "isobject" => "tinyint(1) NOT NULL DEFAULT 0",
            );
        
        // {dbprefix}'questionary_complex_values' by default
        $this->createTable(Yii::app()->getModule('questionary')->questionaryValuesTable, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        $indexedFields = array_keys($fields);
        
        foreach ( $indexedFields as $field )
        {
            $this->createIndex('idx_'.$field, Yii::app()->getModule('questionary')->questionaryValuesTable, $field);
        }
    }
}