<?php
/**
 * Отображение таблицы со списком фильмов и всплывающего modal-окна с формой добавления фильма
 */
/* @var $this QEditFilms */

// виджет с таблицей
$this->widget(
    'bootstrap.widgets.TbExtendedGridView',
    array(
        'type'         => 'striped bordered',
        'dataProvider' => new CActiveDataProvider('QFilmInstance', array(
            'criteria' => array(
                'order'     => 'date DESC',
                'condition' => "`questionaryid` = '{$this->questionary->id}'",
            ),
            'pagination' => false,
        )),
        'template'    => "{items}",
        'columns'     => $this->getTableColumns(),
        // @todo в виджете TbExtendedGridView невозможно задать id
        //       Изменить селектор после того как проблема будет решена
        'htmlOptions' => array('class' => $this->rowIdPrefix.'table grid-view'),
        'rowHtmlOptionsExpression' => 'array("id" => "'.$this->rowIdPrefix.'".$data->id);',
    )
);

// кнопка добавления новой записи
$this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'  => 'link',
        'type'        => 'success',
        'size'        => 'large',
        'label'       => Yii::t('coreMessages', 'add'),
        'icon'        => 'plus white',
        'url'         => '#',
        'htmlOptions' => array(
            'id'          => 'add_film_instance',
            'class'       => 'pull-right',
            'data-toggle' => 'modal',
            'data-target' => '#'.$this->modalId,
        )
    )
);
