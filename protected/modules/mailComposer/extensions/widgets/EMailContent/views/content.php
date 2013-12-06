<?php
/**
 * Выводит таблицу-контейнер, содержащую в себе все сегменты письма
 * Добавляет отступы по 30px слева и справа от содержимого
 */
/* @var $this EMailContent */
?>
<tr id="simple-content-row">
    <td class="w640" width="640" bgcolor="#cbcac8" style="padding-bottom:10px;">
        <table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="w<?= $this->padding; ?>" width="<?= $this->padding; ?>"></td>
                    <td class="w580" width="580" bgcolor="#fff" style="border-radius:10px;">
                    <repeater> 
                        <?php 
                        $widgetPath = 'application.modules.mailComposer.extensions.widgets.EMailSegment.EMailSegment';
                        foreach ( $this->segments as $segment )
                        {// Выводим по очереди каждый сегмент текста письма, с картинкой, заголовком и текстом
                            $segment['padding'] = $this->padding;
                            $this->widget($widgetPath, $segment);
                        }
                        ?>
                    </repeater>
                    </td>
                    <td class="w<?= $this->padding; ?>" width="<?= $this->padding; ?>"></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>