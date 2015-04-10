<?php 
/**
 * Верстка страницы на 2 колонки
 */
/* @var $this Controller */

// head-раздел и мета-теги
$this->beginContent('//layouts/main', array('content' => $content));

// все скрипты, которые должны быть подключены внизу страницы CClientScript::POS_END
$this->endContent();