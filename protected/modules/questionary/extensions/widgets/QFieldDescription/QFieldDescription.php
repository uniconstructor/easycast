<?php

/**
 * Виджет для отображения пояснения к одному полю формы
 */
class QFieldDescription extends CWidget
{
    /**
     * @var string - название поля или раздела формы, к которому нужно вывести пояснение
     */
    public $field;
    
    /**
     * @var string - тип (цвет) пояснения - основано на свойстве компонента alert в twitter bootstrap
     */
    public $type = 'alert';

    public function run()
    {
        if ( $message = $this->getMessage($this->field) )
        {
            echo '<div class="alert alert-'.$this->type.'">'.$message.'</div>';
        }
    }
    
    /**
     * Получить текст описания поля
     * @param string $field - название поля или раздела формы
     * @return string -
     */
    protected function getMessage($field)
    {
        $message = '';
        switch ($field)
        {
            // для фотографий нужна особая верстка - чтобы лучше читалось
            case 'photos': $message = $this->getPhotosMessage(); break;
            // для размера оплаты труда тоже более подробное описание
            case 'salary': $message = $this->getSalaryMessage(); break;
            default: $message = QuestionaryModule::t($field.'_caption');
        }
        
        return $message;
    }
    
    protected function getPhotosMessage()
    {
        $message = '';
        $message .= QuestionaryModule::t('photos_caption');
        $message .= '<ul>';
        $message .= '<li>'.QuestionaryModule::t('photos_caption_actor').'</li>';
        $message .= '<li>'.QuestionaryModule::t('photos_caption_model').'</li>';
        $message .= '<li>'.QuestionaryModule::t('photos_caption_twin').'</li>';
        $message .= '<li>'.QuestionaryModule::t('photos_caption_photomodel').'</li>';
        $message .= '<li>'.QuestionaryModule::t('photos_caption_dancer').'</li>';
        $message .= '<li>'.QuestionaryModule::t('photos_caption_athlete').'</li>';
        $message .= '<li>'.QuestionaryModule::t('photos_caption_all').'</li>';
        $message .= '</ul>';
        $message .= QuestionaryModule::t('photos_caption_final');
        
        return $message;
    }
    
    protected function getSalaryMessage()
    {
        $message = '';
        $message .= QuestionaryModule::t('salary_caption');
        $message .= '<ul>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_2').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_3').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_4').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_5').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_6').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_7').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_8').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_9').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_10').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_11').'</li>';
        $message .= '<li>'.QuestionaryModule::t('salary_caption_12').'</li>';
        $message .= '</ul>';
        $message .= QuestionaryModule::t('salary_caption_final');
        
        return $message;
    }
}