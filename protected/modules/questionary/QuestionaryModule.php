<?php
/** 
 * Модуль для работы с анкетой участника
 * Позволяет искать, сохранять, просматривать и редактировать анкету, включая все сложные поля
 * 
 * @author frost
 */
class QuestionaryModule extends CWebModule
{
    /**
     * Какие значения использовать в форме, в списках значений по умолчанию?
     * @todo перенести в настройки сайта
     * @var bool 
     *         true - использовать только системные (одобренные администратором) значения
     *         false - использовать все значения, введенные пользователями (не рекомендуется)
     */
    const SYSTEM_DEFAULTS_ONLY = false;
    /**
     * @property string the name of the user model class.
     */
    public $userClass = 'User';
    /**
     * @property string the name of the id column in the user table.
     */
    public $userIdColumn = 'id';
    /**
     * @property string the name of the address model class.
     */
    public $addressClass = 'Address';
    /**
     * @property string the name of the id column in the user table.
     */
    public $addressIdColumn = 'id';
    /**
     * @property string the name of the username column in the user table.
     */
    public $userNameColumn = 'username';
    /**
     * @property string the base url to questionary. Override when module is nested.
     */
    public $baseUrl = '/questionary';
    /**
     * @var string ссылка на страницу пользователя (раздел "моя страница")
     */
    public $profileUrl = '/questionary/questionary/view';
    /**
     * @property string the path to the layout file to use for displaying questionary.
     */
    //public $layout = 'application.modules.questionary.views.layouts.main';
    /**
     * @property string the path to the application layout file.
     */
    //public $appLayout = 'application.views.layouts.main';
    /**
     * @property string 
     */
    public $questionaryTable = '{{questionaries}}';
    /**
     * @property string
     */
    public $questionaryValuesTable = '{{questionary_complex_values}}';
    /**
     * @var array настройки галереи изображений анкеты пользователя
     */
    public $gallerySettings = array(
        'class' => 'GalleryBehavior',
        'idAttribute' => 'galleryid',
        // максимальное количество картинок участника - 30
        'limit' => 30,
        // картинка масштабируется в трех размерах: аватар,
        // обычное изображение (для списка анкет), большое изображение (посмотреть подробно)
        'versions' => array(
            'small' => array(
                'centeredpreview' => array(100, 100),
            ),
            'medium' => array(
                'resize' => array(530, 330),
            ),
            'large' => array(
                'resize' => array(800, 1000),
            ),
            'catalog' => array(
                'centeredpreview' => array(150, 150),
            ),
        ),
        'name'        => true,
        'description' => true,
    );
    
    /**
     * @var QuestionaryController
     */
    public $defaultController = 'questionary';
    
    public $_assetsUrl;
    
    /**
     * Initializes the "questionary" module.
     */
    public function init()
    {
        // Set required classes for import.
        $this->setImport(array(
            //'questionary.components.*',
            //'questionary.components.behaviors.*',
            //'questionary.components.dataproviders.*',
            'questionary.controllers.*',
            'questionary.models.*',
            'questionary.models.complexValues.*',
            'questionary.extensions.*',
            // каталог (тесно связан с анкетой, поэтому импортируем и его)
            'application.modules.catalog.CatalogModule',
            // галерея изображений
            'ext.galleryManager.*',
            'ext.galleryManager.models.*',
        ));
        
        if ( method_exists(Yii::app(), 'getAssetManager') )
        {
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::app()->modulePath . DIRECTORY_SEPARATOR .
                'questionary'. DIRECTORY_SEPARATOR .'assets');
        }
        // Normally the default controller is "questionary".
        $this->defaultController = 'questionary';
    }
    
    /**
     * Registers the necessary scripts.
     */
    public function registerScripts()
    {
        // Get the url to the module assets
        $assetsUrl = $this->getAssetsUrl();
    
        // Register the necessary scripts
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $cs->registerCoreScript('jquery.ui');
    
        // Make sure we want to register a style sheet.
        if( $this->cssFile!==false )
        {
            // Default style sheet is used unless one is provided.
            if( $this->cssFile===null )
                $this->cssFile = $assetsUrl.'/css/default.css';
            else
                $this->cssFile = Yii::app()->request->baseUrl.$this->cssFile;
    
            // Register the style sheet
            $cs->registerCssFile($this->cssFile);
        }
    }
    
    /**
     * Publishes the module assets path.
     * @return string the base URL that contains all published asset files of questionary.
     */
    public function getAssetsUrl()
    {
        if( $this->_assetsUrl===null )
        {
            $assetsPath = Yii::getPathOfAlias('questionary.assets');
    
            // We need to republish the assets if debug mode is enabled.
            if( $this->debug===true )
                $this->_assetsUrl = Yii::app()->getAssetManager()->publish($assetsPath, false, -1, true);
            else
                $this->_assetsUrl = Yii::app()->getAssetManager()->publish($assetsPath);
        }
    
        return $this->_assetsUrl;
    }
    
    /**
     * @param $str
     * @param $params
     * @param $dic
     * @return string
     */
    public static function t($str='', $params=array(), $dic='questionary') {
        if (Yii::t("QuestionaryModule", $str)==$str)
        {
            return Yii::t("QuestionaryModule.".$dic, $str, $params);
        }else
        {
           return Yii::t("QuestionaryModule", $str, $params);
        }
    }
    
    /**
     * Переопределяем viewPath чтобы можно было нормально просматривать и редактировать анкету
     * (non-PHPdoc)
     * @see CController::getViewPath()
     * @todo удалить, не пригодилась
     */
    public function getViewPath()
    {
        return Yii::getPathOfAlias('application.modules.questionary.views.questionary');
    }
    
    /**
     * (non-PHPdoc)
     * @see CWebModule::beforeControllerAction()
     */
    public function beforeControllerAction($controller, $action)
    {
        if ( $controller == 'gallery' AND in_array($action,array('delete','ajaxUpload','order','changeData','setCoverId')) )
        {
            if ( Yii::app()->user->isGuest )
            {
                return false;
            }
            if ( Yii::app()->user->checkAccess('Admin') )
            {
                return true;
            }
            return true;
        }
        return parent::beforeControllerAction($controller,$action);
    }
}