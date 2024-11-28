<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger</title>
</head>
<body>
<div style="width: 100%;">
    <table width="100%">
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td>
                            <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents('https://wishmytour.in/backend/public/images/logo.jpg')) }}"
                                 alt="logo"/>
                        </td>
                        <td>
                            <h2 style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; margin: 0; text-align: right;">
                                Commission Ledger
                            </h2>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td colspan="2">
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                {!! $fromInfo !!}
                            </p>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td colspan="2">
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.9375em; font-weight: normal; line-height: 20px; margin: 0;">
                                {!! $toInfo !!}<br/>
                            </p>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; line-height: 20px; margin: 0;">
                                {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}
                            </p>
                        </td>
                    </tr>
                </table>
                <table border="1" cellpadding="0" cellspacing="0" width="100%"
                       style="border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ababab;">
                    <thead>
                    <tr>
                        <th align="left"
                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                            <strong>Date</strong>
                        </th>
                        <th align="left"
                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                            <strong>Description</strong>
                        </th>
                        <th align="right"
                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                            <strong>Debit</strong>
                        </th>
                        <th align="right"
                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: center;">
                            <strong>Credit</strong>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">{{ $transaction->date }}</td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px;">
                                {{ $transaction->description }}
                                @if(!empty($transaction->reference_number))
                                    <br><span style="font-size:11px;">Ref no: {{ $transaction->reference_number }}</span>
                                @endif
                            </td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">{{ $transaction->debit ? number_format($transaction->debit, 2) : '' }}</td>
                            <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">{{ $transaction->credit ? number_format($transaction->credit, 2) : '' }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"
                            style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">
                            <strong>Total</strong></td>
                        <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: bold; padding: 10px 20px; text-align: right;">
                            <strong>{{ number_format($totalDebit, 2) }}</strong>
                        </td>
                        <td style="border: 1px solid #ababab; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.7375em; font-weight: normal; padding: 10px 20px; text-align: right;">
                            <strong>{{ number_format($totalCredit, 2) }}</strong>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td>
                            <p style="color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 0.625em; font-weight: normal; line-height: 20px; margin: 0; text-align: center;">
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
