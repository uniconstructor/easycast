<?php
/**
 * Верстка общего списка всех настроек
 */
/* @var $this EditableConfig */
?>
<div class="title-page">
    <h4 class="title">Настройки</h4>
</div>
<div class="row-fluid">
    <div class="span12">
        <?php
        foreach ( $this->configItems as $config )
        {// выводим все настройки в виде виджетов
            $options = array(
                'config'    => $config,
                'deleteUrl' => $this->deleteUrl,
                'updateUrl' => $this->updateUrl,
                'createUrl' => $this->createUrl,
            );
            $this->getConfigWidget($config, $options);
        }
        ?>
    </div>
</div>