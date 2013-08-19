<?php
/**
 * Выводит таблицу-контейнер, содержащую в себе все сегменты письма
 * @var EMailContent $this
 */
?>
<tr id="simple-content-row">
    <td class="w640" width="640" bgcolor="#121417">
        <table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="w30" width="30"></td>
                    <td class="w580" width="580">
                    <repeater> 
                        <?php 
                        $widgetPath = 'application.modules.mailComposer.extensions.widgets.EMailSegment.EMailSegment';
                        foreach ( $this->segments as $segment )
                        {// Выводим по очереди каждый сегмент текста письма, с картинкой, заголовком и текстом
                            $this->widget($widgetPath, $segment);
                        }
                        ?>
                    </repeater>
                    </td>
                    <td class="w30" width="30"></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>