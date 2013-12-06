<?php

/**
 * Модель для отчета "коммерческое предложение"
 */
class ROffer extends Report
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::init()
     */
    public function init()
    {
        parent::init();
    }

    /**
     * (non-PHPdoc)
     * @see Report::beforeSave()
     */
    public function beforeSave()
    {
        if ( $this->isNewRecord )
        {
            $this->type = 'offer';
        }
        return parent::beforeSave();
    }

    /**
     * (non-PHPdoc)
     * @see CActiveRecord::afterSave()
     */
    public function afterSave()
    {
        parent::afterSave();
    }

    /**
     * (non-PHPdoc)
     * @see Report::collectData()
     * @var array $event
     */
    public function collectData($data)
    {
        return array(
            'email' => $data['email'],
        );
    }
}