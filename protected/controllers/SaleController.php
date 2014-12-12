<?php

/**
 * Контроллер для работы с коммерческим предложением и всем что связано с продажами
 * @todo настроить права доступа
 */
class SaleController extends Controller
{
    /**
     * @var string
     */
    public $layout = 'main';
    
    /**
     * @return array
     *
     * @todo настроить проверку прав на основе RBAC
     */
    public function filters()
    {
        $baseFilters = parent::filters();
        $newFilters  = array(
            // фильтр для подключения YiiBooster 3.x (bootstrap 2.x)
            array(
                'ext.bootstrap.filters.BootstrapFilter',
            ),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        parent::init();
        // требуется для вывода разделов каталога и результатов поиска при демонстрации
        Yii::import('application.modules.catalog.models.*');
        
        // для коммерческого предложения используется специальная тема оформления
        Yii::app()->theme = 'staticLanding';
    }
    
    /**
     * Коммерческое предложение для заказчиков
     * @return void
     */
    public function actionIndex()
    {
        // для коммерческого предложения режим просмотра всегда "заказчик"
        Yii::app()->getModule('user')->setViewMode('customer');
        
        $this->render('sale');
    }
}