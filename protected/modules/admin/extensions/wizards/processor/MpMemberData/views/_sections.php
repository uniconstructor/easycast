<?php
/**
 * Список разделов в которые можно определить участника
 */
/* @var $this MpMemberData */
?>
<h3 class="text-center">Разделы для этой заявки</h3>
<?php 
// Список разделов
$this->widget('admin.extensions.wizards.processor.MpMemberSectionList.MpMemberSectionList', $this->sectionGridOptions);
