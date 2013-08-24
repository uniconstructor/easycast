<?php

/**
 * Список отложенных анкет
 * @todo избавиться от дублирования кода
 */
class QDelayed extends CWidget
{
    public function run()
    {
        $elements = array();
        
        // Получаем все анкеты, которые только что созданы или находятся в статусе "надо проверить"
        $questionaries = Questionary::model()->findAll("status IN ('delayed')");
        foreach ( $questionaries as $questionary )
        {
            // создаем ссылку на страницу пользователя
            $nameUrl = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $questionary->id));
            $element = array();
            $element['id'] = $questionary->id;
            $element['name'] = CHtml::link($questionary->user->fullname, $nameUrl);
            $element['comment'] = $questionary->admincomment;
            $elements[] = $element;
        }
        
        // составляем таблицу со списком анкет для премодерации
        $arrayProvider = new CArrayDataProvider($elements);
        $this->widget('bootstrap.widgets.TbGridView', array(
            'type'         => 'striped bordered condensed',
            'dataProvider' => $arrayProvider,
            'template'=>"{items}{pager}",
            'columns'=>array(
                array('name'=>'name', 'header'=>QuestionaryModule::t('name'), 'type' => 'html'),
                array('name'=>'comment', 'header'=>'Комментарий'),
            ),
        ));
    }
}