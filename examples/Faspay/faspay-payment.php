<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

/*
Merchant name: VoGame Indonesia
Merchant id: 33660
User id: bot33660
Password: p@ssw0rd
*/
$init = new \Growinc\Payment\Init(
		// 'VoGame Indonesia : 33660', // Name & MID
		// 'bot33660 : p@ssw0rd' // UserID & Pass
		'VoGame Indonesia : 33660', // Name & MID
		'bot33660 : dtehsbsB' // UserID & Pass
		// 'PT Growinc Teknologi Indonesia : 33495', // Name & MID
		// 'bot33495 : p@ssw0rd' // UserID & Pass
	);
// Dev URL
// $init->setPaymentURL('https://dev.faspay.co.id/'); // Post Data Transaction
// Live URL
$init->setPaymentURL('https://web.faspay.co.id/'); // Post Data Transaction

$order_id = time();
$invoice_no = 'INV' . $order_id;

$transaction = new \Growinc\Payment\Transaction();
$transaction->setOrderID($order_id);
$transaction->setInvoiceNo($invoice_no);
$transaction->setCurrency('IDR');
//
$transaction->setItem('Apple');
$transaction->setAmount(rand(5000,10000) * 10);
$transaction->setDescription('PAYMENT FOR ' . $invoice_no);
//
$transaction->setCustomerName('Human Warrior');
$transaction->setCustomerEmail('human@warrior.com');
$transaction->setCustomerPhone('081812345678');
$transaction->setCustomerAddress('Jakarta Selatan');
$transaction->setCountrycode('ID');

// Payment Method Supported:
/*
"payment_channel": [{
	"pg_code": "807",
	"pg_name": "Akulaku"
}, {
	"pg_code": "801",
	"pg_name": "BNI Virtual Account"
}, {
	"pg_code": "825",
	"pg_name": "CIMB VA"
}, {
	"pg_code": "701",
	"pg_name": "DANAMON ONLINE BANKING"
}, {
	"pg_code": "708",
	"pg_name": "Danamon VA"
}, {
	"pg_code": "302",
	"pg_name": "LinkAja"
}, {
	"pg_code": "802",
	"pg_name": "Mandiri Virtual Account"
}, {
	"pg_code": "814",
	"pg_name": "Maybank2U"
}, {
	"pg_code": "408",
	"pg_name": "MAYBANK VA"
}, {
	"pg_code": "812",
	"pg_name": "OVO"
}, {
	"pg_code": "402",
	"pg_name": "Permata"
}, {
	"pg_code": "711",
	"pg_name": "Shopee Pay"
}, {
	"pg_code": "818",
	"pg_name": "Sinarmas Virtual Account"
}, {
	"pg_code": "420",
	"pg_name": "UNICount-Rupiah"
}],
*/
// $selected_payment_channel = '801';
// $selected_payment_channel = '812';
// $selected_payment_channel = '402';
$selected_payment_channel = '711';
$transaction->setPaymentMethod($selected_payment_channel);

$vendor = new \Growinc\Payment\Vendors\Faspay($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"response": "Transmisi Info Detil Pembelian",
			"trx_id": "3366080100000042",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"bill_no": "1612772364",
			"bill_items": [{
				"product": "Apple",
				"amount": "928000",
				"qty": "1",
				"payment_plan": "1",
				"tenor": "00",
				"merchant_id": "33660"
			}],
			"response_code": "00",
			"response_desc": "Sukses",
			"redirect_url": "https:\/\/dev.faspay.co.id\/pws\/100003\/0830000010100000\/f04740f3492b5f0b249cb1f620d56c50fee21e61?trx_id=3366080100000042&merchant_id=33660&bill_no=1612772364"
		}
	}
	*/


	// For ShopeePay QRIS
	if ($selected_payment_channel == '711') {
		$content = (object) json_decode($result['response']['content']);
		if (!empty($content->data->redirect_url)) {
			$payment_url = $content->data->redirect_url;
			$result = $vendor->ParseQR($payment_url);
			extract($result);
			print_r($response);
			// Success
			/*
			{
				"status": "000",
				"data": {
					"payment_url": "https:\/\/dev.faspay.co.id\/pws\/100003\/0830000010100000\/1ce9c0c671104fafe6728b5e2af4480bb484d00d?trx_id=3366071100000638&merchant_id=33660&bill_no=1612936546",
					"qr_code": "https:\/\/dev.faspay.co.id\/__assets\/qr\/33660-3366071100000638.png"
				}
			}
			*/
			$content = (object) json_decode($result['response']['content']);
			$qr_code = $content->data->qr_code;
			print_r($qr_code);
		}
	}


	// Call ParsePG, Redirect to OVO, ask Node to hit send to user phone number ------------ used on OVO only
	if ($selected_payment_channel == '812') {
		$content = (object) json_decode($result['response']['content']);
		if (!empty($content->data->redirect_url)) {
			$payment_url = $content->data->redirect_url;
			$result = $vendor->ParsePaymentPage(
					'ovo',
					$payment_url,
					'082298438769'
				);
			extract($result);
			print_r($response);
			// Success
			/*
			{
				"status": "000",
				"data": {
					"payment_url": "https:\/\/dev.faspay.co.id\/pws\/100003\/0830000010100000\/468c236cea48601a3b0b4c512e830ae7215060aa?trx_id=3366081200000281&merchant_id=33660&bill_no=1612789528",
					"phone_number": "082298438769"
				}
			}
			*/
			$content = (object) json_decode($result['response']['content']);
			$phone_number = $content->data->phone_number;
			print_r($phone_number);
		}
	}


} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
