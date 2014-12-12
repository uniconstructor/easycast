<?php

/**
 * 
 */
class DefaultController extends Controller
{
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
	 * Lists all models.
	 */
	public function actionIndex()
	{
	    if ( ! Yii::app()->user->checkAccess('Admin') )
	    {
	        $this->redirect(Yii::app()->createAbsoluteUrl(current(Yii::app()->getModule('user')->loginUrl)));
	    }
		$dataProvider=new CActiveDataProvider('User', array(
			'criteria'=>array(
		        'condition'=>'status>'.User::STATUS_BANNED,
		    ),
			'pagination'=>array(
				'pageSize'=>Yii::app()->controller->module->user_page_size,
			),
		));

		$this->render('/user/index',array(
			'dataProvider'=>$dataProvider,
		));
	}
}