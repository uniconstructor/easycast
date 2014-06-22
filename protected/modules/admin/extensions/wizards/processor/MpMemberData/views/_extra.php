<?php
/**
 * Список дополнительных полей, указаных пользователем в заявке
 */
/* @var $this MpMemberData */
?>
<div class="row-fluid">
    <h3 class="text-center">Анкета участника</h3>
    <?php 
    // дополнительные поля для заявки
    $this->widget('admin.extensions.ExtraFieldsList.ExtraFieldsList', array(
        'member' => $this->member,
    ));
    ?>
</div>