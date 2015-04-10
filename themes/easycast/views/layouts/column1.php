<?php 
/**
 * Верстка страницы "в одну колонку"
 * Используется в большинстве страниц
 */
/* @var $this Controller */

// head-раздел и мета-теги
$this->beginContent('//layouts/main');
// вывод основного содержимого страницы (все что между header и footer)
echo $content;
// все скрипты, которые должны быть подключены внизу страницы CClientScript::POS_END
$this->endContent();