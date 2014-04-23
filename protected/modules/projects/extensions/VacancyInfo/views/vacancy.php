<?php
/**
 * Верстка одной вакансии из списка
 */
/* @var $this VacancyInfo */
?>
<div class="row-fluid">
    <div class="row-fluid">
        <h4><?= $this->vacancy->name; ?></h4>
        <?= $this->vacancy->description; ?>
    </div>
    <div class="row-fluid">
        <?= $salary; ?>
        <br><br>
        <?= $this->createActionButtons(); ?>
    </div>
</div>
