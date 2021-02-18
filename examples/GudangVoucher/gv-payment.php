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
// $transaction->setAmount(rand(5000,10000) * 100);
$transaction->setAmount(10000);
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
// $transaction->setPaymentMethod('cstore,alfamart');
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
			"order_id": "0013619238",
			"qris_url": "https:\/\/www.gudangvoucher.com\/merchant\/cetak.php?type=3&number=MDAwMjAxMDEwMjEyMjY3MzAwMjFDT00uR1VEQU5HVk9VQ0hFUi5XV1cwMTE4OTM2MDA5MTYzMDAyNDc3NTkwMDIxNUdWMjIwMDAwMjQ3NzU5MDAzMDNVQkU1MTQ1MDAxNUlELk9SLkdQTlFSLldXVzAyMTVJRDIwMjEwNjI5ODYwNDEwMzAzVUJFNTIwNDU5NDU1MzAzMzYwNTQwNTEwMDAwNTgwMklENTkwNFZQQVk2MDE1SkFLQVJUQSBTRUxBVEFONjEwNTEyMjQwNjIzMzAxMDgwMDQ4MDEwMDA1MTcyMTAyMTgxMDMzNTdlV3RNZDYzMDQwRTlC",
			"amount": 10000,
			"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619238&email=lorem@ipsum.com&signature=70cfd8b65d60dac322f4e99900f3ca9e&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
			"expired_at": "2021-02-18 14:33:57"
		}
	}
	*/
	/*  Alfamart example
	{
		"status": "000",
		"data": {
			"order_id": "0013619293",
			"cstore": "alfamart",
			"fee": 2500,
			"amount": 12500,
			"pay_code": "00480105",
			"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619293&email=lorem@ipsum.com&signature=6ddc1ecaf8cf93afc07649107b12d3d2&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
			"expired_at": "2021-02-18 14:34:52"
		}
	}
	*/
	/*  VA example
	{
		"status": "000",
		"data": {
			"order_id": "0013619123",
			"bank_code": "cimb_niaga",
			"fee": 1000,
			"amount": 11000,
			"pay_code": "3049010100480089",
			"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619123&email=lorem@ipsum.com&signature=d2bf279d8fffce30a365d9e64aa35203&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
			"expired_at": "2021-02-18 14:32:02"
		}
	}
	*/
	/* All option shown
	{
		"status": "000",
		"data": {
			"order_id": "0013619064",
			"amount": 10000,
			"bank_pay_codes": [{
				"bank_code": "bca",
				"fee": 4400,
				"amount": 14400,
				"pay_code": "7700610100480083"
			}, {
				"bank_code": "permata",
				"fee": 1500,
				"amount": 11500,
				"pay_code": "8992010100480083"
			}, {
				"bank_code": "bni",
				"fee": 2000,
				"amount": 12000,
				"pay_code": "8558010100480083"
			}, {
				"bank_code": "cimb_niaga",
				"fee": 1000,
				"amount": 11000,
				"pay_code": "3049010100480083"
			}, {
				"bank_code": "atm_bersama",
				"fee": 1500,
				"amount": 11500,
				"pay_code": "500501010100480083"
			}],
			"cstore_pay_codes": {
				"cstore_code": "alfamart",
				"fee": "4400",
				"amount": "12500",
				"pay_code": "00480083"
			},
			"qris_url": "https:\/\/www.gudangvoucher.com\/merchant\/cetak.php?type=3&number=MDAwMjAxMDEwMjEyMjY3MzAwMjFDT00uR1VEQU5HVk9VQ0hFUi5XV1cwMTE4OTM2MDA5MTYzMDAyNDc3NTkwMDIxNUdWMjIwMDAwMjQ3NzU5MDAzMDNVQkU1MTQ1MDAxNUlELk9SLkdQTlFSLldXVzAyMTVJRDIwMjEwNjI5ODYwNDEwMzAzVUJFNTIwNDU5NDU1MzAzMzYwNTQwNTEwMDAwNTgwMklENTkwNFZQQVk2MDE1SkFLQVJUQSBTRUxBVEFONjEwNTEyMjQwNjIzMzAxMDgwMDQ4MDA4MzA1MTcyMTAyMTgxMDMxMDMzaUxaVDYzMDRERDA1",
			"gv_wallet_payment_url": "https:\/\/www.gudangvoucher.com\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619064&email=lorem@ipsum.com&custom_redirect=https:\/\/a.g-dev.io\/secure\/callback\/demo",
			"payment_url": "https:\/\/www.gudangvoucher.com\/pg\/v3\/payment.php?merchantid=878&amount=10000&product=Apple&custom=0013619064&email=lorem@ipsum.com&signature=7d9b584ee8e5498f9df9b03f38eda9ad&custom_redirect=https%3A%2F%2Fa.g-dev.io%2Fsecure%2Fcallback%2Fdemo",
			"expired_at": "2021-02-18 14:31:03"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
