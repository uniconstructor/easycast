<?php
/**
 * Главный файл для построения письма.
 * Из статического кода содержит только отступы сверху и снизу
 * @var EMailAssembler $this
 */
?>
<tr>
    <td class="w640" height="20" width="640"></td>
</tr>
<?php 
// Выводим полоску сверху
$this->displayTopBar();
?>
<?php 
// Выводим заголовок
$this->displayMainHeader();
?>
<?php 
// Выводим основное содержимое, разбивая его на блоки. Один блок - один виджет Segment
$this->displayContent();
?>
<tr>
    <td class="w640" height="15" width="640" bgcolor="#cbcac8"></td>
</tr>
<?php 
// Выводим нижнюю часть письма
$this->displayFooter();
?>
<tr>
    <td class="w640" height="60" width="640"></td>
</tr>