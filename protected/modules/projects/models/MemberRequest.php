<?php

/**
 * Класс для работы с заявкой на участие в мероприятии
 */
class MemberRequest extends ProjectMember
{
    /**
     * @see ProjectMember::init()
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'condition' => "`status`='draft' OR `status`='pending'",
            'order'     => '`timecreated` DESC',
        );
    }
    
    /**
     * @see CActiveRecord::scopes()
     */
    public function scopes()
    {
        return array(
            'rejected' => array(
                'condition' => "`status` = '".self::STATUS_REJECTED."'",
            ),
            'draft'    => array(
                'condition' => "`status` = '".self::STATUS_DRAFT."'",
            ),
            'pending'  => array(
                'condition' => "`status` = '".self::STATUS_PENDING."'",
            ),
        );
    }
}