<?php 
/**
 * 
 */
/* @var $this Controller */

// head-раздел и мета-теги
$this->beginContent('//layouts/main');
?>
<div class="page-alternate">
    <div class="container">
        <?= $content; ?>
    </div>
</div>
<?php
// все скрипты, которые должны быть подключены внизу страницы CClientScript::POS_END
$this->endContent();