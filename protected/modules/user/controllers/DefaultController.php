<?php

class DefaultController extends Controller
{
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