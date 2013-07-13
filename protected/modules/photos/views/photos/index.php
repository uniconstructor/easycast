<?php
/**
 * Список всех фотогалерей для пользователя
 * @todo добавить языковые строки
 */


if ( $galleryid )
{// если мы просматриваем текущую галерею - то добавит еще один уровень навигации
    $this->breadcrumbs = array(
        'Галерея' => array('/photos'),
        $galleryName,
    );
}else
{
    $this->breadcrumbs = array(
        'Галерея',
    );
}

?>

<h1>Галерея</h1>

<?php /*$this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
));*/ 

$this->widget('application.modules.photos.extensions.PhotoGalleryWidget.PhotoGalleryWidget', 
    array(
        'galleryId' => $galleryid,
));


?>
