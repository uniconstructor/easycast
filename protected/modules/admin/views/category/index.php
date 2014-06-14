<?php
/**
 * Редактирование списка категорий
 */
/* @var $this CategoryController */

if ( $currentCategory = Category::model()->findByPk($parentId) )
{
    $title = $currentCategory->name;
}else
{
    $title = 'Категории и группы';
}

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Категории и группы',
);
$this->menu = array();

$systemCategories = Category::model()->childrenFor($parentId)->findAll();
if ( $currentCategory->parentid )
{
    $this->menu[] = array(
        'label' => 'Наверх',
        'url'   => array('/admin/category/index', 'parentId' => $currentCategory->parentid),
    );
}
foreach ( $systemCategories as $category )
{
    $this->menu[] = array(
        'label' => $category->name, 
        'url'   => array('/admin/category/index', 'parentId' => $category->id),
    ); 
}

?>

<h1><?= $title; ?></h1>

<div class="row-fluid">
    <?php 
    // редактор для списка категорий
    $this->widget('admin.extensions.EditCategories.EditCategories', array(
        'parentId' => $parentId,
    ));
    ?>
</div>