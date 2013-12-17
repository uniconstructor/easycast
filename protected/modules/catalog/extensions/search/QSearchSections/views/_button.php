<?php
/**
 * Отображение одной кнопки раздела
 */
/* @var $this QSearchSections */
/* @var $section CatalogSection */
?>
<div class="ec-join_but span1">
    <button rel="tooltip" data-toggle="tooltip" data-placement="bottom" data-original-title="<?= $section->name; ?>" 
         type="button" title="" class="btn ec-btn-primary btn-lg btn-usual ec-btn-usual"
         id="QSearchsections_button_<?= $section->id; ?>">
        <img src="<?= $this->_assetUrl.'/images/'.$section->shortname; ?>.png" alt="<?= $section->name; ?>" title="" />
    </button>
    <input type="checkbox" style="display:none;" name="QSearchsections[sections][]" 
        value="<?= $section->id; ?>" id="QSearchsections_hidden_<?= $section->id; ?>">
</div>