<?php

/**
 * Заголовок письма большими буквами (например "Приглашение")
 */
class EMailHeader extends CWidget
{
    /**
     * @var string - тип заголовка (image|text)
     */
    public $type = 'image';
    /**
     * @var string - текст заголовка
     */
    public $header;
    /**
     * @var string - чей телефон показывать в шапке
     *               user - телефон для участников
     *               customer - телефон для заказчиков
     */
    public $target = 'user';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $this->header = trim($this->header);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->type === 'text' )
        {// у письма текстовая шапка
            if ( ! $this->header )
            {// заголовок не задан - ничего не отображаем
                return;
            }
            $this->render('textHeader');
        }elseif ( $this->type === 'image' )
        {// у письма шапка с изображением
            if ( $this->target === 'customer' )
            {// шапка с телефоном для заказчиков
                $imageUrl = ECPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('//images/mail-header-customer.png'));
            }else
            {// шапка с телефоном для участников
                $imageUrl = ECPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('//images/mail-header-user.png'));
            }
            $this->render('imageHeader', array('imageUrl' => $imageUrl));
        }
    }
}