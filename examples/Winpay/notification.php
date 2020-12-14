<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('9220fbdeb1d115a4f2e9b2636edc24cc','5b74d200096570de0280b9838c7af1ab');
$vendor = new \Growinc\Payment\Vendors\Winpay($init);

try {
	// should check no_reff (check on merchant side) and id_transaksi (winpay transaction id)
	$raw_data = file_get_contents("php://input");
	if (!$raw_data) {
		$raw_data = '{
		  "id_transaksi": "5757636",
		  "no_reff": "7891092505",
		  "response_code": "00",
		  "id_produk": "SCPIMNDRCP",
		  "method_code": "MANDIRICP",
		  "keterangan": "Transaksi anda berhasil"
		}';
	}
	$request = (object) json_decode($raw_data);

	$result = $vendor->Callback($request);

	extract($result);
	print_r($result);
} catch (\Throwable $e) {
	echo 'Callback failed: ' . $e->getMessage();
}
