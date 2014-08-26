<?php
/**
 * Образование и фильмография актера
 */
/* @var $this EMailCastingList */
/* @var $questionary Questionary */

?>
<div class="article-content">
    <?= $this->getEducationTable($questionary); ?>
    <?= $this->getFilmsTable($questionary); ?>
    <?= $this->getExtraFieldsTable($questionary, $vacancyId); ?>
</div>