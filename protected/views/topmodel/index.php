<?php
/**
 * 
 */
/* @var $this TopModelController */
?>
<div class="container">
    <div class="row text-center">
        <?= CHtml::image('/images/tm_form_header.jpg') ?>
    </div>
    <div class="row">
        <div class="span12">
            <div class="title-page">
                <h4 class="intro-description">
                    Чтобы подать заявку на участие в кастинге необходходимо указать дополнительные данные.<br>
                    (Личная информация из вашей анкеты не будет видна никому кроме организаторов кастинга. 
                    Любые указанные контакты также будут использоваться только для уточнения 
                    информации и оповещения вас о дальшейших этапах отбора)
                </h4>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <?php 
        // виджет динамической формы: он сам выбирает нужные поля в зависимости от настроек роли
        $this->widget('questionary.extensions.widgets.QDynamicForm.QDynamicForm', array(
            'model' => $model
        ));
        ?>
    </div>
</div>
