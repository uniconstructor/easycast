<?php
/**
 * Список всех текущих съемок на странице "мои события"
 */
/* @var $this EventsAgenda */

$this->widget('bootstrap.widgets.TbThumbnails', array(
    'dataProvider' => $this->dataProvider,
    'itemView'     => '_thumbnailEvent',
    'emptyText'    => 'nodata',
    'template'     => '{items}',
));
