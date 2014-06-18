<?php
/**
 * Отображение таблицы со списком записей и всплывающего modal-окна с формой добавления новой записи
 * 
 * @todo сделать все параметры TbExtendedGridView настраеваемыми (как TbButtonOptions)
 */
/* @var $this EditableGrid */

// виджет с редактируемой и дополняемой таблицей
$grid = $this->widget('bootstrap.widgets.TbExtendedGridView', array(
    // таблица должна подстраиваться поэ экран при просмотре с мобильного телефона
    'responsiveTable' => true,
    'type'            => 'striped bordered',
    // получаем изначальное содержимое таблицы
    // (список вузов, виды спорта и т. д. в зависимости от того что редактируем)
    'dataProvider'    => $this->createGridDataProvider(),
    'template'        => "{items}",
    // получаем настройки для всех колонок таблицы
    'columns'         => $this->getTableColumns(),
    // @todo в виджете TbExtendedGridView невозможно задать id
    //       Изменить селектор после того как проблема будет решена
    //       UPD: проблема вроде как была решена обновлением Yii (ошибка оригинального GridView) нужно проверить
    'htmlOptions'  => array('class' => $this->rowIdPrefix.'table grid-view'),
    'rowHtmlOptionsExpression' => 'array("id" => "'.$this->rowIdPrefix.'".$data->id);',
    'id'          => $this->rowIdPrefix.'table',
));

// кнопка добавления новой записи
$this->widget('bootstrap.widgets.TbButton', $this->addButtonOptions);
