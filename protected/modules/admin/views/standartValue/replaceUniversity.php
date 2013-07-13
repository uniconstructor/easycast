<?php

$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Редактор стандартных значений' => array('/admin/standartValue/index', 'class'=>$class, 'type'=>$type),
    'Замена одного ВУЗа другим',
);

$universities = QUniversityInstance::model()->getUniversityList($type);
?>

<h1>Замена ВУЗа "<?php echo $model->name; ?>"</h1>
<h4>Этот ВУЗ будет выбранным в выпадающем списке, после чего удален. Все анкеты будут автоматически обновлены.</h4>

<?php 
echo CHtml::beginForm('/admin/standartValue/replaceUniversity', 'post');
?>
<h5>
Заменить 
<b><?php echo $model->name; ?>[<?php echo $model->id; ?>]</b>
на: 
<?php 
    echo CHtml::hiddenField('id', $model->id);
    echo CHtml::hiddenField('class', $class);
    echo CHtml::hiddenField('type', $type);
    
    echo CHtml::dropDownList('newid', '', $universities);
    echo '&nbsp';
    echo CHtml::submitButton('Заменить', array('class' => 'btn btn-primary'));
?>
</h5>
<?php 
echo CHtml::endForm();
?>