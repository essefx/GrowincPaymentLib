<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
	// Selaras Devel
	// 'SB-Mid-client-dVvuSh1E6IUAa_4H', // Client Key
	// 'SB-Mid-server-0XbM92nnkZGBicN0BM-smsoz' // Server Key
	// Selaras Prod
	'Mid-client-sspTptaqbt2Xyq_q', // Client Key
	'Mid-server-nraF1k8otJqrXLwnL7pus1eB' // Server Key
);
// $host = 'https://api.sandbox.midtrans.com/'; // Devel
$host = 'https://api.midtrans.com/'; // Prod
$init->setPaymentURL($host);

// For Gopay
$init->setCallbackURL('https://a.g-dev.io/secure/callback/demo');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081212121313');
$transaction->setCountrycode('IDN');
//
$transaction->setItem('Apple');
$transaction->setAmount(100000);
$transaction->setDescription('Pembelian Elektronik');

// Credit card
$transaction->setCardNumber('4811111111111114');
$transaction->setCardExpMonth('12');
$transaction->setCardExpYear('24');
$transaction->setCardCvv('123');
$init->setTokenURL($host . 'v2/token');

/*	paymentType AKA paymentMethod:
	bank transfer
		bank_transfer -> va
			permata, bni, bca, bri
		echannel (mandiri)
	internet banking (redirect url)
		bca_klikpay
		bca_klikbca (not activated)
		bri_epay (not activated)
		cimb_clicks
		danamon_online
	E-wallet
		qris (not activated)
		gopay
		shopeepay (not activated)
	telkomsel_cash (not activated)
	mandiri_ecash
	Over the Counter
		cstore
			indomaret
			alfamart
	akulaku
	credit_card
*/

/*------------------------------ V V V Start of Bank transfer ---------- */
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('bank_transfer,bni');
// $transaction->setPaymentMethod('bank_transfer,bca');
// $transaction->setPaymentMethod('bank_transfer,bri');
// $transaction->setPaymentMethod('echannel'); // Mandiri
/*------------------------------ A A A End of Bank transfer ---------- */

/*------------------------------ V V V Start of E-wallet ---------- */
// $transaction->setPaymentMethod('qris'); // Payment channel is not activated
$transaction->setPaymentMethod('gopay');
// $transaction->setPaymentMethod('shopeepay'); // Payment channel is not activated
/*------------------------------ A A A End of E-wallet ---------- */

/*------------------------------ V V V Start of Convenience store ---------- */
// $transaction->setPaymentMethod('cstore,indomaret');
// $transaction->setPaymentMethod('cstore,alfamart');
/*------------------------------ A A A End of Convenience store ---------- */

// midtrans only for bca_klikbca
// $transaction->setCustomerUserID('midtrans1012');

$vendor = new \Growinc\Payment\Vendors\Midtrans($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	extract($result);
	print_r($result);
	/* // Success VA BCA
	{
		"status": "000",
		"data": {
			"status_code": "201",
			"status_message": "Success, Bank Transfer transaction is created",
			"transaction_id": "c5b2e8cb-bb78-484d-a73f-b73eaa7b6e1b",
			"order_id": "0025325335",
			"merchant_id": "G072317714",
			"gross_amount": "100000.00",
			"currency": "IDR",
			"payment_type": "bank_transfer",
			"transaction_time": "2021-07-03 22:15:34",
			"transaction_status": "pending",
			"va_numbers": [{
				"bank": "bca",
				"va_number": "17714059768"
			}],
			"fraud_status": "accept"
		}
	}
	*/
	/* // Success VA Permata
	{
		"status": "000",
		"data": {
			"status_code": "201",
			"status_message": "Success, PERMATA VA transaction is successful",
			"transaction_id": "2519f174-feea-4408-989a-713a62b6b9d4",
			"order_id": "0025325392",
			"gross_amount": "100000.00",
			"currency": "IDR",
			"payment_type": "bank_transfer",
			"transaction_time": "2021-07-03 22:16:31",
			"transaction_status": "pending",
			"fraud_status": "accept",
			"permata_va_number": "177009624038289",
			"merchant_id": "G072317714"
		}
	}
	*/
	/* // Success QRIS
	{
		"status": "000",
		"data": {
			"status_code": "201",
			"status_message": "QRIS transaction is created",
			"transaction_id": "4ab09c7e-7cdb-4791-b02f-c4749afde6d2",
			"order_id": "0025325654",
			"merchant_id": "G072317714",
			"gross_amount": "100000.00",
			"currency": "IDR",
			"payment_type": "qris",
			"transaction_time": "2021-07-03 22:20:53",
			"transaction_status": "pending",
			"fraud_status": "accept",
			"actions": [{
				"name": "generate-qr-code",
				"method": "GET",
				"url": "https:\/\/api.sandbox.veritrans.co.id\/v2\/qris\/4ab09c7e-7cdb-4791-b02f-c4749afde6d2\/qr-code"
			}],
			"qr_string": "00020101021226620014COM.GO-JEK.WWW011993600914307231771410210G0723177140303UKE51440014ID.CO.QRIS.WWW0215AID2975932007870303UKE5204302453033605802ID5906VOGame6015JAKARTA SELATAN6105123205409100000.0062475036a150227e-50c4-4b82-8b9b-5532d5df934e0703A0163041006",
			"acquirer": "gopay"
		}
	}
	*/
	/* // Success Alfamart
	{
		"status": "000",
		"data": {
			"status_code": "201",
			"status_message": "Success, cstore transaction is successful",
			"transaction_id": "992154fc-66ff-49ea-800d-38c4681e8d3f",
			"order_id": "0025325773",
			"merchant_id": "G072317714",
			"gross_amount": "100000.00",
			"currency": "IDR",
			"payment_type": "cstore",
			"transaction_time": "2021-07-03 22:22:53",
			"transaction_status": "pending",
			"fraud_status": "accept",
			"payment_code": "7231279656124571",
			"store": "alfamart"
		}
	}
	*/
	// print_r($result);
	//
	// Get  PSR7 object
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse());
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
