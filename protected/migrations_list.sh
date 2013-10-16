#!/bin/bash
# Список всех миграций EasyCast

# Главное меню
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.extensions.ECMarkup.ECMainMenu.migrations
# Условия поиска
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.extensions.ESearchScopes.migrations
# Галерея
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.extensions.galleryManager.migrations
# Статьи
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.articles.migrations
# Каталог
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.catalog.migrations
# Новости
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.news.migrations
# Фотогалерея
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.photos.migrations
# Проекты
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.projects.migrations
# Анкеты
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.questionary.migrations
# Пользователи
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.user.migrations
# Уведомления (пока неизвестно, пригодится ли этот модуль)
# php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.notifyii.migrations
# Форум (пока без миграций)
# php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.yii-forum.migrations
# Миграция приложения делается поcледней
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.migrations