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
            background: #d9534f;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
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
            width: 160px;
            height: 160px;
            border: 8px solid #f2f5f9;
            border-radius: 12px;
            background: #fff;
        }
        .scan-text {
            margin-top: 15px;
            font-size: 16px;
            color: #555;
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

    <div class="logo">
        <img src="https://rsadora.com/storage/644/WhatsApp-Image-2025-03-20-at-12.34.59-PM-(1).jpeg" alt="Shopping Store"> <!-- Replace with your logo path -->
    </div>

    <div class="amount-box">
        ABA Payment
    </div>

    <div class="amount-display">
        {{ number_format($order->total ?? 0.00, 2) }}
    </div>
    <div class="currency">
        {{ $responseData['currency'] ?? 'USD' }}
    </div>

    <div class="qr-section">
        @if(isset($responseData['qrImage']))
            <img src="{{ $responseData['qrImage'] }}" alt="Scan QR Code">
        @else
            <p>QR code not available</p>
        @endif
    </div>

    <div class="scan-text">
        Scan to Pay
    </div>

    <div class="or-text">
        OR
    </div>

    @if(isset($responseData['qrImage']))
        <a href="{{ $responseData['qrImage'] }}" download="qr-code.png" class="download-link">
            Download QR
        </a>
    @endif

    <div class="footer-info">
        and upload to Mobile Banking app supporting ABA QR
    </div>

    <div class="total-amount">
        <div style="display: flex;justify-content: space-between;border-bottom: 2px dotted;margin: 5px 0px;padding: 6px 0px;font-weight: 100;font-size: small;">Subtotal<small>{{ number_format($order->total ?? 0.00, 2) }} {{ $responseData['currency'] ?? 'USD' }}</small></div>
        <div style="display: flex;justify-content: space-between">TOTAL<small>{{ number_format($order->total ?? 0.00, 2) }} {{ $responseData['currency'] ?? 'USD' }}</small></div>
    </div>
  

</div>

</body>
</html>
