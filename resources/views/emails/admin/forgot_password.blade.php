@include('includes.email_header')
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <tbody>
        <tr>
          <td><table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
              <tbody>
              <tr>
                  <td width="100%">
                  <table bgcolor="#ffffff" width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                      <tbody>
                      <tr>
                          <td width="100%" height="20"></td>
                        </tr>
                      <tr>
                          <td width="100%"><table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                              <tbody>
                              <tr>
                                  <td><table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                      <tbody>
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top; padding:10px 0;"><strong> Dear <?php echo !empty($data['name']) ? $data['name'] : 'User'; ?>,</strong></td>
                                        </tr>
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:16px; line-height:22px; vertical-align: top; padding:10px 0;"> We have received forgot password request </td>
                                        </tr>
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">Please click the link to reset your password.</td>
                                        </tr>
                                         <tr>
                                            <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">
                                            <a href="{{ config('variable.ADMIN_URL') }}/reset-password/<?php echo $data['forgot_password']; ?>" style="color: #00bbcc;">Reset Password</a>
                                            </td>
                                        </tr>

                                    </tbody>
                                    </table></td>
                                </tr>
                              <tr>
                                  <td><table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                      <tbody>
                                      <tr>
                                          <td width="100%" height="15"></td>
                                        </tr>
                                      <tr>
                                          <td width="100%" height="10" style="border-top:1px solid #eee"></td>
                                        </tr>
                                         <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">If above link does not works then copy paste below url in your browser</td>
                                        </tr>
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top; color:#00bbcc; ">
                                              <a style="color:#00bbcc" href="{{ config('variable.ADMIN_URL') }}/reset-password/<?php echo $data['forgot_password']; ?>">{{ config('variable.ADMIN_URL') }}/reset-password/<?php echo $data['forgot_password']; ?></a>
                                          </td>
                                        </tr>
                                      <tr>
                                          <td width="100%" height="10" style="border-bottom:1px solid #eee"></td>
                                        </tr>
                                      <tr>
                                          <td width="100%" height="10"></td>
                                        </tr>
                                    </tbody>
                                    </table></td>
                                </tr>

                              <tr>
                                  <td><table width="560" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                      <tbody>
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top; padding:10px 0; "> Kind regards, </td>
                                        </tr>
                                      <tr>
                                          <td> {{ config('app.name') }} Team </td>
                                        </tr>

                                    </tbody>
                                    </table></td>
                                </tr>

                              <tr>
                                  <td style="border-bottom:2px  #ccc; height:5px; padding:10px;"></td>
                                </tr>
                            </tbody>
                            </table></td>
                        </tr>
                    </tbody>
                    </table>

                 </td>
               </tr>
          </td>
        </tr>
    </tbody>
</table>

@include('includes.email_footer')
