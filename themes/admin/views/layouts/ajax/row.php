<?php
/**
 * Верстка сетки из нескольких виджетов
 */
/* @var $this Controller */
?>
<!-- row -->
<div class="row">
	<!-- a blank row to get started -->
	<div class="col-sm-12">
		<?= $content; ?>
	</div>
</div>
<!-- end row -->
<?php 
// js, обязательный для работы всех страниц
$this->renderPartial('//layouts/ajax/_pageSetup');