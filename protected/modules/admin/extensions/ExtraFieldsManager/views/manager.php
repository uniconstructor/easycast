<?php
/**
 * Два списка полей: 
 * 1) обязательные поля, которые нужно добавить в анкету чтобы подать заявку
 * 2) дополнительные поля, которых нет в анкете и которые прикрепляются к роли: пользователи также заполняют
 *    их при подаче заявки
 */
/* @var $this ExtraFieldsManager */
?>
<div class="row-fluid">
    <div class="span6">
        <div class="title-page">
            <h2>Обязательные поля для анкеты</h2>
            <h4 class="title-description">
                Выберите поля анкеты которые будет предложено заполнить участнику перед подачей заявки.
                Чем больше полей заполнено - тем меньше данных будет предложено внести участнику.
                Тут нужно указывать данные <b>которые могут понадобится для других ролей</b>.
            </h4>
            <?php 
            $this->widget('admin.extensions.EditRequiredFields.EditRequiredFields', array(
                'objectType' => 'vacancy',
                'objectId'   => $this->vacancy->id,
            ));
            ?>
        </div>
    </div>
    <div class="span6">
        <div class="title-page">
            <h2>Дополнительные поля для заявки</h2>
            <h4 class="title-description">
                Этих полей нет в анкете, они привязаны к заявке и хранятся вместе с ней.
                Здесь нужно указывать поля <b>которые нужны только один раз</b> (например только для этой роли).
            </h4>
            <?php
            $this->widget('admin.extensions.EditExtraFieldInstances.EditExtraFieldInstances', array(
                'objectType' => 'vacancy',
                'objectId'   => $this->vacancy->id,
            ));
            ?>
        </div>
    </div>
</div>