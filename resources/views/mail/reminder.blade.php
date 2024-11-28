<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>OTP Verification</title>
    <meta name="description" content="OTP Verification Email Template.">
    <style type="text/css">
        .btn {
            color: #ffffff !important;
            text-decoration: none;
            background: #393939;
            border: none;
            height: 43px;
            font-family: Futura !important;
            font-size: 18px;
            font-weight: 200;
            line-height: 43px;
            padding: 0 20px;
            width: 190px;
            margin: auto;
            border-radius: 0.25rem;
            display: inline-block;
            cursor: pointer;
        }
        .btn:hover {
            background: #000;
        }
    </style>
</head>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
<!--100% body table-->
<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
       style="@import url('https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800&display=swap'); font-family: 'Mulish', sans-serif;">
    <tr>
        <td>
            <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                   align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="height:80px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        <a href="{{ $siteUrl }}" target="_blank">
                            <img align="center" alt="{{ $siteName }}" border="0" class="center fixedwidth" src="{{ $appUrl }}public/images/logo.jpg" style="-ms-interpolation-mode: bicubic; height: auto; width: 152px;" title="{{ $siteName }}"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="height:20px;">&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                               style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">

                            <tr>
                                <td style="padding:0 35px;">
                                    <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Futura',sans-serif;">Dear User, </h1>
                                    <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                   
                                       <h1>Payment Reminder</h1>
    <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">Your payment of {{ $amount }} is due on {{ $dueDate }}.</p>
    <p style="color:#455056; font-size:15px;line-height:24px; margin:0;"> Please ensure the payment is made by the due date to avoid any penalties.</p></td>
                            </tr>
                            <tr>
                                <td style="height:40px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="height:20px;">&nbsp;</td>
                </tr>
                
                <tr>
                    <td style="text-align:center;">
                        <p style="font-size:14px; color:#393939; line-height:18px; margin:0 0 0;">&copy;2024 <strong>{{ $siteName }}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="height:80px;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!--/100% body table-->
</body>

</html>
