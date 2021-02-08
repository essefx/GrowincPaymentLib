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
		'VoGame Indonesia : 33660', // Name & MID
		'bot33660 : p@ssw0rd' // UserID & Pass
	);
// Dev URL
$init->setPaymentURL('https://dev.faspay.co.id/cvr/300011/10'); // Post Data Transaction
// Live URL
// $init->setPaymentURL('https://web.faspay.co.id/cvr/300011/10'); // Post Data Transaction

$order_id = time();
$invoice_no = 'INV' . $order_id;

$transaction = new \Growinc\Payment\Transaction();
$transaction->setOrderID($order_id);
$transaction->setInvoiceNo($invoice_no);
$transaction->setCurrency('IDR');
//
$transaction->setItem('Apple');
$transaction->setAmount(rand(5000,10000) * 100);
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
$transaction->setPaymentMethod('825');

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
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getCode();
}
