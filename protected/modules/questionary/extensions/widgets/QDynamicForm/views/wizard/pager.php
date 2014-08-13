<?php
/**
 * Фрагмент страницы с навигацией для элемента wizard
 */
/* @var $this QDynamicForm */
/* @var $form TbActiveForm */

// все сообщения об ошибках
echo $form->errorSummary($this->model, null, null, array('id' => $this->formId.'_es_'));
// 
echo $form->error($this->model, 'galleryid', array('id' => $this->formId.'_galleryid_em_'), true).'<br>';
?>
<div style="float:right">
    <input type="button" class="btn btn-large btn-primary button-next" name="next" value="Далее" />
    <?php 
    $this->widget('bootstrap.widgets.TbButton', array(
        'id'         => 'dynamic-registration-submit_'.$this->vacancy->id,
        'buttonType' => 'submit',
        'type'       => 'success',
        'size'       => 'large',
        'label'      => 'Отправить заявку на кастинг',
        'htmlOptions' => array(
            'style' => 'display:none;',
            'class' => 'finish',
        ),
    ));
    ?>
</div>
<div style="float:left">
    <!--input type="button" class="btn button-first" name="first" value="First" /-->
    <input type="button" class="btn btn-large button-previous" name="previous" value="Назад" />
</div><br /><br />