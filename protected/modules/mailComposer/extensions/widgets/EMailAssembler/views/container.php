<?php
/**
 * Главный файл для построения письма.
 * Из статического кода содержит только отступы сверху и снизу
 */
/* @var $this EMailAssembler */
?>
<tr>
    <td class="w640" height="20" width="640"></td>
</tr>
<?php 
// Выводим полоску сверху
$this->displayTopBar();
// Выводим заголовок
$this->displayMainHeader();
// Выводим основное содержимое, разбивая его на блоки. Один блок - один виджет Segment
$this->displayContent();
// Выводим нижнюю часть письма
$this->displayFooter();
?>
<tr>
    <td class="w640" height="60" width="640"></td>
</tr>