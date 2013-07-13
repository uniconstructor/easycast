<?php

$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Редактор стандартных значений' => array('/admin/standartValue/index', 'class'=>$class, 'type'=>$type),
    'Замена одного значения другим',
);

$activities = Questionary::model()->getFieldVariants($type);
?>

<h1>Замена стандартного значения "<?php echo $model->translation; ?>"</h1>
<h4>Это значение будет заменено выбранным в выпадающем списке и после этого удалено. Все анкеты будут автоматически обновлены.</h4>

<?php 
echo CHtml::beginForm('/admin/standartValue/replaceActivityType', 'post');
?>
<h5>
Заменить 
<b><?php echo $model->translation; ?>[<?php echo $model->value; ?>]</b>
на: 
<?php 
    echo CHtml::hiddenField('id', $model->id);
    echo CHtml::hiddenField('class', $class);
    echo CHtml::hiddenField('type', $type);
    
    echo CHtml::dropDownList('newvalue', '', $activities);
    echo '&nbsp';
    echo CHtml::submitButton('Заменить', array('class' => 'btn btn-primary'));
?>
</h5>
<?php 
echo CHtml::endForm();
?>