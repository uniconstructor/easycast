<?php
/**
 * Простой шаблон одной страницы с содержимым: верстка в одну колонку
 * 
 * @todo понять почему "a blank row to get started": имеется ли в виду, что сначала нужно пропустить 
 *       строку, а только потом начинать содержимое?
 */
/* @var $this    Controller */
/* @var $content string */
?>
<!-- row -->
<div class="row">
    <!-- a blank row to get started -->
    <?= $content; ?>
</div>
<!-- end row -->
<?php 
// js, обязательный для работы всех страниц темы SmartAdmin: 
// отвечает за инициализацию всех элементов и за подгрузку содержимого страницы по AJAX
$this->renderPartial('//layouts/ajax/_pageSetup');