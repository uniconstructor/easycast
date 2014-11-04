<?php
/**
 * Верстка общего списка всех настроек (обертка)
 */
/* @var $this EditableConfig */
?>
<div class="title-page">
    <h4 class="title">Настройки</h4>
</div>
<div class="row-fluid">
    <div class="span12">
        <?php
        switch ( $this->display )
        {
            // выводим каждую настройку отдельным блоком
            case 'full': 
                foreach ( $this->configItems as $config )
                {
                    $this->getDataWidget($config);
                }
            break;
            // выводим все настройки общим списком
            case 'short': 
                $this->widget('bootstrap.widgets.TbDetailView', array(
                    'data'       => $this->getConfigListData(),
                    'attributes' => $this->getConfigListAttributes(),
                ));
            break;
        }
        ?>
    </div>
</div>