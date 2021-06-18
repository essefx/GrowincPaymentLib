<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

// $private_key_1 = '000f1f4cb5118390cc2ec79af671d617'; // VG
// $private_key_2 = '19c6f7a74281b16c2e70ba485dcf1750';
// $merchant_key = 'a85b54a715b31119a654928c400c8bb8';
$private_key_1 = '49f39e2d576fb76a041c2c0aa5423cc9'; // GTI
$private_key_2 = '4dd070051527f2ec185c1df3b97a42ca';
$merchant_key = '1666cc26bcbcdb9c371a00d6c1dc1c56';

$init = new \Growinc\Payment\Init(
		$private_key_1,
		$private_key_2
	);

$init->setMerchantKey($merchant_key);

$init->setPaymentURL('https://secure-payment.winpay.id'); // Production URL
// $init->setPaymentURL('https://sandbox-payment.winpay.id'); // Development URL
//
$init->setCallbackURL('https://a.g-dev.io/secure/callback/demo');
$init->setReturnURL('https://a.g-dev.io/secure/callback/demo');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
//
$transaction->setItem('Game 01');
$transaction->setAmount(rand(5000,10000) * 100);
$transaction->setDescription('Product Game Baru');
// $transaction->setOrderID('20210603295621655893');
// $transaction->setInvoiceNo('inv_20210603295621655893');

/*
API Redirect Channel
API ini digunakan untuk mendapatkan kode pembayaran / link pembayaran dari Payment Channel Redirect
-> redirect_channel_code :
	BEBASBAYAR --- XXX NOT AVAILABLE
	DANAMON --- XXX NOT AVAILABLE
	BCAKP
	CIMBC
	BTNONLINE
	BRIEP --- XXX NOT AVAILABLE
	MUAMALAT --- XXX NOT AVAILABLE
	KKWP --- XXX NOT AVAILABLE
	MANDIRICP --- XXX NOT AVAILABLE
	MANDIRIEC --- XXX NOT AVAILABLE

API Payment Code
API ini digunakan untuk mendapatkan kode pembayaran / link pembayaran dari Payment Channel Direct
-> channel_code :
	ALFAMART
	FASTPAY
	INDOMARET --- XXX NOT AVAILABLE

API Bank Transfer
->
	MANDIRIPC
	BCAPC

API Virtual Account
API ini digunakan untuk mendapatkan kode pembayaran / link pembayaran dari Payment Channel Virtual Account
-> va_channel_code :
	BNIVA
	BRIVA
	PERMATAVA
	MANDIRIVA

API QRIS
API ini digunakan untuk mendapatkan kode QR.
QR Code Dinamis hanya berlaku 3 x 24 jam sejak dibuat
QR Code Dinamis hanya dapat digunakan sekali transaksi
QR Code Statis bersifat open nominal, customer dapat input nominal berapapun
-> qris_code :
	QRISPAY
*/
// $transaction->setPaymentMethod('redirect,BCAKP');
// $transaction->setPaymentMethod('redirect,CIMBC');
// $transaction->setPaymentMethod('redirect,BTNONLINE');
// $transaction->setPaymentMethod('cstore,ALFAMART');
// $transaction->setPaymentMethod('cstore,FASTPAY');
// $transaction->setPaymentMethod('bt,MANDIRIPC');
// $transaction->setPaymentMethod('bt,BCAPC');
// $transaction->setPaymentMethod('va,BNIVA');
// $transaction->setPaymentMethod('va,BRIVA');
// $transaction->setPaymentMethod('va,PERMATAVA');
// $transaction->setPaymentMethod('va,MANDIRIVA');
$transaction->setPaymentMethod('qr,QRISPAY');

$vendor = new \Growinc\Payment\Vendors\Winpay($init);

try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	// Success
	/*
	// VA
	{
		"status": "000",
		"data": {
			"rc": "00",
			"rd": "Transaksi Anda sedang dalam proses, Silakan transfer ke akun virtual BNI Anda dengan no akun : 9887888450065292, sebesar Rp. 961.300-. (Batas waktu max : 2021-03-17 19:45)   Terimakasih",
			"request_time": "2021-03-17 17:45:52.290912",
			"data": {
				"reff_id": "311635533",
				"payment_code": "9887888450065292",
				"order_id": "0015977951",
				"request_key": "",
				"url_listener": "https:\/\/a.g-dev.io\/secure\/callback\/demo",
				"payment_method": "BNI VIRTUAL ACCOUNT",
				"payment_method_code": "BNIVA",
				"fee_admin": 0,
				"total_amount": 961300,
				"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/bniva?payid=ddbcf886fd339eb4a5727fc874fa953f"
			},
			"response_time": "2021-03-17 17:45:54.947816"
		}
	}

	// Alfamart
	{
		"status": "000",
		"data": {
			"rc": "00",
			"rd": "Transaksi Anda sedang dalam proses, Anda akan melakukan pembayaran menggunakan Alfamart, Silakan melakukan pembayaran sejumlah IDR 877.400-. Order ID Anda adalah 306474118. RAHASIA Dilarang menyebarkan ke ORANG Tdk DIKENAL",
			"request_time": "2021-02-25 16:22:18.633754",
			"data": {
				"reff_id": "306474118",
				"payment_code": "306474118",
				"order_id": "0014244938",
				"request_key": "",
				"url_listener": "https:\/\/a.g-dev.io\/secure\/callback\/demo",
				"payment_method": "Alfamart",
				"payment_method_code": "ALFAMART",
				"fee_admin": 0,
				"total_amount": 877400,
				"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/alfamart?payid=14f4a4b9013b18c57acc84971a8a34ac"
			},
			"response_time": "2021-02-25 16:22:20.102106"
		}
	}

	// QRIS
	{
		"status": "000",
		"data": {
			"rc": "00",
			"rd": "QR Image is successfully generated",
			"request_time": "2021-04-08 21:56:39.868315",
			"data": {
				"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/qrispay?payid=c65b87430fbba2bf54ca574cb59eebb6",
				"payment_method": "Pembayaran QRIS",
				"payment_method_code": "QRISPAY",
				"fee_admin": 0,
				"total_amount": 888600,
				"order_id": "0017893800",
				"spi_hash": "c65b87430fbba2bf54ca574cb59eebb6",
				"tips": 88860,
				"nominal_mdr": 6220.2,
				"image_qr": "https:\/\/secure-payment.winpay.id\/scqr\/get_image_qr?payid=c65b87430fbba2bf54ca574cb59eebb6"
			},
			"response_time": "2021-04-08 21:56:40.640724"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
