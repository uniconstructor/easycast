<?php
/**
 * Большой заголовок в начале письма
 */
?>
<tr>
    <td id="header" class="w640" width="640" bgcolor="#1f011f"
        align="center">
        <table class="w640" width="640" border="0" cellpadding="0"
            cellspacing="0">
            <tbody>
                <tr>
                    <td class="w30" width="30"></td>
                    <td class="w580" height="30" width="580"></td>
                    <td class="w30" width="30"></td>
                </tr>
                <tr>
                    <td class="w30" width="30"></td>
                    <td class="w580" width="580">
                        <div id="headline" align="center">
                            <p>
                                <singleline label=""><h1 style="color:#ffffff;"><?= $this->header; ?></h1></singleline>
                            </p>
                        </div>
                    </td>
                    <td class="w30" width="30"></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td class="w640" height="30" width="640" bgcolor="#121417"></td>
</tr>