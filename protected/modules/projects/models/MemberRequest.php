<?php

/**
 * Класс для работы с заявкой на участие в мероприятии
 */
class MemberRequest extends ProjectMember
{
    /**
     * (non-PHPdoc)
     * @see ProjectMember::init()
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'condition' => "`status`='draft' OR `status`='pending'",
            'order'     => '`timecreated` DESC');
    }
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::scopes()
     */
    public function scopes()
    {
        return array(
            'rejected' => array(
                'condition'=>"`status` = ".self::STATUS_REJECTED."'",
            ),
            'draft' => array(
                'condition'=>"`status` = '".self::STATUS_DRAFT."'",
            ),
            'pending' => array(
                'condition'=>"`status` = '".self::STATUS_PENDING."'",
            ),
        );
    }
}