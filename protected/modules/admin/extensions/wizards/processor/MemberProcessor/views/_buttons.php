<?php
/**
 * Кнопки "последняя заявка" / "следующая заявка"
 */
/* @var $this MemberProcessor */
?>

<div class="row-fluid">
    <div class="span4">
        <?php
        // кнопка перехода к предыдущей заявке
        if ( $this->lastMemberId )
        {
            $url = $this->getReturnUrl(array('cmid' => $this->lastMemberId));
            echo CHtml::link('Предыдущая заявка', $url, array(
                'class' => 'btn btn-large',
            ));
        }
        ?>
    </div>
    <div class="span4 offset4 text-right">
        <?php 
        // кнопка перехода к следующей заявке
        $url = $this->getReturnUrl(array('lmid' => $member->id));
        echo CHtml::link('Следующая заявка без категории', $url, array(
            'class' => 'btn btn-large btn-primary',
        ));
        ?>
    </div>
</div>