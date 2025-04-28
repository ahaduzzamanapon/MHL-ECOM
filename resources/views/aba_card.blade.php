
<?php
/*
|--------------------------------------------------------------------------
| ABA PayWay API URL
|--------------------------------------------------------------------------
| API URL that is provided by PayWay must be required in your post form
|
*/
define('ABA_PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase');

/*
|--------------------------------------------------------------------------
| ABA PayWay API KEY
|--------------------------------------------------------------------------
| API KEY that is generated and provided by PayWay must be required in your post form
|
*/
define('ABA_PAYWAY_API_KEY', 'bc1d4e36-ca0c-4da3-930d-da5d67500e80');

/*
|--------------------------------------------------------------------------
| ABA PayWay Merchant ID
|--------------------------------------------------------------------------
| Merchant ID that is generated and provided by PayWay must be required in your post form
|
*/
define('ABA_PAYWAY_MERCHANT_ID', 'adoralifestyle');


class PayWayApiCheckout {

    /**
     * Returns the getHash
     * For PayWay security, you must follow the way of encryption for hash.
     *
     * @param string $transactionId
     * @param string $amount
     *
     * @return string getHash
     */
    public static function getHash($hash_str) {
//      echo $hash_str; die;
        $hash = base64_encode(hash_hmac('sha512', $hash_str, ABA_PAYWAY_API_KEY, true));
        return $hash;
    }

    /**
     * Returns the getApiUrl
     *
     * @return string getApiUrl
     */
    public static function getApiUrl() {
        return ABA_PAYWAY_API_URL;
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <title>PayWay Checkout Sample</title>

    <!— Make a copy of this code to paste into your site—>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="author" content="PayWay">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <!— end —>
</head>

<body>
<!— Popup Checkout Form —>
<div id="aba_main_modal" class="aba-modal">
    <!— Modal content —>
    <div class="aba-modal-content">


        <!-- Include PHP class -->
        <?php

        $transactionId = time();
        $amount = $order->total;
        $firstName = $order->first_name;
        $lastName = $order->last_name;
        $phone = $order->phone;
        $email = $order->email;
        $req_time = time();
        $merchant_id = 'adoralifestyle';
        $payment_option='cards'; 
        $return_url =$array['cancel_url']; 
        $cancel_url = $array['cancel_url'];
        $continue_success_url = $array['success_url'];
        $currency = 'usd';


        $hashdata=PayWayApiCheckout::getHash($req_time . $merchant_id . $transactionId . $amount.$firstName.$lastName.$email.$phone .$payment_option. $return_url . $cancel_url . $continue_success_url.$currency)
        ?>

        <form method="POST" target="aba_webservice" action="<?php echo PayWayApiCheckout::getApiUrl(); ?>" id="aba_merchant_request">
            <input type="hidden" name="hash" value="<?php echo $hashdata ; ?>" id="hash"/>
            <input type="hidden" name="tran_id" value="<?php echo $transactionId; ?>" id="tran_id"/>
            <input type="hidden" name="amount" value="<?php echo $amount; ?>" id="amount"/>
            <input type="hidden" name="firstname" value="<?php echo $firstName; ?>"/>
            <input type="hidden" name="lastname" value="<?php echo $lastName; ?>"/>
            <input type="hidden" name="phone" value="<?php echo $phone; ?>"/>
            <input type="hidden" name="email" value="<?php echo $email; ?>"/>
            <input type="hidden" name="req_time" value="<?php echo $req_time; ?>"/>
            <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>"/>
            <input type="hidden" name="payment_option" value="<?php echo $payment_option; ?>"/>
            <input type="hidden" name="return_url" value="<?php echo $return_url; ?>"/>
            <input type="hidden" name="cancel_url" value="<?php echo $cancel_url; ?>"/>
            <input type="hidden" name="continue_success_url" value="<?php echo $continue_success_url; ?>"/>
            <input type="hidden" name="currency" value="<?php echo $currency; ?>"/>
        </form>
    </div>
    <!— end Modal content—>
</div>
<!— End Popup Checkout Form —>

<!— Page Content —>
<div class="container" style="margin-top: 75px;margin: 0 auto;">
    <div style="width: 200px;margin: 0 auto;display: none;">
        <div class="wpr_payment_option">
            <div style="margin-top: 10px">
                <input type="radio" name="payment_option" class="payment_option" style="margin: 14px;float: left;" checked value="<?= $payment_option?>">
                <label class="paymentOption" for="khqr">
                    <img class="cardType" src="logos/ic_KHQR_x2.png">
                    <span class="detailCard002" style="margin-left: 16px; float: right; position: absolute;">
                    <strong><span class="titleCard">ABA KHQR</span><br/></strong>
                    <span class="detailCard003" style="margin-top: 5px;">Scan to pay with any banking app</span>
                </span>
                </label>
            </div>

        </div>
        <h2>TOTAL: 0.01</h2>
        <input type="button" id="checkout_button" value="Checkout Now">
    </div>
</div>
<!— End Page Content —>


<script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>

<script>
    $(document).ready(function(){
        setTimeout(() => {
            $('#checkout_button').click()
        }, 1000);
        $('#checkout_button').click(function(){
            // $('#aba_merchant_request').append($(".payment_option:checked"));
            AbaPayway.checkout();
        });
    });
</script>
<!— End —>
</body>
</html>

