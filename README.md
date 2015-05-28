EasyCast
===============================

Вторая версия сайта, основанная на [Yii 2](http://www.yiiframework.com/).

Проект разделен на три части: основной сайт (frontend), админка (backend), 
и консольное приложение (console). Каждая часть является отдельным
приложением Yii.

Структура проекта основана на 
[расширенном шаблоне приложения Yii 2](https://github.com/yiisoft/yii2-app-advanced)


Структура проекта рассчитана на работу команды из нескольких разработчиков. 
Поддерживается настройка среды исполнения: (development/production).

Структура папок
-------------------

```
common
    config/              общие настройки (не зависят от среды исполнения)
    mail/                шаблоны электронных писем (view files for e-mails)
    models/              классы моделей используемые и в админке и на сайте
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  содержит пакеты composer
environments/            contains environment-based overrides
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
```
