<?php
/**
 * Единая точка входа точка входа в cockpit
 * На этот файл перенаправляются все внешние запросы к любым другим файлам или подпапкам
 */

//include cockpit
include_once('cockpit/bootstrap.php');

$app = new Lime\App();

$app->bind("/", function() {
    return "Hello World!";
});

$app->bind("/misc/cockpit/", function() {
    return "forbidden";
});

$app->run();