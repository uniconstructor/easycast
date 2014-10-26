<?php

/**
 * Действие контроллера для создания/добавления значения настройки
 * 
 * @todo проверки при добавлении значений
 * @todo запретить выполнение insertAfter вне своих списков 
 */
class EcCreateConfigValue extends EcUpdateConfigValue
{
    /**
     * @var bool whether to throw an exception if we cannot find a model requested by the id
     */
    public $exceptionOnNullModel = false;
    /**
     * @var bool 
     */
    public $exceptionOnExistingValue = true;
    /**
     * @var string
     */
    public $modelName = 'EasyListItem';
    
    /**
     * @see EcUpdateAction::run()
     */
    public function run()
    {
        if ( ! Yii::app()->getRequest()->isPostRequest )
        {
            throw new CHttpException(Yii::t('zii', 'Invalid request'));
        }
        // id настройки
        $id      = Yii::app()->request->getParam('id');
        // загружаем модель настройки
        $config  = $this->loadConfigModel($id);
        // для избежания конфликта проверяем существует ли ранее созданное занчение в этой настройке 
        $currentValue = $config->getValueObject();
        
        if ( $config->isSingle() )
        {// настройка с одним значением
            if ( $currentValue )
            {// в настройке уже есть созданное значение
                if ( $exceptionOnExistingValue )
                {// не продолжаем обновление
                    throw new CHttpException('Для этой настройки уже создано значение');
                }else
                {// в настройках указано игнорировать старое значение - обновим текущую запись значения
                    $this->modelName = get_class($currentValue);
                    $model = $currentValue;
                }
            }else
            {// в настройке нет значения - создаем его используя стандартный класс модели
                $model = new $this->modelName;
            }
        }else
        {// настройка содержит список значений 
            if ( $currentValue )
            {// список для настройки уже создан - значит создаем элементы в нем,
                // используя модель заданную в списке по умолчанию в качестве содержимого 
                $this->modelName = $currentValue->itemtype;
                $model = new $this->modelName;
            }else
            {// список для настройки - настройки с моножественным выбором могут ссылаться
                // только на значения класса "список" (EasyList) - поэтому создаем его
                $this->modelName = 'EasyList';
                $model = new $this->modelName;
            }
        }
        if ( ! $modelData = Yii::app()->request->getPost($this->modelName) )
        {// модель данных мы определили, но из формы ничего подобного не пришло
            throw new CHttpException(Yii::t('zii', 'Invalid request'));
        }
        
        // переносим данные из формы в модель
        $model->attributes = $modelData;
        // проверяем значения формы по AJAX
        $this->performAjaxValidation($model);
        
        if ( $afterId = Yii::app()->request->getPost('afterId', 0) )
        {// нужно вставить элемент строго после указанного (только для моделей поддерживающих
            // собственный порядок сортировки по отдельному полю)
            $success = $model->insertAfter($afterId);
        }else
        {// простое сохранение записи
            $success = $model->save();
        }
        
        // выполняем оставшиеся операции
        if ( Yii::app()->getRequest()->isAjaxRequest )
        {
            echo $success ? $this->ajaxResponseOnSuccess : $this->ajaxResponseOnFailed;
            exit(0);
        }
        if ( $this->redirectRoute !== null )
        {
            $this->getController()->redirect($this->redirectRoute);
        }
    }
}