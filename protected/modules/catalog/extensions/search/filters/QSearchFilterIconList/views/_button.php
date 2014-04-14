<?php
/**
 * Отображение одной кнопки раздела
 */
/* @var $this QSearchFilterIconList */
/* @var $section CatalogSection */
?>
<span>
    <button data-toggle="tooltip" data-placement="bottom" type="button"
        class="btn <?= $activeButtonClass; ?>"
        id="QSearchsections_button_<?= $section->id; ?>">
        <?= $section->name; ?>
    </button>
    <input type="checkbox" style="display:none;" name="QSearchsections[sections][]" <?= $checkedValue; ?>
        value="<?= $section->id; ?>" id="QSearchsections_hidden_<?= $section->id; ?>">
</span>