<?php

/**
 * Наш API для работы с мегапланом
 */
class EasyCastMegaplanAPI extends CApplicationComponent
{
    /**
     * @var string - имя хоста на котором расположен сервис мегаплана для этой организации
     */
    public $host;
    /**
     * @var string - id доступа к мегаплану
     */
    public $accessId;
    /**
     * @var string - ключ доступа по которому производятся все запросы
     */
    public $secretKey;
    /**
     * @var int - id пользователя от имени которого совершаются действия и которому назначаются задачи
     */
    public $defaultUserId;
    /**
     * @var int - id сотрудника от имени которого совершаются действия и которому назначаются задачи
     */
    public $defaultEmployeeId;
    
    /**
     * @var SdfApi_Request - официальный класс API от Мегаплана
     */
    protected $api;
    
    /**
     * @see CApplicationComponent::init()
     */
    public function init()
    {
        parent::init();
        // подключаем родной API мегаплана
        Yii::import('application.components.megaplan.*');
        // создаем объект с родным API Мегаплана
        $this->api = new SdfApi_Request($this->accessId, $this->secretKey, $this->host, true);
    }
    
    /**
     * Создать и назначить задачу
     * 
     * @param array $params - данные для создания новой задачи
     * @return array - массив с информацией о созданной задаче (Id, Name)
     * 
     * @see http://help.megaplan.ru/API_task_create
     */
    public function createTask($params)
    {
        $uri = '/BumsTaskApiV01/Task/create.api';
        return $this->api->post($uri, $params);
    }
}