<?php
/**
 * Верстка одной вакансии из списка
 */
/* @var $this VacancyInfo */
?>
<div class="row-fluid">
    <div class="well span12">
        <h4><?= $this->vacancy->name; ?></h4>
        <p><?= $this->vacancy->description; ?></p>
        <?= $salary; ?>
        <p>
            <?= $this->createActionButtons(); ?>
        </p>
    </div>
</div>
