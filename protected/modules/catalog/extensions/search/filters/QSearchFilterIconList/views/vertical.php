<?php
/**
 * Кнопки в один столбец
 */
/* @var $this QSearchFilterIconList */
/* @var $sections CatalogSection[] */
foreach ( $sections as $section )
{
    $activeButtonClass = 'btn-block';
    $checkedValue      = '';
    if ( isset($data['sections']) AND in_array($section->id, $data['sections']) )
    {
        $activeButtonClass .= ' btn-primary';
        $checkedValue       = 'checked="checked"';
    }
    // выводим кнопку раздела
    $this->render('_button', array(
        'section'           => $section,
        'activeButtonClass' => $activeButtonClass,
        'checkedValue'      => $checkedValue,
    ));
}
?>