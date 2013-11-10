<?php
/**
 * Самая нижняя часть письма
 * @var EMailFooter $this
 */
?>
<tr>
    <td class="w640" width="640">
        <table id="footer" class="w640" width="640" bgcolor="#0099cc"
            border="0" cellpadding="0" cellspacing="0" style="border-radius:0px 0px 10px 10px;">
            <tbody>
                <tr>
                    <td class="w30" width="30"></td>
                    <td class="w580 h0" height="30" width="360"></td>
                    <td class="w0" width="60"></td>
                    <td class="w0" width="160"></td>
                    <td class="w30" width="30"></td>
                </tr>
                <tr>
                    <td class="w30" width="30"></td>
                    <td class="w380" valign="top" width="380">
                        <?php 
                        // выводим стандартную подпись
                        $this->displaySignature();
                        ?>
                        <?php 
                        // Выводим служебные ссылки
                        $this->displayLinks();
                        ?>
                    </td>
                    <td class="hide w0" width="10"></td>
                    <td class="hide w0" valign="top" width="190">
                        <?php 
                        // Выводим контакты
                        $this->displayContacts();
                        ?>
                    </td>
                    <td class="w30" width="30"></td>
                </tr>
                <tr>
                    <td class="w30" width="30"></td>
                    <td class="w580 h0" height="15" width="360"></td>
                    <td class="w0" width="60"></td>
                    <td class="w0" width="160"></td>
                    <td class="w30" width="30"></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
