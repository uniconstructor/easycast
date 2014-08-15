<?php
/**
 * Заготовка для верстки во всю ширину содержимого
 */
/* @var $this Controller */
?>
<!-- row -->
<div class="row">
	<!-- a blank row to get started -->
	<?= $content; ?>
</div>
<!-- end row -->
<?php 
// js, обязательный для работы всех страниц
$this->renderPartial('//layouts/ajax/_pageSetup');