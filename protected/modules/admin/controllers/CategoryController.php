<?php

/**
 * Контроллер для отображения списка категорий
 */
class CategoryController extends Controller
{
    public $layout = '//layouts/column2';
    
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
     * @return void
     */
    public function actionIndex()
    {
        $id = Yii::app()->request->getParam('parentId', 1);
        $this->render('index', array(
            'parentId' => $id,
        ));
    }
}