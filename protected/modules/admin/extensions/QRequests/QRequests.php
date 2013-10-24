<?php

/**
 * Список анкет для модерации
 */
class QRequests extends CWidget
{
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $elements = array();
        
        // Получаем все анкеты, которые только что созданы или находятся в статусе "надо проверить"
        $questionaries = Questionary::model()->findAll("status IN ('draft', 'pending') ORDER BY `timemodified` DESC");
        foreach ( $questionaries as $questionary )
        {
            // кнопка подтверждения данных анкеты
            $approveUrl = $this->createSetStatusUrl('active', $questionary->id);
            $approveAjaxOptions = $this->createAjaxOptions('active', $questionary->id);
            $approveButton = CHtml::ajaxButton('Одобрить анкету', $approveUrl, $approveAjaxOptions, array('class' => 'btn btn-success'));
            
            // кнопка отправки анкеты на доработку
            $rejectUrl = $this->createSetStatusUrl('rejected', $questionary->id);
            $rejectAjaxOptions = $this->createAjaxOptions('rejected', $questionary->id);
            $rejectButton = CHtml::ajaxButton('Отправить на доработку', $rejectUrl, $rejectAjaxOptions, array('class' => 'btn btn-danger'));
            
            // создаем ссылку на страницу пользователя
            $nameUrl = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $questionary->id));
            
            $element = array();
            $element['id'] = $questionary->id;
            // Текстовое поле для комментария администратора при одобрении
            $approveText = CHtml::textArea(
                                'approveMessage['.$questionary->id.']', '', 
                                array('id' => 'approveMessage_'.$questionary->id));
            // Текстовое поле для комментария администратора при отправке на доработку
            $rejectText = CHtml::textArea(
                                'rejectMessage['.$questionary->id.']', '', 
                                array('id' => 'rejectMessage_'.$questionary->id));
            
            if ( ! isset($questionary->user->fullname) )
            {// битая анкета (не привязана к пользователю)
                // @todo сделать нормальную обработку таких ошибок
                continue;
            }
            
            $actions = '';
            $actions .= '<table style="width:100%" id="actions_'.$questionary->id.'">';
            $actions .= '<tr>';
            $actions .= '<td>'.$approveButton.'</td>';
            $actions .= '<td>Дополнительные комментарии:<br>'.$approveText.'</td></tr>';
            $actions .= '<tr>';
            $actions .= '<td>'.$rejectButton.'</td>';
            $actions .= '<td>Причина:<br>'.$rejectText.'</td>';
            $actions .= '</tr>';
            $actions .= '</table>';
            
            $element['name'] = CHtml::link($questionary->user->fullname, $nameUrl, array('target' => '_blank'));
            $element['actions'] = $actions;
            $elements[] = $element;
            unset($actions);
        }
        
        // составляем таблицу со списком анкет для премодерации
        $arrayProvider = new CArrayDataProvider($elements,
            array('pagination' => array('pageSize' => 100)));
        
        $this->widget('bootstrap.widgets.TbGridView', array(
            'type'         => 'striped bordered condensed',
            'dataProvider' => $arrayProvider,
            'template'     => "{summary}{items}{pager}",
            'columns' => array(
                array(
                    'name'   => 'name',
                    'header' => QuestionaryModule::t('name'),
                    'type'   => 'raw',
                ),
                array(
                    'name'   => 'actions',
                    'header' => ProjectsModule::t('status'),
                    'type'   => 'raw',
                ),
            ),
        ));
    }
    
    protected function createAjaxOptions($status, $id)
    {
        $buttonUrl = $this->createSetStatusUrl($status, $id);
        // @todo добавить подтверждение перед проверкой или отклонением
        $options = array(
            'url'  => $buttonUrl,
            'data' => array(
                'id'     => $id,
                'status' => $status,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
            //'dataType' => 'json',
            'type'   => 'post',
            'success'    => $this->createAjaxSuccessJS($status, $id),
        );
        
        if ( $status == 'active' )
        {
            $options['data']['message'] = "js:function() {return $('#approveMessage_{$id}').val();}";
        }else
        {
            $options['data']['message'] = "js:function() {return $('#rejectMessage_{$id}').val();}";
        }
        
        return $options;
    }
    
    protected function createAjaxSuccessJS($status, $id)
    {
        if ( $status == 'active' )
        {
            $message = '<div class="alert alert-success">Анкета одобрена</div>';
        }else
        {
            $message = '<div class="alert alert-block">Анкета отправлена на доработку</div>';
        }
        return "function(data){
            var message = '{$message}';
            $('#actions_{$id}').after(message);
            $('#actions_{$id}').hide();
        }";
    }
    
    protected function createSetStatusUrl($status, $id)
    {
        return Yii::app()->createUrl('/admin/questionary/setStatus', 
            array(
                'id'     => $id,
                'status' => $status,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
        ));
    }
}