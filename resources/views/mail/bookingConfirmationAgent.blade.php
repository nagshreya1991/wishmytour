<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Booking Approved</title>
    <meta name="description" content="Booking Approval Email Template.">
</head>
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
<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #fff;" leftmargin="0">
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#fff">
        <tr>
            <td>
                <table style="background-color: #fff; max-width:670px; margin:0 auto;" width="100%" border="0"
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
                                style="max-width:670px;background:#fff; border-radius:3px; text-align:center;">
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 35px;">
                                        <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
                                        A new booking {{ $booking_number }} Amount {{ $amount }} has been completed using your Agent Code: {{ $agent_code }}.<br><br>

                                        Thank you for being a valuable part of our team!<br><br>

                                            Best regards,<br>
                                            WishMyTour
                                        </p>

                                    </td>
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
                        <td>
                            <p style="color:#455056; font-size:12px;line-height:18px; margin:20px 0 10px; text-align:center">
                                Please do not reply to this mail as it is a computer-generated mail.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <p style="font-size:14px; color:#393939; line-height:18px; margin:0 0 0;">&copy;{{ date('Y') }}
                                <strong>{{ $siteName }}</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
