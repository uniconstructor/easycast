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

$categories = Category::model()->childrenFor($parentId)->findAll();
if ( $currentCategory->parentid )
{
    $this->menu[] = array(
        'label' => 'Наверх',
        'url'   => array('/admin/category/index', 'parentId' => $currentCategory->parentid),
    );
}
foreach ( $categories as $category )
{
    $this->menu[] = array(
        'label' => $category->name, 
        'url'   => array('/admin/category/index', 'parentId' => $category->id),
    ); 
}
?>

<div class="container">
    <div class="row-fluid">
        <h1><?= $title; ?></h1>
        
        <?php
        switch ( $currentCategory->type )
        {
            case 'categories':
                // редактор для списка категорий
                $this->widget('admin.extensions.EditCategories.EditCategories', array(
                    'parentId' => $parentId,
                ));
            break;
            case 'sections':
                $this->widget('admin.extensions.EditCatalogSections.EditCatalogSections', array(
                    'categoryId' => $currentCategory->id,
                ));
            break;
            case 'userfields':
                $this->widget('admin.extensions.EditRequiredFields.EditRequiredFields', array(
                    'objectId'   => $currentCategory->id,
                    'objecttype' => 'category',
                ));
            break;
            case 'extrafields':
                $this->widget('admin.extensions.EditExtraFieldInstances.EditExtraFieldInstances', array(
                    'objectId'   => $currentCategory->id,
                    'objecttype' => 'category',
                ));
            break;
            case 'tags':
                
            break;
        } 
        
        ?>
    </div>
    <?php 
    // modal-окна с формами для EditableGrid элементов
    $clips = Yii::app()->getModule('admin')->formClips;
    foreach ( $clips as $clip )
    {
        echo $this->clips[$clip];
    }
    ?>
</div>