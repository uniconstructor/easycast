<?php
/**
 * Отображение одной кнопки раздела
 */
/* @var $this QSearchSections */
/* @var $section CatalogSection */
?>
<div class="ec-join_but">
    <button rel="tooltip" data-toggle="tooltip" data-placement="bottom" data-original-title="<?= $section->name; ?>" 
         type="button" title="" 
         class="btn ec-btn-primary btn-lg btn-usual ec-btn-usual ec-search-section-icon <?= $activeButtonClass; ?>"
         id="QSearchsections_button_<?= $section->id; ?>">
        <img src="<?= $this->_iconsAssetUrl.'/images/'.$section->shortname; ?>.png" alt="<?= $section->name; ?>" title="" />
    </button>
    <input type="checkbox" style="display:none;" name="QSearchsections[sections][]" <?= $checkedValue; ?>
        value="<?= $section->id; ?>" id="QSearchsections_hidden_<?= $section->id; ?>">
</div>