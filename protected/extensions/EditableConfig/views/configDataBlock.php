<?php
/**
 * 
 */
/* @var $this ConfigData */
?>
<div class="row-fluid">
    <div class="span8">
        <h4>Настройка</h4>
        <?php
        // данные объекта настройки
        echo $this->configContent;
        ?>
    </div>
    <div class="span4">
        <h4>Возможные значения</h4>
        <?php
        // список стандартных значений
        echo $this->optionsContent;
        ?>
    </div>
</div>