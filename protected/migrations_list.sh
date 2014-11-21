#!/bin/bash
# Список всех миграций EasyCast

# Галерея
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.extensions.galleryManager.migrations
# Каталог
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.catalog.migrations
# Проекты
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.projects.migrations
# Анкеты
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.questionary.migrations
# Пользователи
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.user.migrations
# Отчеты
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.reports.migrations

####### отключено ########
# Уведомления (пока неизвестно, пригодится ли этот модуль)
# php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.notifyii.migrations
# Форум (пока без миграций)
# php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.yii-forum.migrations
# Новости
# php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.news.migrations
# Условия поиска
# php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.extensions.ESearchScopes.migrations
# Статьи
# php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.articles.migrations
####### отключено ########

# Миграция приложения делается поcледней
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.migrations