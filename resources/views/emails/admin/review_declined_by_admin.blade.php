@include('includes.email_header')
        <!-- start of Full text -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
            <tbody>
                <tr>
                    <td>
                        <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                            <tbody>
                                <tr>
                                    <td width="100%">
                                        <table bgcolor="#f2f5f4" width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                                            <tbody>
                                                <!-- Spacing -->
                                                <tr>
                                                    <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                                </tr>
                                                <!-- Spacing -->
                                                <tr>
                                                    <td>
                                                        <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                                            <tbody>
                                                                <!-- Title -->
                                                                <tr>
                                                                    <td style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color: #797784; line-height: 24px;">
                                                                        Hi <?php echo isset($first_name) ? ucfirst($first_name) : '' ?>,
                                                                    </td>
                                                                </tr><tr><td height="15"></td></tr>
                                                                <tr>
                                                                    <td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                        <?php echo $msg ?>:
                                                                    </td>
                                                                </tr>
                                                                <tr><td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                        <?php echo isset($reason) ? $reason : '' ?>           

                                                                    </td>
                                                                </tr>
                                                                <tr><td height="15"></td></tr>
                                                                <tr><td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                        Below is the original review:          

                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                        Rating: <?php echo isset($review['rating']) ? $review['rating'] : '' ?>          

                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                        Subject: <?php echo isset($review->ratingSubject->title) ? $review->ratingSubject->title : '' ?>          

                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                        Comment: <?php echo isset($review['comment']) ? $review['comment'] : '' ?>          

                                                                    </td>
                                                                </tr>
                                                                <tr><td height="15"></td></tr>
                                                                <?php if ($toComp != '') { ?>
                                                                    <tr><td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                            You can edit the review for reconsideration.  
                                                                        </td>
                                                                    </tr>
                                                                <?php }
                                                                ?>                                                                
                                                                </tr>  
                                                                <tr><td height="15"></td></tr>
                                                                <tr>
                                                                    <td style="font-family: Helvetica, arial, sans-serif; font-size: 15px; color: #797784; line-height: 24px;">
                                                                        Kind regards,<br/>
                                                                        {{ config('app.name') }} Team
                                                                    </td>
                                                                </tr>                                                                
                                                                <!-- End of content -->                                                       
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- Spacing -->
                                                <tr>
                                                    <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                                </tr>
                                                <!-- Spacing -->
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- End of Full Text -->
@include('includes.email_footer')