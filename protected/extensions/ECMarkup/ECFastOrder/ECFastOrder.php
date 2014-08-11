<?php

/**
 * Виджет отображающий всплывающее окно с формой срочного заказа
 * (только код modal-окна, вызов его требуется вручную)
 */
class ECFastOrder extends CWidget
{
    /**
     * @var string - id формы срочного заказа
     */
    public $formid   = 'fast-order-form';
    /**
     * @var string - id всплывающего окна
     */
    public $modalid  = 'fastOrderModal';
    /**
     * @var string - id кнопки отправки формы
     */
    public $submitid = 'fastOrderSubmit';
    
    /**
     * Получить код всплывающего окна для оформления срочного заказа
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $data = new stdClass;
        $data->action   = Yii::app()->createAbsoluteUrl('site/placeFastOrder') ;
        $data->formid   = $this->formid;
        $data->header   = Yii::t('coreMessages', 'place_order');
        $data->modalid  = $this->modalid;
        $data->submitid = $this->submitid;
        $data->orderSuccessMessage = Yii::t('coreMessages', 'place_order_success');
        $data->ajaxSuccessScript   = $this->getOrderAjaxSuccessScript($data);
        
        $model = new FastOrder;
        // внутри всплывающего окна отображаем форму срочного заказа
        $this->render('_fastorder', array('data'=>$data,'model'=>$model));
    }
    
    /**
     * Получить скрипт для отображения сообщения после отправки формы срочного заказа
     * @param stdClass $data - данные для фомирования скрипта с сообщением
     * @return string
     */
    protected function getOrderAjaxSuccessScript($data)
    {
        // Создаем сообщение об успешной отправке заказа
        $message = '<div class="alert alert-success">'.$data->orderSuccessMessage.'</div>';
        $message .= '<div style="text-align:center;">';
        $message .= $this->widget('bootstrap.widgets.TbButton', array(
            'type'  => 'primary',
            'label' => Yii::t('coreMessages', 'close'),
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        ), true);
        $message .= '</div>';
    
        $script = 'function(data){
            if ( data.status == "success" )
            {
                $(".modal-body").html(\''.$message.'\');
                $(".modal-footer").html("");
            }else
            {
                $.each(data, function(key, val) {
                    $("#'.$data->formid.' #"+key+"_em_").text(val);
                    $("#'.$data->formid.' #"+key+"_em_").show();
                });
            }
        }';
    
        return $script;
    }
}