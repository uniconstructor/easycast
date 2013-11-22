<?php
/**
 * Форма добавления сложного поля в анкету (во всплывающем окне)
 * Структура общая для всех дочерних виджетов
 * 
 * @todo языковые строки
 */
/* @var $form TbActiveForm */
/* @var $this QEditFilms */
/* @var $model QFilmInstance */

// отображаем всплывающее окно
$this->beginWidget('bootstrap.widgets.TbModal', array('id' => $this->modalId));

// отображаем форму
$form = $this->beginWidget('TbActiveForm', array(
    'id' => $this->formId,
    'enableAjaxValidation'   => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => true,
    ),
    'action' => $this->createUrl,
));
?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3><?= $this->modalHeader; ?></h3>
</div>

<div class="modal-body">
    <?php
    // отображаем все поля формы добавления новой записи
    $this->renderFormFields($form);
    ?>
</div>

<div class="modal-footer">
    <?php 
    // кнопка добавления записи
    $this->widget('bootstrap.widgets.TbButton', array(
          'buttonType'  => 'ajaxSubmit',
          'type'        => 'success',
          'label'       => Yii::t('coreMessages', 'add'),
          'url'         => $this->createUrl,
          'ajaxOptions' => array(
              //'beforeSend' => new CJavaScriptExpression("function(){\$('#registration-form').yiiactiveform().validate(); return false;}"),
              //'beforeSend' => new CJavaScriptExpression("function(){console.log(\$('#{$this->formId}').contents()); return false;}"),
              'success'  => "function (data, status) {
                      \$('#{$this->modalId}').modal('hide');
                      data = \$.parseJSON(data);
                      {$this->createAfterAddJs()}
                  }",
              'type'     => 'post',
              'error'    => "function(data, status) {
                  var message = '';
                  console.log(data);
                  if ( data.responseText )
                  {
                      message = ': ' + data.responseText;
                  }
                  alert('Ошибка при сохранении данных' + message);
                  return false;
              }",
              'url'      => $this->createUrl,
          ),
    ));
    // закрыть окно
    $this->widget('bootstrap.widgets.TbButton', array(
        'label'       => Yii::t('coreMessages', 'cancel'),
        'htmlOptions' => array('data-dismiss' => 'modal', 'class' => 'pull-left'),
    )); 
    ?>
</div>
<?php
// конец формы 
$this->endWidget($this->formId);
// конец окна
$this->endWidget($this->modalId);
