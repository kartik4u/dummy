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
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top; padding:10px 0;"><strong> Dear <?php echo isset($data['name'])?$data['name']:""; ?>,</strong></td>
                                        </tr>
                                         <tr>
                                                    <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">

                                                        Your account has been <?php
                                                                    if ($data['status'] == 2) {
                                                                        echo "de-activated";
                                                                    } else if ($data['status'] == 1) {
                                                                        echo "activated";
                                                                    }

                                                                    ?> by the admin.


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
                                      <?php  if ($data['status'] == 2) {  ?>

                                         <tr>
                                          <td style="font-family: Open Sans,open-sans,sans-serif; font-size:14px; line-height:22px; vertical-align: top;">
                                            For any queries, contact admin at {{ config('variable.ADMIN_EMAIL') }}
                                          </td>
                                        </tr>
                                    <?php } ?>
                                        
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


<table  width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
       <td>
           <tr>
            <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#312055" align="center">
                <tr>
                   <td height="20px"></td>
                </tr>
            </table>


           </tr>
       </td>
    </tr>
</table>
                   
                   
@include('includes.email_footer')