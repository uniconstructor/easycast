<?php

/**
 * Этот хак необходим для корректной работы всех созданных пользователем AR-записей
 * унаследованных от CustomActiveRecord
 * Доступа на запись на production-сервере у нас нет, сервер в облаке, поэтому
 * создавать файлы с кодом новых AR-классов нельзя - они пропадут при перезагрузке
 * 
 * class_alias() не дает возможности, находясь в созданном объекте, определить какой именно 
 * alias-класс был вызван для создания объекта - поэтому мы не можем узнать для какой модели
 * подгружать метаданные
 * 
 * Подробнее здесь: 
 * @see http://stackoverflow.com/questions/9229605/in-php-how-do-you-get-the-called-aliased-class-when-using-class-alias
 * 
 * @param  string $arClass
 * @return null
 * 
 * @todo более четкая проверка имени класса
 */
//function customActiveRecordAutoloadHack($arClass)
//{
    // if ( ! Yii::app()->getComponent('carma')->carmaArExists($arClass) )
    //{// подключаем класс только в том случае если он есть в нашем списке моделей
    //    return false;
    //}
    //eval("class {$arClass} extends CustomActiveRecord {}");
//}

/**
 * Custom
 * Active
 * Record
 * Metadata
 * Assistant
 * 
 * Модуль для ручного управления структурой матаданных для AR-моделей
 * 
 * Позволяет изменять структуру базы данных без применения миграций
 * Позволяет создавать новые AR-модели с произвольной структурой, связи между моделями, 
 * индексы для таблиц в базе, пояснения и подсказки для полей
 */
class Carma extends CApplicationComponent
{
    /**
     * @var string
     */
    public $arTablePrefix = 'ar_';
    /**
     * @var string
     */
    //public $mdTablePrefix = 'md_';
    /**
     * @var string
     */
    public $arModelsTable = 'models';

    /**
     * @see parent::init()
     */
    public function init()
    {
        // подключаем базовый класс для всех ar-моделей
        Yii::import('application.components.carma.models.CustomActiveRecord');
        // подключаем все виджеты для работы с AR-моделями
        Yii::import('application.components.carma.widgets.*');
        // подключаем все созданные пользователем ar-модели
        $models = Yii::app()->db->createCommand()->select()->
            from('{{'.$this->arTablePrefix.$this->arModelsTable.'}}')->queryAll();
        foreach ( $models as $model )
        {
            if ( class_exists($model) )
            {// класс уже подключен
                continue;
            }
            // да, по-другому никак:
            // @see http://stackoverflow.com/questions/9229605/in-php-how-do-you-get-the-called-aliased-class-when-using-class-alias
            eval("class {$model['model']} extends CustomActiveRecord {}");
        }
        parent::init();
    }
    
    /**
     * 
     * @param  string $arClass - класс наследуемый от CustomActiveRecord
     * @return bool
     */
    /*public function carmaArExists($arClass)
    {
        $arData = $this->getDbConnection()->createCommand()->select()->
            from('{{ar_models}}')->where('model = :model', array(':model' => $arClass))->queryRow();
        if ( $arData )
        {
            return true;
        }
        return false;
    }*/
}