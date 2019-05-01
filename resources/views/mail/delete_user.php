<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Delete User</title>
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
                        <table bgcolor="#ffffff" width="600" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth" style="background: #00bbcc;">
                           <tbody>
                          <tr>
                            <td height="10"></td>
                          </tr>
                          <tr>
                             <td align="center">
                                <a style="text-decortation:none;display: inline-block;width: 100%;text-align: center;" href="<?php echo $site_url =  \Config::get('variable.FRONTEND_URL'); ?>" target="_blank"><img src="{{ URL::asset('/images/white-logo.png') }}" alt="logo" class="logo" style="max-width: 140px;" /></a>
                             </td>
                          </tr>
                          <tr>
                            <td height="10"></td>
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
</td>
</tr>
</tbody>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
<tbody>
<tr>
<td>
<table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
<tbody>
<tr>
<td width="100%">
<table bgcolor="#f1f1f1" width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="border:1px solid #ccc">
<tbody>
<tr>
<td width="100%" height="20"></td>
</tr>
<tr>
<td>
<table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
<tbody>
<tr>

<td style="font-family:Helvetica, Arial, sans-serif; font-size: 15px; color: #00bbcc; font-weight:600;">
Hi Admin,  <br/> <br/> <br/>
</td>

</tr>
<tr>

<td style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;" >

<?php echo !empty($data['name']) ? ucwords($data['name']) : $data['email']; ?> has deleted his Acoount. Please find the reason below:
<br/>
</td>

</tr>
<tr>
<td style="font-family: Open Sans,open-sans,sans-serif; font-size:16px; line-height:22px;">
<td>
</tr>
<tr>

<td style="font-family: Open Sans,open-sans,sans-serif; font-size:16px; line-height:22px;" >
<br/>
<?php
if (isset($data['message'])) {
    echo ucwords($data['message']);
} else {
    echo '';
}
?><br>
</td>
</tr>



<tr>
<td height="20"></td>
</tr>
<tr>
<td class="scale-center">
    <p style="font-family:Helvetica, Arial, sans-serif; font-size:15px; color: #00bbcc; line-height:2; font-style:italic; font-weight:600;">Thank You,<br/>Episodic Team.</p>
</td>
</tr>
<tr><td style="border-bottom: 1px solid #d6d6d5;" height="54"></td></tr>
<td style="border-bottom:2px  #ccc; height:5px; padding:10px;"></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="background-color: #00bbcc; color: #fff; font-family: Open Sans,open-sans,sans-serif;">
<tr>
<td height="13"></td>
</tr>
<tr>
<td>
  <table border="0" cellspacing="0" cellpadding="0" align="center" style=" margin: auto;width: 580px;">
    <tbody>
      <tr>
        <td style="width: 50%; text-align: left;">
        <p style="font-size: 14px;">Copyright©Episodic. All rights reserved.</p>
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
