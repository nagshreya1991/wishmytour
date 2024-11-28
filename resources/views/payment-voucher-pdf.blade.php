<!doctype html>

<html>

<head>

    <meta charset="utf-8">

    <title>Payment Voucher</title>

</head>



<body>

<div style="width: 100%;">

    <table width="100%">

        <tr>

            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%"

                       style="border-collapse: collapse; margin-bottom: 10px;">

                    <tr>

                        <td><img src="data:image/jpg;base64,{{ base64_encode(file_get_contents( "https://wishmytour.in/backend/public/images/logo.jpg" )) }}" alt="logo"/></td>

                        <td>

                            <h2 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; margin: 0; text-align: right;">

                                Payment Voucher</h2>

                        </td>

                    </tr>

                </table>

                <table border="0" cellpadding="0" cellspacing="0" width="100%"

                       style="border-collapse: collapse; margin-bottom: 15px;">

                    <tr>

                        <td>

                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">

                                From:</h3>

                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">

                                Embiz Wishmytour and Hospitality Private Limited<br/>

                                206/A/6 Indira Gandhi Road,<br/>

                                Konnagar, Hooghly,<br/>

                                WB 712235, IN

                            </p>

                        </td>

                    </tr>

                </table>

                <table border="0" cellpadding="0" cellspacing="0" width="100%"

                       style="border-collapse: collapse; margin-bottom: 15px;">

                    <tr>

                        <td>

                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">

                                To:</h3>

                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">

                                {{ $agent->first_name }}  {{ $agent->last_name }}<br/>

                                {{ $agent->address }}<br/>

                                {{ $agent->agent_code }}<br/>

                                {{ $agent->pan_number }}<br/>

                                {{ $agent->mobile }}

                            </p>

                        </td>

                        <td style="text-align: right;">

                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">{{ $voucher->voucher_number }}</h3>

                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">

                                {{ $voucher->payment_date }}<br/>

                                Ref:{{ $voucher->invoice_number }}<br/>

                            </p>

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

        <tr>

            <table border="0" cellpadding="0" cellspacing="0" width="100%"

                   style="border-collapse: collapse; margin-bottom: 10px;">

                <tr>

                    <td>

                        <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0 0 15px;">

                            Your payment has been processed for <strong>Rs.{{$voucher->net_commission}}</strong> against

                            your invoice no: <strong>{{ $voucher->invoice_number }}</strong>

                            dated {{ $voucher->invoice_date }}</p>

                    </td>

                </tr>

            </table>

        </tr>

        <tr>

            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%"

                       style="border: 1px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">



                    <thead>

                    <tr>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Month/Year

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Commission

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Incentive

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Total

                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    <tr>

                        <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">{{ $voucher->month }}</td>

                        <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">{{ number_format($voucher->commission, 2) }}</td>

                        <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">{{ number_format($voucher->group_commission, 2) }}</td>

                        <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">{{ number_format($voucher->total_commission, 2) }}</td>

                    </tr>

                    </tbody>

                    <tfoot>

                    <tr>

                        <th align="right"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: 700; padding: 10px 20px;"

                            colspan="3">TDS (1%)

                        </th>

                        <th align="right"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"

                            colspan="1">- {{ number_format($voucher->tds, 2) }}</th>

                    </tr>

                    <tr>

                        <th align="right"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: 700; padding: 10px 20px;"

                            colspan="3">TOTAL

                        </th>

                        <th align="right"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"

                            colspan="1">Rs {{ number_format($voucher->net_commission, 2) }}</th>

                    </tr>

                    </tfoot>

                </table>

            </td>

        </tr>

        <tr>

            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%"

                       style="border: 1px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">



                    <thead>

                    <tr>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Date

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Booking

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Net Basic

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Commission

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Incentive

                        </th>

                        <th align="center"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">

                            Total

                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @foreach($commissions as $commission)

                        <tr>

                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">{{ $commission->created_at->format('d M Y') }}</td>

                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">{{ $commission->booking_number }}</td>

                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">{{ number_format($commission->basic_amount - $commission->cancelled_amount, 0) }}</td>

                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">{{ number_format($commission->commission, 0) }}</td>

                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">{{ number_format($commission->group_commission, 0) }}</td>

                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">{{ number_format($commission->total_commission, 0) }}</td>

                        </tr>

                    @endforeach

                    </tbody>

                    <tfoot>

                    <tr>

                        <th align="right"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"

                            colspan="5">TOTAL

                        </th>

                        <th align="right"

                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"

                            colspan="1">Rs {{ number_format($voucher->total_commission, 2) }}</th>

                    </tr>

                    </tfoot>

                </table>

            </td>

        </tr>



        <tr>

            <td>

                <table border="0" cellpadding="0" cellspacing="0" width="100%"

                       style="border-collapse: collapse; margin-bottom: 20px;">





                    <tr>

                        <td colspan="8">

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"

                                   style="border-collapse: collapse;">

                                <tr>

                                    <td width="40%">

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"

                                               style="border-collapse: collapse;">

                                            <tr>

                                                <td style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; line-height: 20px; padding: 10px 0;">

                                                    Amount in words

                                                </td>

                                            </tr>

                                            <tr>

                                                <td style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; padding: 10px 0;">Rupees {{ $voucher->amount_in_word }} only</td>

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

            <table border="0" cellpadding="0" cellspacing="0" width="100%">

                <tr>

                    <td>

                        <p style="color: #000; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 0.6em; font-weight: 500; line-height: 20px; margin: 0 0 15px;">

                            This is a Computer System Generated Document and Needs No Signature</p></td>

                </tr>

            </table>

        </tr>

    </table>

</div>

</body>

</html>

