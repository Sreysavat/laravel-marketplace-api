<!DOCTYPE html>
<html>
<head>
    <title>Live Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial;
            text-align: center;
            background: #f5f5f5;
            padding: 40px;
        }

        .box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            width: 320px;
            margin: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .status {
            margin-top: 15px;
            font-weight: bold;
            color: orange;
        }

        .paid {
            color: green;
        }
    </style>
</head>

<body>

<div class="box">

    <h3>Scan to Pay</h3>

    <img src="{{ $payment->response['qr_image'] ?? '' }}" width="250">

    <div id="status" class="status">
        ⏳ Waiting for payment...
    </div>

</div>

<script>
const paymentId = {{ $payment->id }};

async function checkPayment() {
    try {
        const res = await fetch(`/payments/status/${paymentId}`);
        const data = await res.json();

        if (data.status === 'paid') {

            document.getElementById('status').innerHTML =
                '✅ Payment Successful';

            document.getElementById('status').classList.add('paid');

            setTimeout(() => {
                window.location.href = "/payment/success";
            }, 2000);
        }
    } catch (e) {
        console.log(e);
    }
}

// every 2 seconds (ABA-style UX)
setInterval(checkPayment, 2000);
</script>

</body>
</html>