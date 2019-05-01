@include('includes.email_header')

                    <!-- Intro -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="width:100%;">
                        <tr>
                            <td bgcolor="#f1f1f1">

                                <table width="660" border="0" cellspacing="0" cellpadding="0" align="center" class="scale" style="background-color:#FFFFFF;">
                                    <tr>
                                        <td bgcolor="#FFFFFF">
                                            <table width="600" border="0" cellspacing="0" cellpadding="0" align="center" style="font-family:Helvetica, Arial, sans-serif; font-size: 14px; color: #6f6e69;" class="scale">
                                                <tr>
                                                    <td>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="font-family:Helvetica, Arial, sans-serif; font-size: 14px; color: #6f6e69; width:100%" class="scale-inner">
                                                            <tr><td height="30"></td></tr>
                                                            <tr>
                                                                <td style="font-family:Helvetica, Arial, sans-serif; font-size: 18px; color: #53545e; font-weight:500;">Hi <?php echo isset($data['name'])?$data['name']:""; ?>,</td>
                                                            </tr>
                                                            <tr><td height="20"></td></tr>
                                                            <tr>
                                                                <td style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#333;line-height:1.6;">
                                                                    Your account has been <?php
                                                                    if ($data['status'] == 2) {
                                                                        echo "de-activated";
                                                                    } else if ($data['status'] == 1) {
                                                                        echo "activated";
                                                                    }

                                                                    ?> by the admin.

                                                                </td>
                                                            </tr>
                                                            <tr><td height="15"></td></tr>
                                                            <tr>
                                                                <td style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#333;line-height:1.6;">
                                                                    For any queries, contact admin at <?php echo \Config::get('variable.ADMIN_EMAIL'); ?>
                                                                </td>
                                                            </tr>
                                                            <tr><td height="15"></td></tr>
                                                            <tr>
                                                                <td style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;margin-top:3px;color:#777;line-height:1; font-style:italic;">
                                                                    Thank You,<br/><br/>
                                                                    {{ config('app.name', 'Lifewill') }} Team
                                                                </td>
                                                            </tr>
                                                            <tr><td align="center" valign="top" height="30"></td></tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                   
@include('includes.email_footer')