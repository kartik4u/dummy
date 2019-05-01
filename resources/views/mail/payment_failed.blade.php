<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Signup varification Email Template</title>
<style type="text/css">
#outlook a {padding:0;}
body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; background:#272727;}
.ExternalClass {width:100%;}
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
a img {border:none;}
.image_fix {display:block;}
p {margin: 0px 0px !important;}
table td {border-collapse: collapse;}
table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
a {color: #9ec459;text-decoration: none;text-decoration:none!important;}
table[class=full] { width: 100%; clear: both; }
@media only screen and (max-width: 640px) {
a[href^="tel"], a[href^="sms"] {
text-decoration: none;
color: #9ec459;
pointer-events: none;
cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
text-decoration: default;
color: #9ec459 !important; 
pointer-events: auto;
cursor: default;
}
table[class=devicewidth] {width: 440px!important;text-align:center!important;}
td[class=devicewidth] {width: 440px!important;text-align:center!important;}
img[class=devicewidth] {width: 440px!important;text-align:center!important;}
img[class=banner] {width: 440px!important;height:147px!important;}
table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
table[class=icontext] {width: 345px!important;text-align:center!important;}
img[class="colimg2"] {width:420px!important;height:243px!important;}
table[class="emhide"]{display: none!important;}
img[class="logo"]{width:440px!important;height:110px!important;}
}
@media only screen and (max-width: 480px) {
a[href^="tel"], a[href^="sms"] {
text-decoration: none;
color: #9ec459;
pointer-events: none;
cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
text-decoration: default;
color: #9ec459 !important; 
pointer-events: auto;
cursor: default;
}
table[class=devicewidth] {width: 280px!important;text-align:center!important;}
td[class=devicewidth] {width: 280px!important;text-align:center!important;}
img[class=devicewidth] {width: 280px!important;text-align:center!important;}
img[class=banner] {width: 280px!important;height:93px!important;}
table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
table[class=icontext] {width: 186px!important;text-align:center!important;}
img[class="colimg2"] {width:260px!important;height:150px!important;}
table[class="emhide"]{display: none!important;}
img[class="logo"]{width:280px!important;height:70px!important;}

}
</style>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="margin: auto;">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="600" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth" style="background: #00758e;">
                           <tbody>
                            <tr>
                              <td height="50"></td>
                            </tr>
                              
                              <tr>
                                 <td align="center">
                                    
                                    <a style="text-decortation:none;display: inline-block;width: 100%;text-align: center;" href= "<?php echo $site_url =  \Config::get('variable.FRONTEND_URL');
 ?>" target="_blank"><img src="{{ asset('assets/img/email_logo.png') }}" alt="logo" class="logo" style="max-width: 140px;" /></a>
                                    
                                 </td>
                              </tr>
                             <tr>
                              <td height="50"></td>
                            </tr>                                 
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
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
   <tbody>
      <tr>
         <td>
            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="margin: auto;background: #f1f1f1;">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#f1f1f1" width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              
                              <tr>
                                 <td>
                                    <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner" style="color: #00758e;margin: auto;">
                                       <tbody>
                                        <tr><td height="30"></td></tr>
                                        <tr>
                                            <td class="scale-center">

                                                <font style="font-family:Helvetica, Arial, sans-serif; font-size: 15px; color: #00758e; font-weight:600;">Hi <?php echo isset($first_name) ? ucfirst($first_name) : '' ?>,</font>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20"></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="line-height: 30px;" class="scale-center-both">
                                              <br>
                                              <font style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;">Payment for the job <?php echo $job_name; ?> has been failed: <br/><br/></font>
                                              <?php echo isset($msg)?$msg : '' ?>           
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                        <tr>
                                            <td style="line-height: 30px;" class="scale-center-both">
                                            <br>
                                           <font style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;">You can access your account and make improvements that are mentioned above. Then re-send it for approval. <br/><br/></font>
                                            </td>
                                        </tr>
                                    
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                        <tr>
                                            <td class="scale-center">

                                                <p style="font-family:Helvetica, Arial, sans-serif; font-size:15px; color:#00687e; line-height:2; font-style:italic; font-weight:600;">Thank  You,<br/>Carebe Team.</p>

                                            </td>
                                        </tr>
                                        <tr><td style="border-bottom: 1px solid #d6d6d5;" height="54"></td></tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                                <tr>
                                    <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="background-color: #00758e; color: #fff; font-family: Open Sans,open-sans,sans-serif;">
                                      <tr>
                                        <td height="13"></td>
                                      </tr>
                                        <tr>
                                            <td>
                                                
                                                <table border="0" cellspacing="0" cellpadding="0" align="center" style=" margin: auto;width: 580px;">
                                                    <tbody> 
                                                        <tr>
                                                            <td style="width: 50%; text-align: left;">
                                                             <p style="font-size: 14px;">Copyright©Carebe. All rights reserved.</p>
                                                            </td>
                                                            <td style="width: 50%; text-align: right;">
                                                                <a target="_blank" href="https://www.facebook.com/HomeCareEvolution/"><img src="{{ asset('assets/images/facebook.png') }}" alt="facebook"></a>
                                                                <span style="width:4px;display:inline-block;"></span>
                                                                <a target="_blank" href="https://twitter.com/AlixMontrose"><img src="{{ asset('assets/images/twitter.png') }}"  alt="twitter"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                          <td height="13"></td>
                                        </tr>
                                    </table>
                                    </td>
                                   </tr>
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
</body>
</html>
