<?php

/**
 * Событие передаваемое при изменении данных модели
 * Является контейнером для данных, не выполняет никаких действий
 */
class ArDataEvent extends CModelEvent
{
    /**
     * @var ArModel - метаданные изменяемой модели
     */
    public $arModel;
    /**
     * @var array - данные модели до изменения (содержит null если запись была только что создана)
     */
    public $oldData;
    /**
     * @var array - данные модели после изменения (содержит null если запись была только что удалена)
     */
    public $newData;
    /**
     * @var string - категория или область задач к которой относится действие
     *               data - изменение данных таблицы
     *               meta - изменение метаданных и структуры таблицы
     *               lang - изменение строк перевода
     */
    public $actionScope = 'data';
    /**
     * @var string - выполняемое действие (create/update/delete)
     */
    public $actionType;
    /**
     * @var string - когда произошло событие: до действия (before) или после действия (after)
     */
    public $actionCase;
    /**
     * @var string - полное название отправленного события
     */
    public $eventName;
    
    /**
     * @var Carma - ссылка на модуль
     */
    protected $carma;
    
    /**
     * @see parent::__construct()
     */
    public function __construct($sender, $params)
    {
        $this->params = array();
        $this->params['arModel']    = null;
        $this->params['oldData']    = null;
        $this->params['newData']    = null;
        $this->params['actionType'] = null;
        $this->params['actionCase'] = null;
        $this->params['eventName']  = null;
        // задаем значения по умолчанию
        parent::__construct($sender, CMap::mergeArray($this->params, $params));
        // получаем ссылку на компонент
        $this->carma &= Yii::app()->getComponent('carma');
        // проверка данных события
        if ( ! $this->params['oldData'] AND ! $this->params['newData'] )
        {
            throw new CException('Model data not found in ArDataEvent');
        }
        $this->setArModel($this->params['arModel']);
        $this->setActionType($this->params['actionType']);
    }
    
    /**
     * @param string $type - выполняемое действие (create/update/delete)
     */
    public function setActionType($type)
    {
        if ( ! in_array($type, array('create', 'update', 'delete')) )
        {
            throw new CException('Incorrect action: '.$type);
        }
        $this->params['actionType'] = $type;
    }
    
    /**
     * @param string $type - выполняемое действие (create/update/delete)
     */
    public function setActionCase($case)
    {
        if ( ! in_array($case, array('before', 'after')) )
        {
            throw new CException('Incorrect action case: '.$case);
        }
        $this->params['actionCase'] = $case;
    }
    
    /**
     * @param ArModel $arModel
     */
    public function setArModel(ArModel $arModel)
    {
        $this->params['arModel'] = $arModel;
    }
    
    /**
     * @param ArModel $name
     */
    public function setEventName($name)
    {
        $this->params['eventName'] = $name;
    }
}