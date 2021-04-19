<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	date_default_timezone_set('Asia/Jakarta');

	require_once __DIR__ . '/../../vendor/autoload.php';

	// $private_key_1 = '000f1f4cb5118390cc2ec79af671d617';
	// $private_key_2 = '19c6f7a74281b16c2e70ba485dcf1750';
	// $merchant_key = 'a85b54a715b31119a654928c400c8bb8';
	$private_key_1 = '49f39e2d576fb76a041c2c0aa5423cc9';
	$private_key_2 = '4dd070051527f2ec185c1df3b97a42ca';
	$merchant_key = '1666cc26bcbcdb9c371a00d6c1dc1c56';

	$init = new \Growinc\Payment\Init(
			$private_key_1,
			$private_key_2
		);

	$vendor = new \Growinc\Payment\Vendors\Winpay($init);

	try {
		// should check no_reff (check on merchant side) and id_transaksi (winpay transaction id)
		$raw_data = file_get_contents("php://input");
		if (!$raw_data) {
			// non QRIS
			$raw_data = '{
					"id_transaksi": "0012430479",
					"no_reff": "302665432",
					"response_code": "00",
					"id_produk": "SCPIMNDRCP",
					"method_code": "MANDIRIVA",
					"keterangan": "Transaksi anda berhasil"
				}';
			// QRIS
			/*
			$raw_data = '{
			"id_transaksi": "4929984",
			"no_reff": "5e4ce7cf7901234569",
			"response_code": "00",
			"id_produk": "QRISPAY",
			"method_code": "QRISPAY",
			"keterangan": "Transaksi Anda berhasil",
			"nominal": 50352.47,
			"biaya_layanan": 252.00,
			"tips": 0,
			"nominal_nett": 50100.47,
			"method_name":"QR TRANSFER",
			"qris_data":
				{
					"brand_name":"SPEEDCASH",
					"issuer_reff":"000004929983",
					"buyer_reff":"Tester",
					"sent_time":"2020-11-16T07:58:01.852Z"
				}
			}';
			*/
		}
		$request = (object) json_decode($raw_data);
		$result = $vendor->Callback($request);
		extract($result);
		print_r($response);
	} catch (\Throwable $e) {
		echo 'Callback failed: ' . $e->getMessage() . ':' .  $e->getCode();
	}
