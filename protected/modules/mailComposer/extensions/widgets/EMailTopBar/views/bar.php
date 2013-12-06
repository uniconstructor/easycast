<?php
/**
 * Верхняя полоска письма
 */
/* @var $this EMailTopBar */
?>
<tr>
    <td class="w640" width="640">
        <table id="top-bar" class="w640" width="640" bgcolor="#09c" style="border-radius:10px 10px 0px 0px;"
            border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td class="w15" width="15"></td>
                    <td class="w610" valign="middle" width="610" align="left">
                        <table class="w610" width="610" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="w610" height="8" width="610">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <?php // выводим служебные ссылки 
                            $this->displayLinks();
                        ?>
                        <table class="w610" width="610" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="w610" height="8" width="610">&nbsp;</td>
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