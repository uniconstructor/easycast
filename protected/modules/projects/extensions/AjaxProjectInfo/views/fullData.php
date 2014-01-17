<?php
/**
 * Информация о проекте: краткое описание + логотип + полное описание
 */
/* @var $this AjaxProjectInfo */
?>
<div class="row-fluid">
    <div class="span3 text-center">
        <?= $logo; ?>
        <?= $this->project->shortdescription; ?>
    </div>
    <div class="span9">
        <?= $description; ?>
    </div>
</div>