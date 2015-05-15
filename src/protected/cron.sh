#!/bin/bash
Запускает все cron-задачи EasyCast.
Выполняемый код находится в protected/commands/CronCommand.php

# определяем директорию, в которой лежат файлы (в зависимости от того, где запускаем: dev, test или production)
export EASYCAST_PROTECTED=/home/frost/server/sites/bglance/protected

php $EASYCAST_PROTECTED/yiic cron