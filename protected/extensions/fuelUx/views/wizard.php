<?php
/**
 * 
 */
/* @var $this FuelWizard */

// контейнер виджета (без него не будут работать стили библиотеки fuelUx)
echo CHtml::openTag('div', array('class' => 'fuelux'));

// верхняя часть со списком шагов
echo CHtml::openTag('div', $this->htmlOptions);
echo CHtml::openTag('ul', array('class' => 'steps'));

$completed = true;
$count     = 1;
foreach ( $this->steps as $name => $step )
{
    $itemOptions = array(
        'data-target' => '#'.$name,
    );
    $spanOptions = array(
        'class' => 'badge',
    );
    if ( $name === $this->activeStep )
    { // выделим текущий активный шаг
        $itemOptions['class']  = 'active';
        $spanOptions['class'] .= ' badge-info';
        $completed = false;
    }
    if ( $completed )
    { // отметим пройденный шаг
        $itemOptions['class'] .= 'complete';
    }
    // один шаг процесса
    echo CHtml::openTag('li', $itemOptions);
    echo CHtml::tag('span', $spanOptions, $count);
    echo $step['title'];
    echo CHtml::tag('span', array('class' => 'chevron'));
    echo CHtml::closeTag('li');
    
    $count++;
}
// конец верхней части
echo CHtml::closeTag('ul');
echo CHtml::closeTag('div');

// нижняя часть с содержимым, обновляется по AJAX
echo CHtml::openTag('div', array('class' => 'step-content'));
foreach ( $this->steps as $name => $step )
{
    $divOptions = array(
        'id   ' => $name, 
        'class' => 'step-pane',
    );
    if ( $name === $this->activeStep )
    {
        $divOptions['class'] .= ' active';
    }
    // один шаг процесса 
    echo CHtml::openTag('div', $divOptions);
    echo $step['content'];
    echo CHtml::closeTag('div');
}
// кнопки
if ( $this->displayButtons )
{
    echo CHtml::openTag('div', array('class' => 'actions'));
    $this->widget('bootstrap.widgets.TbButton', $this->prevButtonOptions);
    $this->widget('bootstrap.widgets.TbButton', $this->nextButtonOptions);
    echo CHtml::closeTag('div');
}

// конец содержимого
echo CHtml::closeTag('div');


// конец контейнера для wizard
echo CHtml::closeTag('div');
?>
<script src="https://fuelcdn.com/fuelux/2.3/loader.min.js"></script>