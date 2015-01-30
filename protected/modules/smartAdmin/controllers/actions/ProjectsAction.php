<?php

/**
 * Страница списка проектов
 */
class ProjectsAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        $projects = Project::model()->findAll();
        $data     = array();
        foreach ( $projects as $project )
        {/* @var $project Project */
            $labelOptions = array(
                'class' => 'label',
                'style' => 'background-color:'.$project->statusNode->metadata['bgColor'].';',
            );
            $status = CHtml::tag('span', $labelOptions, $project->statusNode->label);
            $data[] = array(
                $project->id, 
                $project->name, 
                $project->type, 
                $project->leader ? $project->leader->fullname : Yii::t('zii', 'Not set'),
                $status,
            );
        }
        $html = $this->controller->widget('smartAdmin.extensions.DataTable.DataTable', array(
            'columns' => array(
                array(
                    'title' => 'id',
                    //'data' => 'id',
                ),
                array(
                    'title' => 'Название',
                    //'data'  => 'name',
                ),
                array(
                    'title' => 'Тип',
                    //'data'  => 'name',
                ),
                array(
                    'title' => 'Руководитель',
                    //'data'  => 'name',
                ),
                array(
                    'title' => 'Статус',
                    //'data'  => 'name',
                ),
            ),
            'data' => $data,
        ), true);
        $this->controller->renderText($html);
    }
}