<?php

/**
 * Контроллер для работы с каталогом актеров
 * 
 * @todo проставить права доступа
 * @todo вынести получение результатов AJAX-поиска в отдельный виджет
 * @todo перенести активный статус анкеты в условия раздела
 */
class CatalogController extends Controller
{
    /**
     * (non-PHPdoc)
     * @see CController::init()
     */
    public function init()
    {
        // Импортируем классы для работы с анкетами пользователей
        Yii::import('application.modules.questionary.QuestionaryModule');
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.questionary.models.complexValues.*');
        Yii::import('application.modules.catalog.models.*');
        
        parent::init();
    }
    
    /**
     * Отобразить главную страницу - список актеров по категориям
     */
	public function actionIndex()
	{
	    // Получаем раздел каталога, который надо просмотреть (если указано)
	    $sectionid = Yii::app()->request->getParam('sectionid', 1);
	    // получаем вкладку в разделе
	    $tab       = Yii::app()->request->getParam('tab', 0);
	    // получаем страницу во вкладке
	    $page      = Yii::app()->request->getParam('Questionary_page', 0);
	    
	    // Запоминаем всю навигацию в сессии
	    CatalogModule::setNavigationParam('sectionId', $sectionid);
	    CatalogModule::setNavigationParam('tab', $tab);
	    CatalogModule::setNavigationParam('page', $page);
	    
	    // Получаем дополнительные данные для поиска (если пользователь захотел свой поиск)
	    $search = Yii::app()->request->getPost('search');
	    
	    $criteria = Yii::app()->getModule('catalog')->getCatalogCriteria();
	    $dataProvider = new CActiveDataProvider('Questionary', 
	        array(
	            'criteria' => $criteria, 
	            'pagination'=>array(
                    'pageSize'=>CatalogModule::PAGE_ITEMS_COUNT,
                    ),
	            )
	    );
	    
		$this->render('/catalog/index', 
		    array(
		        'dataProvider' => $dataProvider,
		        'sectionid'    => $sectionid,
		        'tab'          => $tab,
	        )
		);
	}
	
	/**
	 * Отобразить страницу "мой выбор"
	 * 
	 * @return null
	 */
	public function actionMyChoice()
	{
	    $message = '';
	    $questionaries = array();
	    if ( ! $this->canPlaceOrders() )
	    {// у пользователя нет права создавать заказы - он вообще не должен видеть ссылку
	        // на эту страницу. Перенаправим его на главную
	        $this->redirect(Yii::app()->baseUrl);
	        Yii::app()->end();
	    }
	    if ( FastOrder::orderIsEmpty() )
	    {// В заказе пока нет ни одного участника - отобразим сообщение
	        if ( ! Yii::app()->user->hasFlash('success') )
	        {
	            $catalogUrl  = Yii::app()->createUrl('//catalog');
	            $catalogLink = CHtml::link(CatalogModule::t('catalog'), $catalogUrl);
	            Yii::app()->user->setFlash('info',
	                CatalogModule::t('no_users_in_order_message', array('{catalog_link}' => $catalogLink)));
	        }
	    }else
	    {// В заказе есть анкеты - соберем по ним все данные
	        foreach ( FastOrder::getPendingOrderUsers() as $id )
	        {
	            $questionaries[$id] = Questionary::model()->findByPk($id);
	        }
	    }
	    
	    // Создадим новый заказ
	    $order = new FastOrder;
	    
	    $this->render('/catalog/myChoice',
    	    array(
    	        'questionaries' => $questionaries,
    	        'order'         => $order,
    	    )
	    );
	    
	}
	
	/**
	 * Отобразить страницу с большой формой поиска
	 */
	public function actionSearch()
	{
	    Yii::import('application.modules.catalog.extensions.search.SearchFilters.SearchFilters');
	    
	    $this->render('search');
	}
	
	/**
	 * Получить список анкет через AJAX-запрос
	 * Используется в разделах каталога и в большой форме поиска
	 * Если анкет нет, или критерии поиска не заданы - в ответ приходит сообщение
	 * Параметры:
	 *     $type (filter/search) - тип запроса (фильтр каталога или большая форма поиска)
	 *     $sectionId (int) - id раздела каталога (если поиск происходит внутри раздела)
	 *     $searchData (JSON) - данные из формы поиска
	 * 
	 * @return string - html-код виджета с участниками 
	 * 
	 * @todo подключить к большой форме поиска
	 */
	public function actionAjaxSearch()
	{
	    /*if ( ! Yii::app()->request->isAjaxRequest )
	    {
	        throw new CHttpException(500, 'This is AJAX action');
	        Yii::app()->end();
	    }*/
	    // Проверяем наличие всех обязательных параметров
	    // режим поиска (по фильтрам в разделе или по всей базе)
	    $mode      = Yii::app()->request->getParam('mode', 'form');
	    $sectionId = Yii::app()->request->getParam('sectionId', 0);
	    
	    if ( $mode == 'filter' AND ! $section = CatalogSection::model()->findByPk($sectionId) )
	    {// попытка поискать в несуществующем разделе
	        throw new CHttpException(500, 'Section not found');
	    }
	    // при первой загрузке страницы попробуем получить данные поиска из сессии
	    if ( $sectionId > 1 )
	    {
	        $data = CatalogModule::getSessionSearchData('filter', $sectionId);
	    }else
	    {
	        $data = CatalogModule::getSessionSearchData('form');
	    }
	    if ( $formData = Yii::app()->request->getPost('data', null) )
	    {// переданы данные для поиска - делаем из них нормальный массив
	        $data = CJSON::decode($formData);
	    }
	    
	    $options = array(
	        'mode'    => $mode,
	        'data'    => $data,
	    );
	    if ( $mode == 'filter' )
	    {
	        $options['section'] = $section;
	    }
	    
	    $this->widget('catalog.extensions.search.QSearchResults.QSearchResults', $options);
	}
	
	/**
	 * Отобразить результат поиска по фильтрам
	 * @param CatalogSection $section
	 * @param array $data - данные из формы поиска
	 * @return null
	 */
	protected function displayFilterSearch($section, $data)
	{
	    
	}
	
	/**
	 * Отобразить результат поиска по большой форме
	 * @param array $data - данные из формы поиска
	 * @return null
	 */
	protected function displayFormSearch($data)
	{
	     
	}
	
	/**
	 * Запомнить в сессию номер страницы каталога, на которую перешел пользователь
	 */
	public function actionSetNavigationParam()
	{
	    if ( $sectionId = Yii::app()->request->getParam('sectionId') )
	    {
	        CatalogModule::setNavigationParam('sectionId', $sectionId);
	    }
	    if ( $tab = Yii::app()->request->getParam('tab') )
	    {
	        CatalogModule::setNavigationParam('tab', $tab);
	        if ( $section = CatalogSection::model()->findByPk($sectionId) )
	        {
	            if ( $section->shortname == $tab )
	            {
	                CatalogModule::setNavigationParam('tab', 0);
	            }
	        }
	    }
	    if ( $page = Yii::app()->request->getParam('page') )
	    {
	        CatalogModule::setNavigationParam('page', $page);
	    }
	    
	    echo 'OK';
	}
	
	/**
	 * Получить код формы с фильтрами поиска через AJAX-запрос
	 * 
	 * @return null
	 * @todo при AJAX-загрузке на работают скрипты - пока не используется
	 */
	public function actionGetSearchFiltersForm()
	{die('NOT USED');
	    $sectionId = Yii::app()->request->getPost('sectionId', 0);
	    if ( ! $section = CatalogSection::model()->findByPk($sectionId) )
	    {
	        throw new CHttpException(500, 'sectionId required');
	    }
	    if ( ! Yii::app()->request->isAjaxRequest )
	    {
	        Yii::app()->end();
	    }
	    $this->widget('catalog.extensions.search.SearchFilters.SearchFilters', array(
            'section' => $section,
        ));
	    Yii::app()->end();
	}
	
	/**
	 * Очистить сохраненные в сессии данные формы поиска
	 * (Может очистить как данные всей формы поиска так и в отдельном разделе.
	 * Как все поля - так и отдельное поле, все зависит от настроек)
	 * 
	 * @return null
	 * @todo обработать возможные ошибки
	 * @todo сделать возможность очистить всю форму одним запросом
	 */
	public function actionClearSessionSearchData()
	{
	    // если название фильтра не задано - мы можем очистить только 
	    if ( ! $namePrefix = Yii::app()->request->getPost('namePrefix', '') )
	    {
	        throw new CHttpException(500, 'namePrefix required');
	    }
	    if ( ! Yii::app()->request->isAjaxRequest )
	    {
	        Yii::app()->end();
	    }
	    // определяем, что нужно очистить: данные каталога или большую форму 
	    $sectionId = Yii::app()->request->getPost('sectionId', 0);
	    if ( $section = CatalogSection::model()->findByPk($sectionId) )
	    {// очищаем данные внутри раздела каталога
	        CatalogModule::clearFilterSearchData($namePrefix, $sectionId, array());
	    }else
        {// очищаем данные большой формы поиска
	        CatalogModule::clearFormSearchData($namePrefix, array());
	    }
	    
	    echo 'OK';
	}
	
	/**
	 * Определить, может ли пользователь делать заказы
	 * (заказы может делать гость, админ или заказчик, но не участник)
	 * @return bool
	 */
	protected function canPlaceOrders()
	{
	    if ( Yii::app()->user->isGuest )
	    {
	        return true;
	    }
	    
	    if ( Yii::app()->user->checkAccess('Customer') OR Yii::app()->user->checkAccess('Admin') )
	    {
	        return true;
	    }
	    
	    return false;
	}
}