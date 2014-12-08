<?php
/**
 * 
 */
/* @var $this  ECAccordion */
/* @var $block array */
/* @var $id    string */
// группа блоков
echo CHtml::openTag('div', array('class' => 'accordion-group'));
// заголовок
echo CHtml::openTag('div', array('class' => 'accordion-heading'));
echo CHtml::openTag('a', $titleHtmlOptions);
echo '<b>'.$block['title'].'</b>';
echo CHtml::closeTag('a');
echo CHtml::closeTag('div');
// содержимое
echo CHtml::openTag('div', $contentHtmlOptions);
echo CHtml::openTag('div', array('class' => 'accordion-inner'));
echo $block['content'];
echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
// конец группы
echo CHtml::closeTag('div');