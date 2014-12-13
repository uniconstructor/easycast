<?php

/**
 * Админский контроллер для работы с разделами каталога
 * @package easycast
 * @subpackage admin
 * 
 */
class CatalogSectionController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';
	/**
	 * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
	 */
	protected $defaultModelClass = 'CatalogSection';
	
	/**
	 * @see CController::init()
	 */
	public function init()
	{
	    Yii::import('application.modules.catalog.models.*');
	    parent::init();
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		$baseFilters = parent::filters();
        $newFilters  = array(
            'accessControl',
            'ext.bootstrap.filters.BootstrapFilter',
        );
        return CMap::mergeArray($baseFilters, $newFilters);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // все проверки прав производятся в beforeControllerAction
				'actions' => array('index', 'view', 'create', 'update', 'admin', 'delete', 
				    'setSearchData', 'clearSearchData'),
				'users' => array('@'),
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
	    $this->layout = '//layouts/column1';
	    $section = $this->loadModel($id);
	    if ( $ids = Yii::app()->request->getPost('activeFilters') )
	    {// сохраняем список используемых фильтров, если требуется
	        $this->bindSearchFilters($section, $ids);
	    }
	    
		$this->render('view', array(
			'model' => $section,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new CatalogSection;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if( isset($_POST['CatalogSection']) )
		{
			$model->attributes = $_POST['CatalogSection'];
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset($_POST['CatalogSection']) )
		{
			$model->attributes = $_POST['CatalogSection'];
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
			'model' => $model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if ( Yii::app()->request->isPostRequest )
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if ( ! isset($_GET['ajax']) )
			{
			    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		}else
		{
		    throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('CatalogSection');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new CatalogSection('search');
		$model->unsetAttributes();  // clear any default values
		if ( isset($_GET['CatalogSection']) )
		{
		    $model->attributes = $_GET['CatalogSection'];
		}

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	/**
	 * Изменить критерии выборки людей, которые подходят под эту вакансию
	 *
	 * @return null
	 */
	public function actionSetSearchData()
	{
	    /*if ( ! Yii::app()->request->isAjaxRequest )
	    {
	        Yii::app()->end();
	    }*/
	    // id вакансии (обязательно)
	    $id = Yii::app()->request->getParam('id', 0);
	    $section = $this->loadModel($id);
	     
	    // условия, по которым отбираются люди на вакансию
	    if ( $data = Yii::app()->request->getPost('data', null) AND ! empty($data) )
	    {// переданы данные для поиска - делаем из них нормальный массив
	        $data = CJSON::decode($data);
	    }else
	    {// данные для поиска не переданы - завершаем работу
	        echo 'Не переданы данные для поиска';
	        Yii::app()->end();
	    }
	     
	    // Сохраняем данные поисковой формы и пересоздаем критерий поиска
	    $section->setSearchData($data);
	     
	    // считаем сколько участников подошло под выбранные критерии
	    //echo 'Подходящих участников: '.$section->countPotentialApplicants();
	    Yii::app()->end();
	}
	
	/**
	 * Очистить сохраненные в сессии данные формы поиска
	 * (Может очистить как данные всей формы поиска так и в отдельном разделе.
	 * Как все поля - так и отдельное поле, все зависит от настроек)
	 *
	 * @return null
	 * @todo обработать возможные ошибки
	 */
	public function actionClearFilterSearchData()
	{
	    /*if ( ! Yii::app()->request->isAjaxRequest )
	    {
	        Yii::app()->end();
	    }*/
	    // если название фильтра не задано - мы можем очистить только
	    if ( ! $namePrefix = Yii::app()->request->getPost('namePrefix', '') )
	    {
	        throw new CHttpException(500, 'namePrefix required');
	    }
	     
	    $sectionId = Yii::app()->request->getPost('id', 0);
	    if ( $section = EventVacancy::model()->findByPk($sectionId) )
	    {// очищаем данные внутри вакансии
	        $section->clearFilterSearchData($namePrefix);
	    }
	
	    echo 'OK';
	    Yii::app()->end();
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 * @return CatalogSection
	 */
	/*public function loadModel($id)
	{
		$model = CatalogSection::model()->findByPk($id);
		if( $model === null )
		{
		    throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}*/

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'catalog-section-form' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Привязать фильтры поиска к разделу каталога или обновить фильтров
	 * @param CatalogSection $section
	 * @param string $ids - id фильтров поиска
	 * @return void
	 */
	protected function bindSearchFilters($section, $ids)
	{
	    $filters = array();
	    $ids = explode(',', $ids);
	    foreach ( $ids as $id )
	    {
	         $filter = CatalogFilter::model()->findByPk($id);
	         $filters[$id] = $filter;
	    }
	    $section->bindSearchFilters($filters);
	}
}
