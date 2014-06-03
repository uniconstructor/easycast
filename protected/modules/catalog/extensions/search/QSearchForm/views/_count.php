<?php
/**
 * Фрагмент формы поиска: блок с количеством подходящих условиям участников
 */
/* @var $this QSearchForm */
?>
<?= CHtml::openTag('div', $this->countContainerHtmlOptions); ?>
    <span class="alert alert-success">Найдено: <b id="<?= $this->countResultsId; ?>"></b></span>
<?= CHtml::closeTag('div'); ?>