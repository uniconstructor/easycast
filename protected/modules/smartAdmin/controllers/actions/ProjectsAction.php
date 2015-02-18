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
            //$status = CHtml::tag('span', $labelOptions, $project->statusNode->label);
            $status = $project->statusNode->label;
            $data[] = array(
                'id'     => $project->id, 
                'name'   => CHtml::encode($project->name), 
                'type'   => $project->type, 
                'leader' => $project->leader ? $project->leader->fullname : Yii::t('zii', 'Not set'),
                'status' => $status,
            );
        }
        $html = $this->controller->widget('smartAdmin.extensions.DataTable.DataTable', array(
            'columns' => array(
                array(
                    'title' => 'id',
                    'data'  => 'id',
                ),
                array(
                    'title' => 'Название',
                    'data'  => 'name',
                ),
                array(
                    'title' => 'Тип',
                    'data'  => 'type',
                ),
                array(
                    'title' => 'Руководитель',
                    'data'  => 'leader',
                ),
                array(
                    'title' => 'Статус',
                    'data'  => 'status',
                ),
            ),
            'data' => $data,
        ), true);
        $this->controller->renderText($html);
    }
}