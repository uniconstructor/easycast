<?php
/**
 * Верстка одной вакансии из списка
 */
/* @var $this VacancyInfo */
?>
<div class="row-fluid">
    <div class="well span12">
        <div class="row-fluid">
            <h4><?= $this->vacancy->name; ?></h4>
            <p><?= $this->vacancy->description; ?></p>
        </div>
        <div class="row-fluid">
            <?= $salary; ?>
            <br><br>
            <?= $this->createActionButtons(); ?>
        </div>
    </div>
</div>
