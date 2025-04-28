<!-- resources/views/aba_qr.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ABA QR Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f2f5f9;
            font-family: 'Poppins', 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .payment-card {
            background: #ffffff;
            border-radius: 16px;
            width: 347px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding-bottom: 20px;
            text-align: center;
        }
        .logo {
            padding: 20px;
        }
        .logo img {
            height: 40px;
        }
        .amount-box {
            color: #000000;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        .amount-display {
            font-size: 32px;
            margin: 15px 0;
            color: #333;
        }
        .currency {
            font-size: 14px;
            color: #888;
            margin-top: -10px;
        }
        .qr-section {
            margin: 20px 0;
        }
       .qr-section img {
            width: 60%;
        }
        .scan-text {
    margin-top: 15px;
    font-size: 9px;
    color: #9d9d9d;
    width: 60%;
    place-self: anchor-center;
    font-weight: 100;
}
        .or-text {
            font-size: 14px;
            color: #aaa;
            margin: 10px 0;
        }
        .download-link {
            display: inline-block;
            margin-top: 10px;
            background: #00b2ff;
            color: #fff;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }
        .download-link:hover {
            background: #0090cc;
        }
        .footer-info {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
        .total-amount {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin: 20px 12px;
}
    </style>
</head>
<body>
<div class="payment-card">


    <div class="amount-box">
        ABA Payment
    </div>

    

    <div class="qr-section">
        @if(isset($responseData['qrImage']))
            <img src="{{ $responseData['qrImage'] }}" alt="Scan QR Code">
        @else
            <p>QR code not available</p>
        @endif
    </div>

    <div class="scan-text">
        Scan with Bakong App or Mobile Banking app that support KHOR 
    </div>


    @if(isset($responseData['qrImage']))
        <a href="{{ $responseData['qrImage'] }}" download="qr-code.png" class="download-link">
            Download QR
        </a>
    @endif

</div>

</body>
</html>
