<?php

/**
 * Блок письма с информацией о руководителе проекта
 */
class EMailManagerInfo extends CWidget
{
    /**
     * @var User
     */
    public $manager;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $anyTimeImage   = EcPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('/images/24-7-365.png'));
        $iTakeCareImage = EcPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('/images/i-take-care.png'));
        
        $mainImageUrl   = $this->manager->questionary->getAvatarUrl('catalog');
        $managerPhoto   = EcPurifier::getImageProxyUrl($mainImageUrl);
        
        $position = 'руководитель проектов';
        if ( $this->manager->email === 'ceo@easycast.ru' )
        {
            $position = 'основатель и управляющий';
        }
        
        $this->render('manager', array(
            'managerPhoto'   => $managerPhoto,
            'anyTimeImage'   => $anyTimeImage,
            'iTakeCareImage' => $iTakeCareImage,
            'fullName'       => $this->manager->fullname,
            'position'       => $position,
            'phone'          => $this->manager->questionary->mobilephone,
            'email'          => $this->manager->email,
            'fbUrl'          => $this->manager->questionary->fbprofile,
        ));
    }
}