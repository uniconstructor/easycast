<?php
/**
 * Простой шаблон одной страницы с содержимым: верстка в одну колонку
 * 
 * @todo сомневаюсь в необходимости этого шаблона - удалить если не пригодится
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