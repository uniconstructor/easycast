<?php
/**
 * Верстка виджета проверки соответствия анкеты критериям поиска
 */
/* @var $this SearchFilterHelper */

echo CHtml::beginForm(Yii::app()->createUrl($this->forceInviteUrl), 'post', array(
    'id' => $this->getFormId(),
));
?>
<div class="row-fluid" class="text-center">
    Проверить критерии поиска
</div>
<div class="row-fluid">
    <div class="row-fluid">
        <div class="span6" class="text-center">
            <?php
            // id роли
            echo CHtml::label('id роли', 'vacancyId');
            echo CHtml::textField('vacancyId', $this->vacancyId);
            ?>
            <div id="search-helper-vacancy-name-<?= $this->id; ?>"></div>
        </div>
        <div class="span6" class="text-center">
            <?php
            // id участника
            echo CHtml::label('id участника', 'questionaryId');
            echo CHtml::textField('questionaryId', $this->questionaryId);
            ?>
            <div id="search-helper-user-name-<?= $this->id; ?>"></div>
        </div>
    </div>
    <div class="row-fluid" class="text-center">
        <?php 
        // AJAX-кнопка проверки результатов
        $this->getAjaxButton('check');
        ?>
    </div>
    <div class="row-fluid">
        <div id="<?= $this->getResultId('check'); ?>" class="text-center">
            <?php 
            // таблица соответствия анкеты участника критериям роли
            $this->getDefaultResult();
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <?php 
            // AJAX-кнопка отправки приглашения
            $this->getAjaxButton('invite');
            ?>
            <div id="<?= $this->getResultId('invite'); ?>"></div>
        </div>
        <div class="span6">
            <?php
            // AJAX-кнопка подачи заявки от имени участника
            $this->getAjaxButton('subscribe');
            ?>
            <div id="<?= $this->getResultId('subscribe'); ?>"></div>
        </div>
    </div>
</div>
<?php 
echo CHtml::endForm();
?>