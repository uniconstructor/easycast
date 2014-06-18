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

$categories = Category::model()->forParent($parentId)->findAll();
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
                if ( $parentId == 5 )
                {// в разделе доп. полей заявки можно увидеть отредактировать весь список полей
                    echo '<br><h2>Все дополнительные поля:</h2><br>';
                    $this->widget('admin.extensions.EditExtraFields.EditExtraFields', array(
                        'categoryId' => $currentCategory->id,
                    ));
                }
            break;
            case 'sections':
                // редактор разделов анкеты
                $this->widget('admin.extensions.EditCatalogSections.EditCatalogSections', array(
                    'categoryId' => $currentCategory->id,
                ));
            break;
            case 'userfields':
                // редактор групп обязательных полей анкеты (шаблоны создания анкеты)
                $this->widget('admin.extensions.EditRequiredFields.EditRequiredFields', array(
                    'objectId'   => $currentCategory->id,
                    'objectType' => 'category',
                ));
            break;
            case 'extrafields':
                // редактор групп дополнительных ролей заявки
                echo '<br><h4>Добавить существующее поле</h4><br>';
                $this->widget('admin.extensions.EditExtraFieldInstances.EditExtraFieldInstances', array(
                    'objectId'   => $currentCategory->id,
                    'objectType' => 'category',
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