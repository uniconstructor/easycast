<?php

/**
 * Контроллер, составляющий все письма сайта
 */
class MailController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = 'application.modules.mailComposer.views.layouts.mail';
    
    /**
     * (non-PHPdoc)
     * @see CController::init()
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }
    
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     *
     * @todo настроить проверку прав на основе ролей
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('createSimpleMail',),
                'users'   => array('@'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * Создать самое простое письмо: заголовок, подзаголовок, абзац текста, 
     * стандартная подпись с контактами, отписка по желанию. Все настраивается.
     * 
     * @param array $params - массив параметров для составления письма
     * @return string - html-код письма
     */
    public function actionCreateSimpleMail($params)
    {
        $defaults = $this->getSimpleMailDefaults();
        $options = CMap::mergeArray($defaults, $params);
        
        // создаем виджет-сборщик письма
        
        // устанавливаем в него значения по умолчанию
        
        // получаем от виджета код письма
        
    }
    
    /**
     * Получить настройки по умолчанию для составления простого письма
     * @return array
     */
    protected function getSimpleMailDefaults()
    {
        return array(
            // самый большой заголовок письма
            'heading' => '',
            // заголовок первого абзаца
            'subject' => '',
            // первый абзац текста (только текст, html и самая простая разметка)
            'text'    => '',
            // настройки для виджета, собирающего письмо из блоков
            'assemblerOptions' => array(),
        );
    }
}