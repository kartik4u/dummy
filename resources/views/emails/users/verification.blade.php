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
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top; padding:10px 0;"><strong> Dear {{ $user->name }} User,</strong></td>
                                        </tr>
                                        @if ($user->secondary_email == '')
                
            
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:16px; line-height:22px; vertical-align: top; padding:10px 0;"><span style="color:#00bbcc">Thanks</span> for signing up! </td>
                                        </tr>
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">Please click on the link below to verify your email address.</td>
                                        </tr>
                                        
                                        @else
                                        <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:16px; line-height:22px; vertical-align: top; padding:10px 0;"><span style="color:#00bbcc">Welcome</span> your request for change email </td>
                                        </tr>
                                      <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">Please click on the link below to verify your email address.</td>
                                        </tr>
                                        @endif
                                         <tr>
                                                    <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">

                                                        <a href="{{ config('variable.SERVER_URL') }}/verification/<?php echo $user->verify_token;?>" style="color: #90cbf5;">
                                                            <span style="color: #00bbcc;">Click Here</span>
                                                        </a>

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
                                              <a style="color:#00bbcc" href="{{ config('variable.SERVER_URL') }}/verification/<?php echo $user->verify_token;?>">{{ config('variable.SERVER_URL') }}/verification/<?php echo $user->verify_token;?></a>
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
