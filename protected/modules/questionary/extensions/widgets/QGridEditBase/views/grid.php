<?php
/**
 * Отображение таблицы со списком записей и всплывающего modal-окна с формой добавления новой записи
 */
/* @var $this QEditFilms */

// виджет с таблицей
$grid = $this->widget(
    'bootstrap.widgets.TbExtendedGridView',
    array(
        'type'         => 'striped bordered',
        'dataProvider' => new CActiveDataProvider($this->modelClass, array(
            'criteria' => $this->getGridCriteria(),
            'pagination' => false,
        )),
        'template'    => "{items}",
        'columns'     => $this->getTableColumns(),
        // @todo в виджете TbExtendedGridView невозможно задать id
        //       Изменить селектор после того как проблема будет решена
        'htmlOptions' => array('class' => $this->rowIdPrefix.'table grid-view'),
        'rowHtmlOptionsExpression' => 'array("id" => "'.$this->rowIdPrefix.'".$data->id);',
        'id' => $this->rowIdPrefix.'table',
    )
);

// кнопка добавления новой записи
$this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'  => 'link',
        'type'        => 'success',
        'size'        => 'large',
        'label'       => $this->addButtonLabel,
        'icon'        => 'plus white',
        'url'         => '#',
        'htmlOptions' => array(
            'id'          => $this->addButtonId,
            'class'       => 'pull-right',
            'data-toggle' => 'modal',
            'data-target' => '#'.$this->modalId,
        )
    )
);
