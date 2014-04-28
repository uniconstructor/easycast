#!/bin/bash
# Файл, запускающий все миграции EasyCast
# Сначала запускаются все миграции модулей (в любом порядке), а самом конце миграция всего приложения
# На момент запуска миграции приложения предполагается, что миграфии всех модулей уже успешно выполнены

# определяем директорию, в которой лежат файлы (в зависимости от того, где запускаем: dev, test или production)
export EASYCAST_PROTECTED=/home/frost/server/sites/bglance/protected

# Запускаем сами миграции
sh $EASYCAST_PROTECTED/migrations_list.sh

# trader
php $EASYCAST_PROTECTED/yiic migrate --migrationPath=application.modules.trader.migrations