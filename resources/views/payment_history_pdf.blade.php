<!DOCTYPE html>
<html>
<head>
    <title>Payment History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Payment History</h1>
    <table>
        <thead>
            <tr>
                <th>Booking Number</th>
                <th>Payment Amount</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentList as $payment)
            <tr>
                <td>{{ $payment->booking_number }}</td>
                <td>{{ $payment->amount }}</td>
                <td>{{ $payment->payment_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
