<?php

// подключаем базовый класс для всех ar-моделей
Yii::import('application.components.carma.models.*');

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
 * 
 * @todo getArModelsTable() - автоматически добавляет префикс к имени таблицы
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
    public $arModelsTable = 'models';
    /**
     * @var string
     */
    public $dbMessageSourceName = 'dbMessages';

    /**
     * @see parent::init()
     * 
     * @todo не извлекать модели с system=1
     * @todo более четкая проверка имени класса
     */
    public function init()
    {
        // подключаем все созданные пользователем ar-модели
        $models = Yii::app()->db->createCommand()->select()->
            from('{{'.$this->arTablePrefix.$this->arModelsTable.'}}')->
            where('`system` <> 1')->queryAll();
        foreach ( $models as $model )
        {
            if ( /*$model['system'] OR*/ class_exists($model['model'], true) )
            {// класс модели является системным (уже подключен автоматически)
                continue;
            }
            /**
             * да, здесь eval, 
             * да, по-другому никак (совсем-совсем), 
             * да, class_alias() не поможет и вот почему:
             * 
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
             */
            eval("class {$model['model']} extends CustomActiveRecord {}");
        }
        // подключаем все виджеты для работы с AR-моделями
        Yii::import('application.components.carma.widgets.*');
        // регистрация всех обработчиков событий
        parent::init();
    }
    
    /**
     * 
     * @param ArEvent $event
     */
    /*public function loadArEventLaunchers($event, )
    {
        
    }*/
    
    /**
     * 
     * @param ArEvent $event
     */
    public function loadArEventListeners($event=null)
    {
        
    }
    
    /**
     * 
     * @param ArEvent $event
     */
    public function loadArEventHandlers($event=null)
    {
        
    }
    
    /**
     * Зарегистрировать всех инициаторов и обработчиков событий AR-моделей
     * Вызывается при инициализации модуля
     * 
     * @return void
     */
    protected function initEventSystem()
    {
        
    }
}

class ArModel extends CustomActiveRecord {}
class ArRelation extends CustomActiveRecord {}
class ArRule extends CustomActiveRecord {}
class ArTemplate extends CustomActiveRecord {}
class ArWidget extends CustomActiveRecord {}
class ArPointer extends CustomActiveRecord {}
class ArAttribute extends CustomActiveRecord {}
class ArModelAttribute extends CustomActiveRecord {}
class ArAttributeValue extends CustomActiveRecord {}
class ArMetaLink extends CustomActiveRecord {}
class ArValueJson extends CustomActiveRecord {}
class ArValueInt extends CustomActiveRecord {}
class ArValueString extends CustomActiveRecord {}
class ArValueText extends CustomActiveRecord {}
class ArValueBoolean extends CustomActiveRecord {}
class ArValueFloat extends CustomActiveRecord {}
class ArForm extends CustomActiveRecord {}
class ArFormField extends CustomActiveRecord {}
class ArEvent extends CustomActiveRecord {}
class ArEventListener extends CustomActiveRecord {}
class ArEventLauncher extends CustomActiveRecord {}
class ArEntity extends CustomActiveRecord {}
class ArI18nMessage extends CustomActiveRecord {}
class ArI18nTranslation extends CustomActiveRecord {}