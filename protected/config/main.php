<?php

// Twitter bootstrap path alias
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');
// путь к google-библиотеке для обработки телефонных номеров
Yii::setPathOfAlias('libphonenumber', dirname(__FILE__).'/../components/libphonenumber');


// Главный файл конфигурации приложения.
// Здесь задаются все общие параметры, одинаковые для "production"(релиза), "test"(тестового сервера) 
// и "dev"(версии разработчика).
// В разных ветках git-репозитория лежат дополнительные config-файлы для каждой версии сайта.
// Каждая версия сайта собирается отдельным ant-скриптом.
// Последовательность сборки такова: 
//                    Yii
//                    EasyCast (ядро)
//                    Оригинальные плагины
//                    Наши изменения в плагинах
//                    Настройки окружения (для dev, test или production версии)
return array(
    /// general application parameters //
    
    // физический путь к папке "protected"
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    
    // язык приложения
    'language'       => 'ru',
    'sourceLanguage' => 'en_us',
    
    // предварительно загружаемые компоненты
    'preload' => array('log', 'messages', 'bootstrap'),
    
    // Название проекта 
    'name' => 'easyCast',
    
    // Короткие имена для вызова популярных библиотек
    // @todo попробовать перенести сюда bootstrap и посмотреть, ничего ли не сломается
    'aliases' => array(
        
    ),
    
    // сокращенные пути к контроллерам
    /*'controllerMap' => array(
        // загрузка изображений в галерею
        // @todo удалить в целях безопасности
        'gallery' => array(
            'class'     => 'ext.galleryManager.GalleryController',
            'pageTitle' => 'Gallery administration',
        ),
    ),*/
    
	// autoloading model and component classes
	'import' => array(
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
        //'application.extensions.bootstrap.helpers.TbHtml',
        // yes/no toogle column widget
	    'application.extensions.jtogglecolumn.*',
	    // Import simpleWorkflow extension (for statuses)
	    'application.extensions.simpleWorkflow.*',
	    // библиотека для работы с JS, включающая плагин shadowbox
	    'ext.sweekit.Sweeml',
	),

	'modules' => array(
	    // Пользователи
        'user' => array(
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
            'returnUrl' => array('//questionary/questionary/view'),
            //'returnUrl' => array('//site/index'),
            // page after logout
            'returnLogoutUrl' => array('//site/index'),
        ),
	    
        // Права доступа (RBAC)
	    'rights' => array(
	        // установка нам больше не понадобится
	        'install'           => false,
	        // разрешаем использовать переменные в правилах доступа
	        'enableBizRuleData' => true,
	        // нужно установить разметку страницы в соответствии с нашей темой
	        'appLayout' => '//layouts/column1',
	    ),
	    
	    // анкета пользователя (реализована отдельным модулем)
	    'questionary' => array(
            'controllerMap' => array(
                // задаем путь к контроллеру загрузки изображений (для анкеты)
                'gallery' => array(
                    'class' => 'ext.galleryManager.GalleryController',
                    'handlerClass' => 'GmS3Photo',
                    'customBehaviors' => array(
                        'S3GalleryControllerBehavior' => array(
	                        'class' => 'application.extensions.galleryManager.behaviors.S3GalleryControllerBehavior',
	                    ),
	                ),
                ),
            ),
        ),
	    
        // Форум
        'forum' => array(
            'class' => 'application.modules.yii-forum.YiiForumModule',
            'forumTableClass'    => 'table',
            'forumListviewClass' => 'detail-view table table-striped table-condensed',
            'forumDetailClass'   => 'detail-view table table-striped table-condensed',
        ),
        
        // Календарь событий
        'calendar' => array(
            'class' => 'application.modules.calendar.CalendarModule',
        ),
        
        // админка
        'admin' => array(
            'class' => 'application.modules.admin.AdminModule',
            'controllerMap' => array(
                // задаем путь к контроллеру загрузки изображений (для анкеты)
                'gallery' => array(
                    'class' => 'ext.galleryManager.GalleryController',
                    'handlerClass' => 'GmS3Photo',
                ),
            ),
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
        
        // Оповещения
        // @todo привести в порядок или удалить
        'notifyii' => array(
            'class' => 'application.modules.notifyii.NotifyiiModule',
        ),
        
        // Письма (этот модуль отвечает за правильную верстку писем)
        'mailComposer' => array(
            'class' => 'application.modules.mailComposer.MailComposerModule',
        ),
        
        // отчеты
        'reports' => array(
            'class' => 'application.modules.reports.ReportsModule',
        ),
	),

	// Компоненты приложения
	'components' => array(
	    // пользователь (наследник класса WebUser)
		'user' => array(
		    // используем класс пользователя из модуля rights чтобы работали права доступа на основе ролей (RBAC)
            'class'          => 'RWebUser',
            // сразу же пускаем участника на сайт после регистрации, чтобы сэкономить ему время
            'allowAutoLogin' => true,
            // адрес страницы с формой входа
            'loginUrl'       => array('/user/login'),
		),
	    
		// Отображаем все URL в формате /путь/к/странице (на сервере должен быть включен mod_rewrite)
		'urlManager' => array(
			'urlFormat' => 'path',
			'rules'     => array(
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
		    'showScriptName' => false
		),
		
		// MySQL database settings
		'db' => array(
			// Логин и пароль для базы определяются ant-скриптами сборки проекта
			// (в зависимости от того где работает приложение: на реальном сервере или локально)
			// поэтому в основном конфиге эти параметры не указываются
		    'tablePrefix'    => 'bgl_',
		    'emulatePrepare' => true,
			'charset'        => 'utf8',
		),
	    
	    // HTTP request handling
	    'request' => array(
	        'class' => 'CHttpRequest',
	        // prevent XSS
	        'enableCsrfValidation'   => true,
	        // prevent Cookie-based attacks
	        'enableCookieValidation' => true,
	    ),
		
	    // обработка ошибок
		'errorHandler' => array(
			// use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
	    
        // логи
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
			    // храним логи в базе
				array(
					'class'        => 'CDbLogRoute',
					'connectionID' => 'db',
					'levels'       => 'error, warning, info, application, AWS',
				    'autoCreateLogTable' => true,
				),
			),
		),
	    
	    // Подключаем библиотеку, позволяющую разграничение доступа на основе ролей (RBAC)
	    // Класс RDbAuthManager предоставлен модулем rights и находится в /modules/rights/components
	    'authManager' => array(
	        // Provides support authorization item sorting.
	        'class'      => 'RDbAuthManager',
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
        'bootstrap' => array(
            'class' => 'bootstrap.components.Bootstrap',
            //'class' => 'bootstrap.components.TbApi',
        ),

        // image manipulation library (for galleryManager)
        'image' => array(
            'class' => 'ext.image.CImageComponent',
        ),
        
        // Настройки сессии
        'session' => array(
            // @todo хранить сессию в БД     
            'class'     => 'CHttpSession',//CDbHttpSession
            'autoStart' => true,
            // храним сессию 2 месяца
            'timeout' => 3600 * 24 * 60,
            //'connectionID' => 'db',
            //'autoCreateSessionTable' => true,
        ),
        
        // Настройки менеджера скриптов
        'clientScript' => array(
            // настройка темы jQuery
            'scriptMap' => array(
                'jquery-ui.css'     => '/css/jqueryui/flick/jquery-ui.css',
                'jquery-ui.min.css' => '/css/jqueryui/flick/jquery-ui.min.css',
            ),
            // подключаем sweekit-библиотеку для удобной работы со скриптами
            'behaviors' => array(
                'sweelixClientScript' => array(
                    'class' => 'ext.sweekit.behaviors.SwClientScriptBehavior',
                ),
            ),
        ),
        
        // Наша обертка вокруг Amazon Web Services API
        'ecawsapi' => array(
            'class' => 'EasyCastAmazonAPI',
        ),
        
        // отсылка SMS (через smspilot.ru)
        'sms' => array(
            'class'  => 'application.components.smspilot.SmsPilotAPI',
            'from'   => 'easyCast',
            'apikey' => 'I5F2640ER6B486245L3HCB6VTJN687RQ10V5DAVB3KG2J1B29U6PZ5MK95WHTGFB',
        ),
        
        // Компонент "simple Workflow" - для грамотной работы со статусами
        'swSource' => array(
            'class' => 'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
        ),
        
        // API для работы с Мегапланом (только общие настройки, все основное лежит в production и dev-ветках)
        'megaplan' => array(
            'class' => 'EasyCastMegaplanAPI',
            'host'  => 'easycast.megaplan.ru',
        ),
        
        // Настройки по умолчанию для всех виджетов
        'widgetFactory' => array(
            'widgets' => array(
                // Выравнивание верстки для форума
                // @todo поправить форум, а эту настройку отсюда убрать
                'CGridView' => array(
                    'itemsCssClass' => '',
                    'pagerCssClass' => '',
                ),
                
                // Формы для сложных значений
                'MultiModelForm' => array(
                    'tableView'         => true,
                    'bootstrapLayout'   => true,
                    // все кнопки "удалить" становятся красными
                    'removeHtmlOptions' => array(
                            'class' => 'btn btn-danger mmf_removelink',
                        ),
                    // все кнопки "добавить" становятся зелеными
                    'addHtmlOptions' => array(
                            'class' => 'btn btn-success',
                        ),
                    // делаем все формы узкими, чтобы они вписались в верстку
                    'tableHtmlOptions' => array(
                            'style' => 'width:auto;',
                            'class' => 'table-striped',
                        ),
                ),
                
                // Выбор даты из календаря
                'CJuiDatePicker' => array(
                    'language' => 'ru',
                    'options' => array(
                        'showAnim' => 'fold',
                        // @todo привести к стандартному формату
                        'dateFormat' => 'dd/mm/yy',
                    ),
                ),
                
                // Галерея загрузки фотографий
                'GalleryManager' => array(),
                
                // Выбор даты (календарь из библиотеки yiiBooster)
                'TbDatePicker' => array(
                    'options' => array(
                        'language'  => 'ru',
                        'format'    => 'dd.mm.yyyy',
                        'weekStart' => 1,
                        'autoclose' => true,
                    ),
                ),
            ),
        ),
	),

    // Используем собственную тему оформления для сайта
    'theme' => 'easycast',

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
	    // телефон по умолчанию, для всех вопросов по сайту
	    'adminPhone'    => '+7(906)098-32-07',
	    // телефон для заказчиков
	    'customerPhone' => '+7(495)227-5-226',
	    // телефон для участников
	    'userPhone'     => '+7(968)590-88-00',
	    // почта по умолчанию для сбора всех вопросов и отправки технических писем
	    'adminEmail'    => 'admin@easycast.ru',
	    // password salt для хеширования паролей
	    'hashSalt'      => '68xc7mtux0',
	    
	    // стандартный формат ввода даты для всех форм в приложении
	    // (используется формат jquery-виджетов)
	    'inputDateFormat'     => 'dd.M.yyyy',
	    'inputTimeFormat'     => 'HH:mm',
	    'inputDateTimeFormat' => 'dd.MM.yyyy HH:mm',
	    // страндартный формат вывода даты для всех форм в приложении
	    // (используется формат php-функции date())
	    'outputDateFormat'     => "d.m.Y",
	    'outputTimeFormat'     => 'H:i',
	    'outputDateTimeFormat' => "d.m.Y H:i",
	    
	    // Настройки хостинга Amazon
	    'AmazonS3Config' => 'easycast.s3',
	    
	    // использовать ли прокси сервера google для отображения картинок в письмах
	    // (должно быть включено на production и выключено на машине разработчика)
	    'useGoogleImageProxy' => true,
	),
);