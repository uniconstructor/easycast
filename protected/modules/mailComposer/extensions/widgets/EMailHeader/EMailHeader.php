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
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->header = trim($this->header);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->type == 'text' )
        {
            if ( ! $this->header )
            {// заголовок не задан - ничего не отображаем
                return;
            }
            $this->render('textHeader');
        }elseif ( $this->type == 'image' )
        {
            $imageUrl = ECPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('//images/mail-header.png'));
            $this->render('imageHeader', array('imageUrl' => $imageUrl));
        }
        
    }
}