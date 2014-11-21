<?php

// Главный файл конфигурации приложения.
// Здесь задаются все общие параметры, одинаковые для "production"(релиза), "test"(тестового сервера) 
// и "dev"(версии разработчика).
// В разных ветках git-репозитория лежат дополнительные config-файлы для каждой версии сайта.
// Каждая версия сайта собирается отдельным ant-скриптом.
// Последовательность сборки такова: 
//                    1) Yii
//                    2) EasyCast (ядро)
//                    3) Оригинальные плагины
//                    4) Наши изменения в плагинах
//                    5) Настройки окружения (для dev, test или production версии)
return array(
    // Основные параметры приложения
    // физический путь к папке "protected"
	'basePath'       => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    // язык приложения
    'language'       => 'ru',
    // язык исходников (установлен английский потому что он основной для большинства сторонних модулей)
    'sourceLanguage' => 'en_us',
    // предварительно загружаемые компоненты
    'preload'        => array('log', 'messages', 'bootstrap'),
    // Название проекта 
    'name'           => 'easyCast',
    // Короткие имена для вызова популярных библиотек
    'aliases' => array(
        // виджет для асинхронной загрузки больших файлов
        'xupload'        => 'ext.xupload',
        // YiiBooster (версия 3 для bootstrap 2.3.2)
        'bootstrap'      => 'ext.bootstrap',
        // библиотека google для обработки телефонных номеров
        'libphonenumber' => 'application.components.libphonenumber',
    ),
    // Используем собственную тему оформления для сайта
    'theme'  => 'maximal',
	// автозагрузка для основных классов приложения
    'import' => array(
        // основные компоненты приложения
		'application.actions.*',
		'application.behaviors.*',
		'application.models.*',
		'application.filters.*',
		'application.components.*',
	    'application.extensions.*',
        // @todo заменить выбор страны и города на GeoNames API
	    'application.extensions.CountryCitySelectorRu.models.*',
	    // Пользователи
	    'application.modules.user.models.*',
	    'application.modules.user.components.*',
	    // Управление доступом на основе ролей (RBAC)
	    'application.modules.rights.*',
	    'application.modules.rights.components.*',
	    // Анкетные данные участника
	    'application.modules.questionary.*',
	    'application.modules.questionary.components.*',
	    'application.modules.questionary.models.*',
        // Модуль загрузки изображений
        'ext.galleryManager.models.*',
	    // Модуль simpleWorkflow (для работы со статусами модели)
	    'application.extensions.simpleWorkflow.*',
	    // sweekit: библиотека для удобной работы с JS, включающая плагин shadowbox
	    'ext.sweekit.Sweeml',
	    // галерея изображений (виджет)
        'application.extensions.galleria.*',
        // @deprecated устаревшие компоненты
        // @todo (запланировано) компонент для работы с Google Maps
        // 'ext.sweekit.map.*',
        // Виджеты Twitter Bootstrap
        // @todo удалить после подключения YiiBoster 4.0.1
        //'application.extensions.bootstrap.widgets.*',
        // старый виджет выбора "да/нет"
        // @todo с подключением YiiBooster виджет устарел: заменить все обращения
        //       к нему на новые элементы а затем удалить из сборки при рефакторинге
        'application.extensions.jtogglecolumn.*',
	),
    // все модули проекта
	'modules' => array(
	    // Пользователи
        'user' => array(
            // encrypting method (php hash function)
            'hash'                => 'sha1',
            // send activation email
            'sendActivationMail'  => true,
            // allow access for non-activated users
            'loginNotActiv'       => true,
            // activate user on registration (only sendActivationMail = false)
            'activeAfterRegister' => true,
            // automatically login from registration
            'autoLogin'           => true,
            // registration path
            'registrationUrl'     => array('//user/registration'),
            // recovery password path
            'recoveryUrl'         => array('//user/recovery'),
            // login form path
            'loginUrl'            => array('//user/login'),
            // page after login
            'returnUrl'           => array('//questionary/questionary/view'),
            // page after logout
            'returnLogoutUrl'     => array('//site/index'),
        ),
        // Права доступа (RBAC)
	    'rights' => array(
	        // установка (производится только один раз, потом всегда должна быть false)
	        'install'           => false,
	        // разрешаем использовать переменные в правилах доступа
	        'enableBizRuleData' => true,
	        // нужно установить разметку страницы в соответствии с нашей темой
	        'appLayout'         => '//layouts/column1',
	    ),
	    // анкета пользователя (реализована отдельным модулем)
	    'questionary' => array(
            'controllerMap' => array(
                // задаем путь к контроллеру загрузки изображений (для анкеты)
                'gallery'   => array(
                    'class'        => 'application.modules.questionary.controllers.QGalleryController',
                    // используем класс-обработчик загруженных изображений, работающий с Amazon
                    'handlerClass'  => 'GmS3Photo',
                    'customBehaviors' => array(
                        'S3GalleryControllerBehavior' => array(
	                        'class' => 'application.extensions.galleryManager.behaviors.S3GalleryControllerBehavior',
	                    ),
	                ),
                ),
            ),
        ),
        // Календарь событий
        // @todo весь модуль теперь можно заменить отдельным виджетом
        'calendar' => array(
            'class' => 'application.modules.calendar.CalendarModule',
        ),
        // Админка
        'admin' => array(
            'class' => 'application.modules.admin.AdminModule',
            'controllerMap' => array(
                // задаем путь к контроллеру загрузки изображений (для анкеты)
                'gallery' => array(
                    'class'        => 'ext.galleryManager.GalleryController',
                    'handlerClass' => 'GmS3Photo',
                    'customBehaviors' => array(
                        'S3GalleryControllerBehavior' => array(
                            'class' => 'application.extensions.galleryManager.behaviors.S3GalleryControllerBehavior',
                        ),
                    ),
                ),
            ),
        ),
        // Проекты
        'projects' => array(
            'class' => 'application.modules.projects.ProjectsModule',
        ),
        // Каталог
        'catalog' => array(
            'class' => 'application.modules.catalog.CatalogModule',
        ),
        // Новости
        /*'news' => array(
            'class' => 'application.modules.news.NewsModule',
        ),*/
        // Статьи
        /*'articles' => array(
            'class' => 'application.modules.articles.ArticlesModule',
        ),*/
        // Письма (этот модуль отвечает за правильную верстку писем)
        'mailComposer' => array(
            'class' => 'application.modules.mailComposer.MailComposerModule',
        ),
        // отчеты
        'reports' => array(
            'class' => 'application.modules.reports.ReportsModule',
        ),
        // @todo (запланировано) Уведомления на Android/iOS
        /*'mobileNotification' => array(
            'class' => 'ext.sweekit.actions.SwMobileNotifier',
            'mode' => 'production', // can be development to use the sandbox
            'apnsCertificateFile' => 'my_apple_certificate_for_push.pem',
            'apnsCertificatePassphrase' => 'my_apple_certificate_passphrase', // comment out if there is no passphrase
            'apnsEmbeddedCaFile'=>true, // embed entrust ca file if needed (ssl errors, ...)
            'c2dmUsername' => 'my_gmail_push_account@gmail.com',
            'c2dmPassword' => 'my_gmail_push_account_password',
            'c2dmApplicationIdentifier' => 'my_gmail_push_app_identifier',
        ),*/
	),

	// Компоненты приложения
	'components' => array(
	    // пользователи (важно: здесь настройки для авторизации, а не для модели User)
		'user' => array(
		    // используем класс пользователя из модуля rights чтобы работали права доступа на основе ролей (RBAC)
            'class'          => 'RWebUser',
            // сразу же пускаем участника на сайт после регистрации, чтобы сэкономить ему время
            'allowAutoLogin' => true,
            // адрес страницы с формой входа
            'loginUrl'       => array('/user/login'),
		),
		// библиотека CURL
		// @todo перейти на Guzzle: https://github.com/guzzle/guzzle
		'curl' => array(
		    'class'   => 'ext.curl.Curl',
		    //'options' => array(),
		),
		// настройки преобразования url-адресов
		// @todo найти способ настраивать сокращенные адреса из базы а не вручную через этот файл
		'urlManager' => array(
		    // отображаем все URL в формате /путь/к/странице (на сервере должен быть включен mod_rewrite)
			'urlFormat' => 'path',
			'rules'     => array(
				'<controller:\w+>/<id:\d+>'              => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>'          => '<controller>/<action>',
				// сокращенные ссылки на специальные страницы
				// @todo перенести в контроллер
				// проект "МастерШеф"
				'chief'       => 'projects/vacancy/registration/vid/749',
				'masterchief' => 'projects/vacancy/registration/vid/749',
				// проект "выбери меня"
				'vm'          => 'projects/vacancy/registration/vid/1017',
				'vm1'         => 'projects/vacancy/registration/vid/1018',
				'vybor_man'   => 'projects/vacancy/registration/vid/1017',
				'vybor_woman' => 'projects/vacancy/registration/vid/1018',
			),
		    'showScriptName' => false,
		),
		
		// настройки MySQL (только общие для всех сборок)
		'db' => array(
			// ВАЖНО: Логин и пароль для базы определяются ant-скриптами сборки проекта
			// (в зависимости от того где работает приложение: на реальном сервере или локально)
			// поэтому в основном конфиге эти параметры не указываются: они задаются 
			// в config-файлах веткок dev, test и release
			// (для того чтобы базовый config.php содержал только те настройки, которые не меняются
		    // в зависимости от варианта сборки)  
		    'tablePrefix'    => 'bgl_',
		    'emulatePrepare' => true,
			'charset'        => 'utf8',
		),
	    // работа с HTTP-запросами
	    'request' => array(
	        'class'     => 'CHttpRequest',
	        // дополнительные методы объекта request для работы js-библиотеки sweekit
	        'behaviors' => array(
	            'sweelixAjax' => array(
	                'class' => 'ext.sweekit.behaviors.SwAjaxBehavior',
	            ),
	        ),
	        // включаем защиту от XSS-атак
	        'enableCsrfValidation'   => true,
	        // включаем защиту от подмены cookie
	        'enableCookieValidation' => true,
	    ),
	    // обработка ошибок
		'errorHandler' => array(
			// путь 'site/error' будет использован для отображения всех ошибок на сайте
            'errorAction' => 'site/error',
        ),
	    // Подключаем библиотеку, позволяющую разграничение доступа на основе ролей (RBAC)
	    // Класс RDbAuthManager предоставлен модулем rights и находится в /modules/rights/components
	    'authManager' => array(
	        // Provides support authorization item sorting
	        'class'      => 'RDbAuthManager',
	        // показываем ошибки авторизации только в режиме отладки: 
	        // (для тестовой сборки и версии разработчика)
	        'showErrors' => YII_DEBUG,
	    ),
	    // Подключаем модуль i18n чтобы можно было переводить приложение на разные языки
	    // @todo подключить CDbMessageSource для того чтобы в модуле Questionary
	    //       можно было руками добавлять перевод для стандартных значений 
	    //       или править тексты пояснений к полям анкеты
	    //       (пока что все языковые строки берутся из php-файлов)
	    'messages' => array(
	        'class' => 'CPhpMessageSource',
	    ),
        // YiiBooster 3: обертка для Twitter Bootstrap 2.3.2 
        'bootstrap' => array(
            'class'          => 'bootstrap.components.Bootstrap',
            // подключаем набор иконок "Font Awesome"
            //'fontAwesomeCss' => true,
            // сжимаем скрипты и стили
            'minify'         => true,
            // отключаем базовые css для bootstrap при использовании темы maximal
            'coreCss'        => false,
            'bootstrapCss'   => false,
            'responsiveCss'  => false,
        ),
        // библиотека для работы с изображениями (требуется для плагина galleryManager)
        'image' => array(
            'class' => 'ext.image.CImageComponent',
        ),
        // Настройки сессии
        // @todo хранить сессию в БД, используя расширение http://www.yiiframework.com/extension/session
        // @todo вынести время хранения сессии в настройку
        'session' => array(
            'class'                  => 'CDbHttpSession',
            'autoStart'              => true,
            // стандартное время хранения сессии: 2 месяца
            'timeout'                => 3600 * 24 * 60,
            'connectionID'           => 'db',
            'autoCreateSessionTable' => false,
        ),
        // Настройки js-библиотек и скриптов
        'clientScript' => array(
            // подключаем скрипты для работы js-библиотеки sweekit 
            'behaviors' => array(
                'sweelixClientScript' => array(
                    'class' => 'ext.sweekit.behaviors.SwClientScriptBehavior',
                ),
            ),
        ),
        // Наша обертка вокруг Amazon Web Services API: облегчает обращение к часто используемым методам
        'ecawsapi' => array(
            'class' => 'application.components.EcAwsApi',
        ),
        // отправка SMS (через smspilot.ru)
        // @todo для production перенести ключи доступа к SmsPilot в настройки сервера, (как пароль к БД)   
        //       В dev и test-ветках использовать другие ключи
        'smspilot' => array(
            'class'  => 'application.components.smspilot.SmsPilotAPI',
            'from'   => 'easyCast',
            'apikey' => 'I5F2640ER6B486245L3HCB6VTJN687RQ10V5DAVB3KG2J1B29U6PZ5MK95WHTGFB',
        ),
        // Компонент "Simple Workflow" нужен для работы со статусами объектов, позволяет установить
        // workflow-схему работы для приложения {@see http://en.wikipedia.org/wiki/Workflow}
        // через этот компонент должны производится любые операции со статусом любого объекта в базе
        'swSource' => array(
            'class'          => 'SWPhpWorkflowSource',
            'definitionType' => 'class',
        ),
        // @todo Компонент для работы с сервисом отправки писем {@see https://mailchimp.com}
        // используем API последней (второй) версии
        /*'mailchimp' => array(
            'class'  => 'application.extensions.mailchimp.EMailChimp2',
            'apikey' => '43db0f030585ce1f6b6a27fa4d13de12-us7',
        ),*/
        // Google API: компонент для работы с сервисами Google
        'JGoogleAPI' => array(
            'class' => 'application.components.JGoogleAPI.JGoogleAPI',
            // Account type Authentication data
            /*'serviceAPI' => array(
                'clientId'    => '28411509328-h2tdqhkf8vbi606iddpgmolvdj1559gj.apps.googleusercontent.com',
                'clientEmail' => '28411509328-h2tdqhkf8vbi606iddpgmolvdj1559gj@developer.gserviceaccount.com',
                'keyFilePath' => dirname(__FILE__).'/../data/JGoogleAPI/API Project-38864dd6a6bf.p12',
            ),
            'defaultAuthenticationType' => 'serviceAPI',
            */
            // Web Service Authentication data
            // @todo вынести ключи доступа в параметры сервера Amazon 
            'webappAPI' => array(
                'clientId'          => '28411509328-avgcsevcbd0ths764j9925i23diqr0q2.apps.googleusercontent.com',
                'clientEmail'       => '28411509328-avgcsevcbd0ths764j9925i23diqr0q2@developer.gserviceaccount.com',
                'clientSecret'      => 'VwMv8zIKO9IU_xe8yc7Y4NOh',
                // @todo заменить на https после установки сертификата
                'redirectUri'       => 'http://easycast.ru/gapi/oauth2callback',
                // @todo заменить на https после установки сертификата
                'javascriptOrigins' => 'http://easycast.ru',
            ),
            'defaultAuthenticationType' => 'webappAPI',
            // Scopes needed to access the API data defined by authentication type
            'scopes' => array(
                'serviceAPI' => array(
                    'drive' => array(
                        'https://www.googleapis.com/auth/drive.file',
                    ),
                ),
                'webappAPI' => array(
                    'drive' => array(
                        'https://www.googleapis.com/auth/drive.file',
                    ),
                ),
            ),
            // You can define one of the authentication types or both 
            // (for a Service Account or Web Application Account)
            'simpleApiKey' => 'AIzaSyB5IsWcZfQE5otLyrXMBNMiRTktAeEbHCg',
        ),
        
        // Настройки по умолчанию для всех виджетов Yii
        'widgetFactory' => array(
            'widgets' => array(
                // Формы для сложных значений
                // @todo удалить эту настройку вместе с самим плагином multimodelform
                //       когда весь multimodelform будет заменен редактируемыми grid-списками
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
                // Выбор даты из календаря (jQuery)
                // @todo этот элемент везде должен быть заменен более новым TbDatePicker 
                'CJuiDatePicker' => array(
                    'language' => 'ru',
                    'options'  => array(
                        'showAnim'   => 'fold',
                        'dateFormat' => 'dd/mm/yy',
                    ),
                ),
                // Галерея загрузки фотографий
                'GalleryManager' => array(),
                // Выбор даты (календарь из библиотеки yiiBooster)
                'TbDatePicker' => array(
                    'options' => array(
                        'language'  => 'ru',
                        // @todo проверить, правильно ли записано, привести к единому формату, использовать константы
                        'format'    => 'dd.mm.yyyy',
                        // неделя всегда начинается с понедельника
                        'weekStart' => 1,
                        // автоматически закрывать календарь после выбора даты
                        'autoclose' => true,
                    ),
                ),
                // плитка изображений с раскрывающимся описанием
                'CdGridPreview' => array(
                    // не подключаем библиотеку modernizr вместе с виджетом
                    // (она уже включена вместе со стандартными библиотеками в теме оформления, layouts/main.php)
                    'includeModernizr' => false,
                ),
            ),
        ),
        // трассировка и логи
        // @todo для всех сборок добавить плагин для просмотра логов
        // @see  http://www.yiiframework.com/extension/yii-audit-module/
        'log' => array(
            'class'  => 'CLogRouter',
            'routes' => array(
                // логи easyCast хранятся отдельно от системных: наша таблица содержит, в основном
                // данные по активности пользователей и статистику выполняемых ими задач
                'EcLogRoute' => array(
                    'class'        => 'EcLogRoute',
                    'connectionID' => 'db',
                    'levels'       => 'easycast',
                    'filter'       => array(
                        'class'      => 'CLogFilter',
                        'prefixUser' => true,
                        'logUser'    => true,
                    ),
                    'autoCreateLogTable' => true,
                ),
            ),
        ),
	),
	
	// другие параметры приложения, синтаксис вызова: Yii::app()->params['paramName']
	// @todo переместить в params.php
	'params' => array(
	    // телефон по умолчанию, для всех вопросов по сайту (используем телефон техподдержки пользователей)
	    'adminPhone'    => '+7(968)590-88-00',
	    // телефон для заказчиков (прямой, круглосуточный)
	    'customerPhone' => '+7(495)227-5-226',
	    // телефон для участников (техподдержка пользователей)
	    'userPhone'     => '+7(968)590-88-00',
	    // почта по умолчанию для сбора всех вопросов и отправки технических писем
	    'adminEmail'    => 'admin@easycast.ru',
	    // password salt для хеширования паролей
	    // @todo переместить в настройки сервера amazon (как логин/пароль к БД)
	    'hashSalt'      => '68xc7mtux0',
	    // список администраторов, от имени которых система может отправлять письма
	    // (как правило это руководители проектов)
	    // список разрешенных email-адресов можно посмотреть и отредактировать 
	    // в админской панели Amazon SES:
	    // @see https://console.aws.amazon.com/ses/home?region=us-east-1#verified-senders-email:
	    // @todo вынести список в настройку или брать его напрямую через Amazon API
	    'verifiedSenders' => array(
            'admin@easycast.ru',
            'ceo@easycast.ru',
            'irina@easycast.ru',
            'liza@easycast.ru',
            'max@easycast.ru',
        ),
	    
	    ////////////////////////////////////////////////////////////////////
	    ///////// ВАЖНО: стандарт форматирования дат во всех формах ////////
	    ////////////////////////////////////////////////////////////////////
	    // При работе с виджетами выбора даты и времени нужно знать важную особенность:
	    // стандарты форматирования даты jQuery и PHP НЕСОВМЕСТИМЫ, хотя и очень похожи. 
	    // Настройки виджетов ввода даты/времени используют один стандарт форматирования даты 
	    // (как в jquery), а функция date() в PHP - другой
	    // Поэтому НЕЛЬЗЯ одновременно использовать одну и ту же запись формата даты/времени 
	    // для преобразования unixtime в дату (в форме, для установки значения по умолчанию)
	    // и для преобразования даты в unixtime (при получении данных из такой формы).
	    
	    // @todo объявить все форматы для даты и времени константами и записать их в начале этого конфига.
	    //       Это нужно на случай если для каких-то других плагинов понадобится указать формат даты 
	    //       и времени
	    
	    // форматы ввода даты и времени: используются в настройках виджетов, для преобразования
	    // unixtime в дату скриптами виджета (на стороне клиента) 
	    // работает правило форматирования jquery-виджетов:
	    //     M  - обозначает месяц (3 буквы, язык текущей локали)
	    //     ii - обозначает минуты
	    //     mm - обозначает месяц (2 цифры, ведущие нули)
	    'inputDateFormat'     => 'dd.mm.yyyy',
	    'inputTimeFormat'     => 'hh:ii',
	    'inputDateTimeFormat' => 'dd.mm.yyyy hh:ii',
	    // форматы вывода даты и времени: используются в коде формы для преобразования 
	    // unixtime в дату при подстановке значения по умолчанию
	    // работает формат php-функции date(): 
	    //     M - обозначает месяц (3 буквы, язык текущей локали)
	    //     m - тоже обозначает месяц (2 цифры, ведущие нули)
	    //     i - обозначает минуты (2 цифры, ведущие нули)
	    'outputDateFormat'     => "d.m.Y",
	    'outputTimeFormat'     => 'H:i',
	    'outputDateTimeFormat' => "d.m.Y H:i",
	    
	    // Настройки хостинга Amazon
	    'AWSRegion' => 'us-east-1',
	    // Использовать ли прокси сервера google для кэширования картинок в отправляемых сервером письмах?
	    // Включение этой опции позволяет всегда отображать картинки из наших писем в большинстве
	    // почтовых программ и во всех основных почтовых веб-интерфейсах (gmail, google, yandex, mail.ru)
	    // Пользователю не нужно будет нажимать "показать картинки" при получении письма: он увидит их сразу.
	    // Как это будет работать: 
	    // @see http://www.campaignmonitor.com/resources/will-it-work/image-blocking/
	    // Должно быть включено на production и выключено на машине разработчика
	    // @todo убрать из общего конфига и вынести в dev, test и production
	    'useGoogleImageProxy' => true,
	    
	    // использовать ли по умочанию новые виджеты, написанные с применением технологий CSS3?
	    // (не все браузеры их пока поддерживают)
	    // @deprecated использовать механизм тем для этого
	    'useCSS3'           => true,
	    // название настройки которая хранит список по умолчанию для новых значений настройки
	    'defaultListConfig' => 'defaultListId',
	),
);