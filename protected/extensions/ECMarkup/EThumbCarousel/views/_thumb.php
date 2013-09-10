<?php
/**
 * Отображает одну preview-картинку
 */
?>
<li class="span1">
    <a href="#" id="<?=$data['baseId']; ?>-thumbnail-<?=$data['num']; ?>" class="thumbnail" 
        rel="tooltip" data-title="<?php echo isset($data['label']) ? $data['label'] : ''; ?>">
        <img src="<?=$data['image']; ?>">
    </a>
</li>