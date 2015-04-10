<?php

/**
 * Класс пользователя веб-приложения с дополнениями easyCast
 * 
 * @property User        $model
 * @property Questionary $questionary
 */
class EcWebUser extends RWebUser
{
    /**
     * Получить модель с данными текущего авторизованного пользователя
     * 
     * @return User|null
     */
    public function getModel()
    {
        if ( $this->isGuest )
        {
            return null;
        }
        return User::model()->findByPk($this->id);
    }
    
    /**
     * Получить анкету текущего авторизованного пользователя
     * 
     * @return Questionary|null
     */
    public function getQuestionary()
    {
        if ( ! $user = $this->getModel() )
        {
            return null;
        }
        if ( $user->questionary instanceof Questionary )
        {
            return $user->questionary;
        }
    }
    
    /**
     * @todo Получить данные текущего авторизованного заказчика 
     * 
     * @return Customer
     */
    public function getCustomer()
    {
        
    }
    
    /**
     * @todo Получить данные текущего авторизованного сотрудника easyCast 
     * 
     * @return Customer
     */
    public function getManager()
    {
        
    }
    
    /**
     * Получить id анкеты текущего авторизованного пользователя
     *
     * @return int|null
     */
    public function getQuestionaryId()
    {
        if ( $questionary = $this->getQuestionary() )
        {
            return $questionary->id;
        }
    }
    
    /**
     * Получить ФИО из анкеты текущего авторизованного пользователя
     *
     * @return string|null
     */
    public function getFullName()
    {
        if ( $questionary = $this->getQuestionary() )
        {
            return $questionary->firstname.' '.$questionary->lastname;
        }
        return 'Гость';
    }
    
    /**
     * Получить url изображения с аватаром текущего авторизованного пользователя
     *
     * @param  string $size     - размер аватара
     * @param  bool   $absolute - url картинки или заглушку, если у пользователя пока нет ни одной фотографии
     * @return string - url картинки или заглушку, если у пользователя пока нет ни одной фотографии
     *                  или пользователь является гостем
     *                  
     * @todo отдельная иконка для гостя
     * @todo получение аватарок заказчиков и админов
     */
    public function getAvatarUrl($size='small')
    {
        $guestAvatar = Yii::app()->getBaseUrl(true).'/images/nophoto.png';
        $noAvatar    = Yii::app()->getBaseUrl(true).'/images/nophoto.png';
        if ( $this->isGuest )
        {
            return $guestAvatar;
        }
        if ( ! $questionary = $this->getQuestionary() )
        {
            return $noAvatar;
        }
        return $questionary->getAvatarUrl($size);
    }
    
    /**
     * Получить изображение с аватаром текущего авторизованного пользователя
     * 
     * @param  string $size
     * @param  array $imageOptions
     * @return NULL|string
     */
    public function getAvatarImage($size='small', $imageOptions=array())
    {
        if ( ! $src = $this->getAvatarUrl($size) )
        {
            return null;
        }
        if ( ! isset($imageOptions['alt']) )
        {
            $alt = $this->getFullName();
        }else
        {
            $alt = $imageOptions['alt'];
            unset($imageOptions['alt']);
        }
        return CHtml::image($src, $alt, $imageOptions);
    }
    
    /**
     * Получить изображение с аватаром текущего авторизованного пользователя
     * которое одновременно является ссылкой на просмотр анкеты участника   
     * 
     * @param  string $size
     * @param  array  $linkOptions
     * @param  array  $imageOptions
     * @return string
     */
    public function getAvatarLink($size='small', $linkOptions=array(), $imageOptions=array())
    {
        $image = $this->getAvatarImage($size, $imageOptions);
        if ( ! $url = $this->getProfileUrl() )
        {
            $url = Yii::app()->createUrl('/easy');
        }
        return CHtml::link($image, $url, $linkOptions);
    }
    
    /**
     * Получить ссылку на страницу профиля текущего авторизованного пользователя содержащую его полное имя
     * 
     * @return string
     */
    public function getFullNameLink()
    {
        $name = $this->getFullName();
        if ( ! $url = $this->getProfileUrl() )
        {
            $url = Yii::app()->createUrl('/easy');
        }
        return CHtml::link($name, $url);
    }
    
    /**
     * Получить адрес страницы профиля текущего авторизованного пользователя
     * 
     * @return string|null
     */
    public function getProfileUrl()
    {
        if ( ! $id = $this->getQuestionaryId() )
        {
            return null;
        }
        return Yii::app()->createUrl('/questionary/questionary/view', array('id' => $id));
    }
}