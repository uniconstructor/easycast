<?php
/**
 * Отображение одной вакансии
 */
$style = 'display:none;';
if ( $messageText )
{
    $style = '';    
}
?>
<div class="row span6">
    <h4><?= CHtml::encode($vacancy->name); ?></h4>
    <p><?= $vacancy->description; ?></p>
    <div id="vacancy_message_<?= $vacancy->id; ?>" class="<?= $messageClass; ?>" style="<?= $style; ?>">
        <?= $messageText; ?>
    </div>
    <p><?= $addAppllicationButton; ?></p>
    <hr>
</div>