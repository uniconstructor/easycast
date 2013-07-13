<?php

/**
 * Контроллер фотогалереи
 */
class PhotosController extends Controller
{
    public $layout='//layouts/column1';
    
    /**
     * отобразить главную страницу галереи
     * @return null
     */
	public function actionIndex()
	{
	    $galleryName = '';
	    // id отображаемой галереи (если отображается одна галерея)
	    if ( $galleryid = Yii::app()->request->getParam('galleryid') )
	    {
	        $galleryName = PhotoGallery::model()->findByPk($galleryid)->name;
	    }
	    
	    
		$this->render('index', array(
		    'galleryid'   => $galleryid,
		    'galleryName' => $galleryName,
		));
	}
	
	/**
	 * Отобразить все фотографии одной галереи
	 * @return null
	 */
	public function actionView()
	{
	    $model = $this->loadModel($id);
	    $this->render('view', array(
    	        'model' => $model,
    	    )
	    );
	    $this->render('view');
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
	    $model=PhotoGallery::model()->findByPk($id);
	    if($model===null)
	        throw new CHttpException(404,'The requested page does not exist.');
	    return $model;
	}
}