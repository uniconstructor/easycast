<?php
/**
 * Список всех текущих съемок на странице "мои события"
 */
/* @var $this EventsAgenda */

$this->widget('bootstrap.widgets.TbThumbnails', array(
    'dataProvider' => $this->dataProvider,
    'itemView'     => '_event',
    'emptyText'    => 'nodata',
    'template'     => '{items}',
));
