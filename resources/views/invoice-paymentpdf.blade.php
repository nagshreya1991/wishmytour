<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
</head>
<body>
<div style="width: 100%;">
    <table width="100%">
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td>
                            <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents('https://wishmytour.in/backend/public/images/logo.jpg')) }}" alt="logo"/>
                        </td>
                        <td>
                            <h2 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; margin: 0; text-align: right;">
                                Payment Receipt
                            </h2>
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
                                #{{ $payment->transaction_number }}</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                Created: {{ \Carbon\Carbon::parse($payment->created_at)->format('F d, Y') }}<br/>
                                Total Amount: {{ number_format($payment->total_amount, 2) }}<br/>
                                Paid Amount: {{ number_format($payment->paid_amount, 2) }}<br/>
                                Payment Date: {{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}
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
                            A payment has been received for Rs.{{ number_format($payment->paid_amount, 2) }} against your booking no: {{ $payment->booking_number }}.
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
                                <strong>Amounts</strong>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                                Booking Fee
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                                {{ number_format($payment->paid_amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th align="right" colspan="1" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: 700; padding: 10px 20px;">
                                Total
                            </th>
                            <th align="right" colspan="1" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                Rs {{ number_format($payment->paid_amount, 2) }}
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
