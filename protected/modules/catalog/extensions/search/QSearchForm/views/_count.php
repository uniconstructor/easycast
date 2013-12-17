<?php
/**
 * Фрагмент формы поиска: блок с количеством подходящих условиям участников
 */
/* @var $this QSearchForm */
?>
<div id="<?= $this->countResultsId; ?>_container" class="well hide" style="text-align:center;">
    <span class="alert alert-success">Найдено: <b id="<?= $this->countResultsId; ?>"></b></span>
</div>