<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - Tour and Travels Company</title>
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
                                Provisional confirmation</h2>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td>
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; margin: 0 0 3px 0;">
                            Tour Operator:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; margin: 0;">
                                {{ $vendorDetails->organization_name }}<br>
                                {{ $vendorDetails->address }}
                            </p>
                        </td>
                        <td style="text-align: right;">
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; margin: 0 0 3px 0;">
                                Billing Address:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; margin: 0;">
                            {{ $bookingCustomer->first()->name }}<br/>
                            {{ $bookingCustomer->first()->address }}
                            </p>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td>
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; margin: 0 0 3px 0;">
                                 Details:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; margin: 0;">
                                <strong>PAN No:</strong> {{ $vendorDetails->pan_number }}<br/>
                                <strong>GST Reg:</strong> {{ $vendorDetails->gst_number }}
                            </p>
                        </td>
                        <td style="text-align: right;">
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; margin: 0 0 3px 0;">
                                Customer Details:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; margin: 0;">
                                {{--	<strong>GST Reg:</strong> 1945652KLRGDT<br />
                                    <strong>GST Reg:</strong> 1945652KLRGDT--}}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr bgcolor="#CECECE">
                        <th style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; padding: 5px; text-align: left;"
                            colspan="2">Package Details
                        </th>
                        <th style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; padding: 5px; text-align: left;"
                            colspan="6">Package cost
                        </th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px;"
                            width="20%">Name
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px;"
                            width="40%">Description
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                            width="10%">Net Amt.
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                            width="10%">Addon Amt.
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                            width="10%">GST Amt.
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                            width="10%">TCS Amt.
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                            width="10%">Coupon DIscount
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; padding: 5px; text-align: center;"
                            width="10%">Total Amt.
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size:  0.8125em; font-weight: normal; line-height: 24px; padding: 5px;">
                            <strong>{{ $packageName }}</strong></td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px;">
                            {{ $totalDaysAndNights }} <br>
                                @foreach ($stayPlan as $stayIndex => $stayItem)
                                <span>
                                {{ $stayItem['city_name'] }} ({{ $stayItem['total_nights'] }}N)
                                @if ($stayIndex < count($stayPlan) - 1)
                                &rarr; 
                                @endif
                                </span>
                                @endforeach
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">{{ number_format($booking->base_amount, 2) }}</td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                            {{ number_format($booking->addon_total_price, 2) }}
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                            {{ number_format($booking->gst_price, 2) }}
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                            {{ number_format($booking->tcs, 2) }}
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                            {{ number_format($booking->coupon_price, 2) }}
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; padding: 5px; text-align: center;">
                            {{ number_format($booking->final_price, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;"
                            colspan="2">TOTAL
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: right;"
                            colspan="6">Rs {{ number_format($booking->final_price, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                   style="border-collapse: collapse;">
                                <tr>
                                    <td width="40%">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                               style="border-collapse: collapse;">
                                            <tr>
                                                <td style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; border-bottom: 2px solid #ababab; padding: 5px;">
                                                    Amount in words
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px;">{{ $finalPriceInWords }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border-left: 2px solid #ababab;" width="60%">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                               style="border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 5px;">
                                                    <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; margin: 0;">
                                                        For {{ $vendorDetails->fullname }}</h4>
                                                    <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; margin: 0;">
                                                        Authorized Signature</p>
                                                </td>
                                                <td style="padding: 5px; text-align: right;"><img
                                                            src="data:image/jpg;base64,{{ base64_encode(file_get_contents( "https://wishmytour.in/backend/public/images/signature-img.jpg" )) }}"
                                                            alt="Signature image" border="0"
                                                            style="height: auto; width: 200px;"/></td>
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
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr bgcolor="#CECECE">
                        <th colspan="4"
                            style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; padding: 5px; text-align: left;">
                            Passenger Details
                        </th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                            width="8%">#
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;">
                            Name
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                            width="10%">Age
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;"
                            width="14%">Gender
                        </td>
                    </tr>

                    @foreach ($bookingPassengers as $key => $passenger)
                        <tr>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                                {{ $key + 1 }}
                            </td>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;">
                                {{ $passenger->first_name }} {{ $passenger->last_name }}
                            </td>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                                {{ \Carbon\Carbon::parse($passenger->dob)->age }} years
                            </td>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;">
                                {{ $passenger->gender }}
                            </td>
                        </tr>
                    @endforeach


                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr bgcolor="#CECECE">
                        <th colspan="2"
                            style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; padding: 5px; text-align: left;">
                            Itenary Details
                        </th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                        >Days
                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;">
                            Details
                        </td>
                    </tr>

                    @foreach ($itineraries as $key => $itinerary)
                        <tr>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                                {{ $itinerary->day }}
                            </td>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;">
                                {{ $itinerary->itinerary_description }}
                            </td>
                        </tr>
                    @endforeach

                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border: 2px solid #ababab; border-collapse: collapse; margin-bottom: 20px;">
                    <tr bgcolor="#CECECE">
                        <th colspan="2"
                            style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: bold; padding: 5px; text-align: left;">
                            Transaction Details
                        </th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;"
                        ><strong>Payment Transaction ID:</strong>

                        </td>
                        <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;">
                        <strong>Date & Time:</strong>

                        </td>
                    </tr>

                    
                        <tr>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: center;">
                                {{ $bookingPayment->transaction_number }}
                            </td>
                            <td style="border: 2px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.6875em; font-weight: normal; padding: 5px; text-align: left;">
                            {{ $bookingPayment->updated_at }}
                            </td>
                        </tr>
               

                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 5px;">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">
                                Inclusions</h4>
                            @foreach ($inclusions as $inclusion)
                                <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                                    {{ $inclusion }}
                                </p>
                            @endforeach
                        </td>
                        <td style="padding: 5px 5px 5px 0;">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">
                                Exclusions</h4>
                            @foreach ($exclusions as $exclusion)
                                <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                                    {{ $exclusion }}
                                </p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">
                                PAYMENT POLICY</h4>
                            @foreach ($paymentPolicies as $paymentPolicy)
                                <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                                    {{ $paymentPolicy }}
                                </p>
                            @endforeach
                        </td>
                        <td style="padding: 5px 5px 5px 0;">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">
                                CANCELLATION POLICY</h4>
                            @foreach ($cancellationPolicies as $cancellationPolicy)
                                <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                                    {{ $cancellationPolicy }}
                                </p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0 10px 0;" colspan="2">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">
                            STATUTORY Declaration:</h4>
                           
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                            Failing to make any or all payment within Due Date will lead to Cancellation (subject to cancellation policy for the respective tour package/product/services you have bought read with Terms of Use) of the entire package you have booked  / booking you made without any further correspondence. </p>

                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0 10px 0;" colspan="2">
                            <h4 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: bold; margin: 0 0 8px 0; text-transform: uppercase;">
                                TERMS AND CONDITION</h4>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                                {!! nl2br(e($packageTerms)) !!}</p>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                                {!! nl2br(e($defaultTerms)) !!}</p>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.5625em; font-weight: normal; margin: 0 0 0 0;">
                                I confirm that I have read and I accept Cancellation Policy, User Agreement, Terms of Service and Privacy Policy of Wishmytour</p>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
