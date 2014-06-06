<?php
/**
 * Подача заявки на событие через динамическую форму анкеты
 */
/* @var $this VacancyController */
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            
            <div class="span12">
                <div class="title-page">
                    <h4 class="intro-description">
                        Чтобы подать заявку на эту роль необходходимо указать дополнительные данные. <br>
                        <b>Ваши контакты не будут видны на сайте</b> никому кроме администраторов 
                        и будут использоваться только для связи с вами. 
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
</div>