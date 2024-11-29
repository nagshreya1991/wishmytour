<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Untitled Document</title>
</head>

<body>
<div style="width: 100%;">
    <table width="100%">
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td><img src="{{ asset('public/images/logo.jpg') }}" alt="logo" /></td>
                        <td>
                            <h2 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; margin: 0; text-align: right;">Invoice/provisional confirmation</h2>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td>
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">Vendor:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                Hari Sadhan Travelers Pvt. Ltd. <br />
                                53/7 NSC Bose Road, Ranikuthi,<br />
                                Kolkata - 722154
                            </p>
                        </td>
                        <td style="text-align: right;">
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">Billing Address:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                Gourmohon Das<br />
                                Jagacha GIP Colony, Mohiary Road,<br />
                                Howrah - 711112
                            </p>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td>
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">Vendor Details:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                <strong>PAN No:</strong> ATG5879KLT<br />
                                <strong>GST Reg:</strong> 1945652KLRGDT
                            </p>
                        </td>
                        <td style="text-align: right;">
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">Customer Details:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                <strong>GST Reg:</strong> 1945652KLRGDT<br />
                                <strong>GST Reg:</strong> 1945652KLRGDT
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr bgcolor="#CECECE">
                        <th style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; padding: 10px 20px; text-align: left;" colspan="2">Package Details</th>
                        <th style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; padding: 10px 20px; text-align: left;" colspan="6">Package cost</th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px;">Name</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px;">Description</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center; width: 8%;">Net Amt.</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center; width: 8%;">Addon Amt.</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center; width: 8%;">GST Amt.</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center; width: 8%;">TCS Amt.</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center; width: 10%; white-space: nowrap;">Coupon DIscount</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; padding: 10px 20px; text-align: center; width: 8%; white-space: nowrap;">Total Amt.</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 1.25em; font-weight: normal; line-height: 24px; padding: 10px 20px;"><strong>Chadar And Lingshed Village Trek</strong></td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px;">4 Days / 3 Nights Package</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">0.00</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">0.00</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">0.00</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">0.00</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">0.00</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; padding: 10px 20px; text-align: center;">0.00</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;" colspan="2">TOTAL</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: right;" colspan="6">Rs XXXXXX.XXXX</td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <tr>
                                    <td width="40%">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                            <tr>
                                                <td style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; line-height: 20px; border-bottom: 2px solid #ababab; padding: 10px 20px;">Amount in words</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px;">Fifty thousand Only</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border-left: 2px solid #ababab;" width="60%">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 10px 20px;">
                                                    <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; line-height: 20px; margin: 0;">For Hari Sadhan Travelers Pvt Ltd</h4>
                                                    <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">Authorized Signature</p>
                                                </td>
                                                <td style="padding: 10px 20px; text-align: right;"><img src="img/signature-img.jpg" alt="Signature image" border="0" style="height: auto; width: 428px;" /></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr bgcolor="#CECECE">
                        <th colspan="4" style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; padding: 10px 20px; text-align: left;">Passenger Details</th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;" width="8%">#</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Name</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;" width="10%">Age</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;" width="14%">Gender</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">1</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Partho Sarathi Sen</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">35</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Male</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">2</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Mira Sen</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">33</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Female</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">3</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Satyaki Sen</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">12</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Male</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">4</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Laskhmipriya Sen</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: center;">06</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; padding: 10px 20px; text-align: left;">Female</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr bgcolor="#CECECE">
                        <th colspan="4" style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; padding: 10px 20px; text-align: left;">Itenary Details</th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: center;" width="8%">Days</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;" width="92%">Details</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: center;">1</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">Arrive Leh : Arrive Kushok Bakula airport Leh - 3500m above sea level. Transfer to hotel.</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: center;">2</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">Drive Sham Valley: Post breakfast we drive on the Srinagar highway and start our Day.</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: center;">3</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">Leh to Nubra via Khardungla Pass : Today at morning drive to Nubra valley, driving over the highest motored.</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: center;">4</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">Depart Leh: In the morning transfer to the airport to board the flight for your onward destination.</td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: center;">14</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">Depart Leh: In the morning transfer to the airport to board the flight for your onward destination.</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">
                            <strong>Payment Transaction ID:</strong><br />
                            1234986465456874
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">
                            <strong>Date & Time:</strong><br />
                            07/05/2024, 11:18:55
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;" rowspan="2">
                            <strong>Invoice Value:</strong><br />
                            248.00
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">
                            <strong>Mode of Payment:</strong><br />
                            UPI
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">
                            <strong>Payment Transaction ID:</strong><br />
                            1234986465456874
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px;padding: 10px 20px; text-align: left;">
                            <strong>Date & Time:</strong><br />
                            07/05/2024, 11:18:55
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 20px; text-align: left;">
                            <strong>Mode of Payment:</strong><br />
                            Debit Card
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 10px 20px 10px 0;">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif;font-size: 0.8125em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">CANCELLATION POLICY</h4>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif;font-size: 0.8125em; font-weight: normal; line-height: 18px; margin: 0 0 0 0;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries.</p>
                        </td>
                        <td style="padding: 10px 20px;">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif;font-size: 0.8125em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">PAYMENT POLICY</h4>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif;font-size: 0.8125em; font-weight: normal; line-height: 18px; margin: 0 0 0 0;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries.</p>
                        </td>
                        <td style="padding: 10px 0 10px 20px;">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif;font-size: 0.8125em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">TERMS AND CONDITON</h4>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif;font-size: 0.8125em; font-weight: normal; line-height: 18px; margin: 0 0 0 0;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
