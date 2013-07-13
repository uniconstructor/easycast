<?php
/* @var $this ProjectsController */

$this->breadcrumbs=array(
	ProjectsModule::t('projects'),
);
?>
<!--h1><?php // echo ProjectsModule::t('projects'); ?></h1-->

<div class="row span12">
<?php 

// выводим пользователей в каталоге
$this->widget('bootstrap.widgets.TbThumbnails', array(
    'dataProvider' => $dataProvider,
    'template'     => "{items}{pager}",
    'itemView'     => '_project',
    )
);

?>
</div>