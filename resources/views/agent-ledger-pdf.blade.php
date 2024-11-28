<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Ledger</title>
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
                                Commission Ledger
                            </h2>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td>
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">
                                Company:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                <strong>{{ $companyName }}</strong><br/>
                                {{ $companyAddress }}
                            </p>
                        </td>
                        <td style="text-align: right;">
                            <h3 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: bold; margin: 0 0 3px 0;">
                                Agent Details:</h3>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                {{ $agentDetails->first_name }} {{ $agentDetails->last_name }}<br/>
                                PAN: {{ $agentDetails->pan_number }}<br/>
                                Mobile: {{ $agMobile }}<br/>
                                Period: {{ request()->input('start_date') }} to {{ request()->input('end_date') }}
                            </p>
                        </td>
                    </tr>
                </table>
                <table border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ababab;">
                    <thead>
                        <tr>
                            <th align="left" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                <strong>Date</strong>
                            </th>
                            <th align="left" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                <strong>Description</strong>
                            </th>
                            <th align="right" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                <strong>Debit</strong>
                            </th>
                            <th align="right" style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                <strong>Credit</strong>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledger as $entry)
                            @if ($entry['type'] !== 'closing-balance')
                                <tr>
                                    <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                        {{ $entry['date'] }}
                                    </td>
                                    <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                        {{ $entry['description'] }}
                                        @if (!empty($entry['invoice_number']))
                                            <br>Ref.No.: {{ $entry['invoice_number'] }}
                                        @endif
                                        @if (!empty($entry['voucher_number']))
                                            <br>Ref.No.: {{ $entry['voucher_number'] }}
                                        @endif
                                    </td>
                                    <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">
                                        {{ $entry['type'] === 'debit' || $entry['type'] === 'opening-balance' ? number_format($entry['amount'], 2) : '' }}
                                    </td>
                                    <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">
                                        {{ $entry['type'] === 'credit' ? number_format($entry['amount'], 2) : '' }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: bold; padding: 10px 20px; text-align: right;">
                                <strong>{{ number_format($totalDebit, 2) }}</strong>
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: bold; padding: 10px 20px; text-align: right;">
                                <strong>{{ number_format($totalCredit, 2) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                {{ $closingBalanceDate }}
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: bold; padding: 10px 20px;">
                                <strong>Closing Balance</strong>
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: bold; padding: 10px 20px; text-align: right;">
                                <strong>{{ number_format($finalBalance, 2) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;"></td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: bold; padding: 10px 20px; text-align: right;">
                                <strong>{{ number_format($totalDebit, 2) }}</strong>
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: bold; padding: 10px 20px; text-align: right;">
                                <strong>{{ number_format($totalCredit + $finalBalance, 2) }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
