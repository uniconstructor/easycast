<?php
/**
 * Верхняя полоска письма
 */
?>
<tr>
    <td class="w640" width="640">
        <table id="top-bar" class="w640" width="640" bgcolor="#1f011f"
            border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="w15" width="15"></td>
                    <td class="w325" valign="middle" width="350"
                        align="left">
                        <table class="w325" width="350" border="0"
                            cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="w325" height="8"
                                        width="350">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <?php // выводим служебные ссылки 
                            $this->displayLinks();
                        ?>
                        <table class="w325" width="350" border="0"
                            cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="w325" height="8"
                                        width="350">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="w30" width="30"></td>
                    <td class="w255" valign="middle" width="255"
                        align="right">
                        <table class="w255" width="255" border="0"
                            cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="w255" height="8"
                                        width="255"></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php // Выводим кнопки соцсетей
                            $this->displaySocial();
                        ?>
                        <table class="w255" width="255" border="0"
                            cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="w255" height="8"
                                        width="255">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="w15" width="15"></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>