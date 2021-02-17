<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'878', // MerchantID
		'9485a72ed5fab4245f22ef97' // MerchantKey
	);
// Development
// $init->setPaymentURL('https://www.gudangvoucher.com/pg/v3/payment-sandbox.php');
// Production
$init->setPaymentURL('https://www.gudangvoucher.com/pg/v3/payment.php');
//
$init->setCallbackURL('https://a.g-dev.io/secure/callback/demo');
$init->setReturnURL('https://a.g-dev.io/secure/callback/demo');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
//
$transaction->setItem('Apple');
$transaction->setAmount(rand(5000,10000) * 100);
$transaction->setDescription('Pembelian Elektronik');
//
// Payment Method Supported:
// 1. bank_transfer (VA) ----------- CURRENTLY ONLY THIS SUPPORTED
// 	bca
// 	permata
// 	bni
// 	cimb_niaga
// 	atm_bersama
// 2. qris
// 3. cstore (retail)
// 	alfamart
// 	indomaret
//
// $transaction->setPaymentMethod('bank_transfer,bca');
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('bank_transfer,bni');
// $transaction->setPaymentMethod('bank_transfer,cimb_niaga');
// $transaction->setPaymentMethod('bank_transfer,atm_bersama');
// $transaction->setPaymentMethod('qris');
$transaction->setPaymentMethod(''); // use blank to show all option at once

$vendor = new \Growinc\Payment\Vendors\GudangVoucher($init);

try {
	// $result = $vendor->RedirectPayment($transaction);
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	// Success
	/*  QRIS example
		{
		"status": "000",
		"data": {
			"order_id": "0013551743",
			"amount": 967900,
			"qris_url": "https:\/\/www.gudangvoucher.com\/merchant\/cetak.php?type=3&number=MDAwMjAxMDEwMjEyMjY3MzAwMjFDT00uR1VEQU5HVk9VQ0hFUi5XV1cwMTE4OTM2MDA5MTYzMDAyNDc3NTkwMDIxNUdWMjIwMDAwMjQ3NzU5MDAzMDNVQkU1MTQ1MDAxNUlELk9SLkdQTlFSLldXVzAyMTVJRDIwMjEwNjI5ODYwNDEwMzAzVUJFNTIwNDU5NDU1MzAzMzYwNTQwNjk2NzkwMDU4MDJJRDU5MDRWUEFZNjAxNUpBS0FSVEEgU0VMQVRBTjYxMDUxMjI0MDYyMzMwMTA4MDA0NzkzMzYwNTE3MjEwMjE3MTU0OTAya2o2R1k2MzA0QzMyOQ==",
			"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=967900&product=Apple&custom=0013551743&email=lorem@ipsum.com&signature=5d91913ed502a88aee523e59e60f5d8f&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
			"expired_at": "2021-02-17 19:49:02"
		}
	}
	*/
	/*  VA example
	{
		"status": "000",
		"data": {
			"order_id": "0013551800",
			"amount": 818700,
			"bank_code": "atm_bersama",
			"fee": 1500,
			"pay_code": "500501010100479338",
			"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=817200&product=Apple&custom=0013551800&email=lorem@ipsum.com&signature=e4b895c6b7529374e457fcae4d109f6a&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
			"expired_at": "2021-02-17 19:50:01"
		}
	}
	*/
	/* All option shown
	{
		"status": "000",
		"data": {
			"order_id": "0013551902",
			"amount": 985100,
			"all_pay_codes": [{
				"amount": 989500,
				"bank_code": "bca",
				"fee": 4400,
				"pay_code": "7700610100479340"
			}, {
				"amount": 986600,
				"bank_code": "permata",
				"fee": 1500,
				"pay_code": "8992010100479340"
			}, {
				"amount": 987100,
				"bank_code": "bni",
				"fee": 2000,
				"pay_code": "8558010100479340"
			}, {
				"amount": 986100,
				"bank_code": "cimb_niaga",
				"fee": 1000,
				"pay_code": "3049010100479340"
			}, {
				"amount": 986600,
				"bank_code": "atm_bersama",
				"fee": 1500,
				"pay_code": "500501010100479340"
			}],
			"qris_payment_url": "https:\/\/www.gudangvoucher.com\/merchant\/cetak.php?type=3&number=MDAwMjAxMDEwMjEyMjY3MzAwMjFDT00uR1VEQU5HVk9VQ0hFUi5XV1cwMTE4OTM2MDA5MTYzMDAyNDc3NTkwMDIxNUdWMjIwMDAwMjQ3NzU5MDAzMDNVQkU1MTQ1MDAxNUlELk9SLkdQTlFSLldXVzAyMTVJRDIwMjEwNjI5ODYwNDEwMzAzVUJFNTIwNDU5NDU1MzAzMzYwNTQwNjk4NTEwMDU4MDJJRDU5MDRWUEFZNjAxNUpBS0FSVEEgU0VMQVRBTjYxMDUxMjI0MDYyMzMwMTA4MDA0NzkzNDAwNTE3MjEwMjE3MTU1MTQyZldESDQ2MzA0RUM4RQ==",
			"gv_wallet_payment_url": "https:\/\/www.gudangvoucher.com\/payment.php?merchantid=878&amount=985100&product=Apple&custom=0013551902&email=lorem@ipsum.com&custom_redirect=https:\/\/a.g-dev.io\/secure\/callback\/demo",
			"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=985100&product=Apple&custom=0013551902&email=lorem@ipsum.com&signature=836cc67c193cf9269a66daffc62cd332&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
			"expired_at": "2021-02-17 19:51:42"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
