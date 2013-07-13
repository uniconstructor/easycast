<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// Twitter bootstrap path alias
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    // general application parameters
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    // @todo сделать выбор языка в зависимости от региона
    'language' => 'ru',
    'sourceLanguage' => 'en_us',
    
    // preloading components
    'preload'=>array('log', 'messages'),
    
    // Project name
    'name'=> 'EasyCast',
    
    'aliases' => array(
    ),

    'controllerMap' => array(
        'gallery'=>array(
            'class'=>'ext.galleryManager.GalleryController',
            'pageTitle'=>'Gallery administration',
        ),
        /*'questionary' => array(
            'class' => 'application.modules.questionary.controllers.QuestionaryController',
        ),*/
    ),
    
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	    'application.extensions.*',
        // country and city selector
	    'application.extensions.CountryCitySelectorRu.models.*',
	    // User module
	    'application.modules.user.models.*',
	    'application.modules.user.components.*',
	    // RBAC module
	    'application.modules.rights.*',
	    'application.modules.rights.components.*',
	    // Questionary module
	    'application.modules.questionary.*',
	    'application.modules.questionary.components.*',
	    'application.modules.questionary.models.*',
        // Image gallery manager
        'ext.galleryManager.models.*',
        // Image manipulation library
        //'ext.YiiImage.CImageComponent',
        // Twitter bootstrap
        'application.extensions.bootstrap.widgets.*',
        // yes/no toogle column widget
	    'application.extensions.jtogglecolumn.*',
	),

	'modules'=>array(
        'user'=>array(
            // encrypting method (php hash function)
            'hash' => 'sha1',
            // send activation email
            'sendActivationMail' => true,
            // allow access for non-activated users
            'loginNotActiv' => true,
            // activate user on registration (only sendActivationMail = false)
            'activeAfterRegister' => false,
            // automatically login from registration
            'autoLogin' => true,
            // registration path
            'registrationUrl' => array('/user/registration'),
            // recovery password path
            'recoveryUrl' => array('/user/recovery'),
            // login form path
            'loginUrl' => array('/user/login'),
            // page after login
            'returnUrl' => array('//site/index'),
            // page after logout
            'returnLogoutUrl' => array('//site/index'),
        ),
	    
	    'rights'=>array(
	        // Enables the installer.
	        'install'=>false,
	    ),
	    
	    // анкета пользователя (реализована отдельным модулем)
	    'questionary' => array(
            'controllerMap' => array(
                'gallery'=>array(
                    'class'=>'ext.galleryManager.GalleryController',
                    //'pageTitle'=>'Gallery administration',
                ),
            ),
        ),
	    
        // Форум
        'forum'=>array(
            'class'=>'application.modules.yii-forum.YiiForumModule',
            'forumTableClass' => 'table',
            'forumListviewClass' => 'detail-view table table-striped table-condensed',
            'forumDetailClass' => 'detail-view table table-striped table-condensed',
        ),
        
        // Календарь событий
        'calendar' => array(
            'class' => 'application.modules.calendar.CalendarModule',
        ),
        
        // админка
        'admin' => array(
            'class' => 'application.modules.admin.AdminModule',
        ),
        
        // проекты
        'projects' => array(
            'class' => 'application.modules.projects.ProjectsModule',
        ),
        
        // каталог
        'catalog' => array(
            'class' => 'application.modules.catalog.CatalogModule',
        ),
        
        // фотогалерея
        'photos' => array(
            'class' => 'application.modules.photos.PhotosModule',
        ),
        
        // Новости
        'news' => array(
            'class' => 'application.modules.news.NewsModule',
        ),
        
        // Статьи
        'articles' => array(
            'class' => 'application.modules.articles.ArticlesModule',
        ),
	),

	// application components
	'components'=>array(
		'user'=>array(
		    // enable cookie-based authentication
		    // Allows super users access implicitly.
            'class' => 'RWebUser',
            'allowAutoLogin' => true,
            'loginUrl' => array('/user/login'),
		),
	    
		// enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
		    'showScriptName'=>false
		),
		
		// MySQL database settings
		'db'=>array(
			// Логин и пароль для базы определяются скриптами сборки проекта
			// (в зависимости от того где работает приложение: на реальном сервере или локально)
			// поэтому в основном конфиге эти параметры не указываются
		    // 'connectionString' => 'mysql:host=localhost;dbname=easycast',
			// 'username' => 'root',
			// 'password' => 'root',
		    'tablePrefix' => 'bgl_',
		    'emulatePrepare' => true,
			'charset' => 'utf8',
		),
	    
	    // HTTP request handling
	    'request' => array(
	        'class' => 'CHttpRequest',
	        // prevent XSS
	        'enableCsrfValidation'=>true,
	        // prevent Cookie-based attacks
	        'enableCookieValidation'=>true,
	    ),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
	    
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CDbLogRoute',
					'connectionID' => 'db',
					'levels'=>'error, warning, info, application',
				    'autoCreateLogTable' => true,
				),
			),
		),
	    
	    // Подключаем библиотеку, позволяющую разграничение доступа на основе ролей (RBAC)
	    'authManager' => array(
	        // Provides support authorization item sorting.
	        'class'=>'RDbAuthManager',
	        // Роль по умолчанию. Все, кто не админы, модераторы и юзеры — гости.
	        //'defaultRoles' => array('guest'),
	        // показываем ошибки только в режиме отладки
	        'showErrors' => YII_DEBUG,
	    ),
	    
	    // Подключаем модуль i18n чтобы можно было переводить приложение на разные языки
	    'messages' => array(
	        // языковые строки берутся из файла
	        'class' => 'CPhpMessageSource',
	    ),

        // Twitter bootstrap
        'bootstrap'=>array(
            'class'=>'bootstrap.components.Bootstrap',
        ),

        // image manipulation library
        'image'=>array(
            'class'=>'ext.image.CImageComponent',
        ),
        // настройки виджетов по умолчанию
        'widgetFactory'=>array(
            'widgets'=>array(
                // Выравнивание верстки для форума
                'CGridView'=>array(
                    'itemsCssClass'=>'',
                    'pagerCssClass'=>'',
                ),
                // Формы сложных значений
                'MultiModelForm' => array(
                    // @todo разобраться, почему не работает перевод сообщений в конфиге
                    // 'addItemText'       => Yii::t('coreMessages','add'),
                    // 'removeText'        => Yii::t('coreMessages','delete'),
                    'tableView'         => true,
                    'bootstrapLayout'   => true,
                    // все кнопки "удалить" становятся красными
                    'removeHtmlOptions' => array(
                            'class' => 'btn btn-danger mmf_removelink',
                        ),
                    // все кнопки "добавить" становятся зелеными
                    'addHtmlOptions'    => array(
                            'class' => 'btn btn-success',
                        ),
                    // делаем все формы узкими, чтобы они вписались в верстку
                    'tableHtmlOptions'  => array(
                            'style' => 'width:auto;',
                            'class' => 'table-striped',
                        ),
                ),
                // Выбор даты из календаря
                'CJuiDatePicker' => array(
                    'language' => 'ru',
                    'options'=>array(
                        'showAnim' => 'fold',
                        'dateFormat' => 'dd/mm/yy',
                    ),
                ),
            ),
        ),
        
        // Настройки сессии 
        'session' => array(
            'class' => 'CHttpSession',
            //'connectionID' => 'db',
            //'autoCreateSessionTable' => true,
            'autoStart' => true,
        ),
	),

    // Используем собственную тему оформления для сайта
    'theme'=>'easycast',

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
	    
	),
	
);