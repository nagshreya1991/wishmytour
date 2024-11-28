<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Credit Note</title>
</head>
<body>
<div style="width: 100%;">
    <table width="100%">
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td><img src="data:image/jpg;base64,{{ base64_encode(file_get_contents( "https://wishmytour.in/backend/public/images/logo.jpg" )) }}" alt="logo"/></td>
                        <td>
                            <h2 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; margin: 0; text-align: right;">
                                Credit Note</h2>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td>
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">
                                From:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                {{ $vendor->organization_name }}<br/>
                                PAN: {{ $vendor->pan_number }}<br/>
                                Code: {{ $vendor->vendor_code }}<br/>
                                Mobile: {{ $vendor->vendor_mobile }}<br/>
                                Address: {{ $vendor->address }}
                            </p>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td>
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">
                                To:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                {{ $customer->first_name }} {{ $customer->last_name }}<br/>
                                Address: {{ $customer->address }}<br/>
                                Mobile: {{ $customer->mobile }}
                            </p>
                        </td>
                        <td style="text-align: right;">
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">
                                #{{ $cancellation->transaction_number }}</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                Created: {{ \Carbon\Carbon::parse($cancellation->created_at)->format('F d, Y') }}<br/>
                                Total Amount: {{ number_format($cancellation->booking_price, 2) }}<br/>
                                Refund Amount: {{ number_format($cancellation->refund_amount, 2) }}<br/>
                                Payment Date: {{ \Carbon\Carbon::parse($cancellation->created_at)->format('F d, Y') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td>
                        <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0 0 15px;">
                            A credit note has been issued for Rs.{{ number_format($cancellation->refund_amount, 2) }} against your booking no: {{ $cancellation->booking_number }} dated {{ $cancellation->booking_date }}.
                        </p>
                    </td>
                </tr>
            </table>
        </tr>
        <tr>
            <td>
                <table border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ababab;">
                    <thead>
                        <tr>
                            <th align="center" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                            <strong>Particulars</strong>
                            </th>
                            <th align="center" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                            <strong>  Amounts</strong>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                           Booking Amount
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                                {{ number_format($cancellation->booking_price, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                            Cancellation Charge
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                                {{ number_format($cancellation->cancellation_charge, 2) }}
                            </td>
                        </tr>
                        @if($cancellation->gst_charge != 0.00)
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                                GST Charge
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                                {{ number_format($cancellation->gst_charge, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th align="right" colspan="1" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: 700; padding: 10px 20px;">
                                Total
                            </th>
                            <th align="right" colspan="1" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                Rs {{ number_format($cancellation->paid_amount, 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        
        <tr>
            <td>
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                This is a computer generated document and does not require signature.
                            </p>
                        </td>
                       
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
