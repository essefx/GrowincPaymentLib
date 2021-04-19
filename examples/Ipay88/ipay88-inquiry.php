<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	date_default_timezone_set('Asia/Jakarta');

	require_once __DIR__ . '/../../vendor/autoload.php';

	$init = new \Growinc\Payment\Init('ID01676', '0XScE2NdvU');

	$init->setBaseURI('https://sandbox.ipay88.co.id');
	$init->setRequestURL('https://sandbox.ipay88.co.id');

	$vendor = new \Growinc\Payment\Vendors\Ipay88($init);

	try {
		$result = $vendor->Inquiry((object) [
			'order_id' => '0018824953',
			'amount' => '700000',
		]);
		// extract($result);
		// print_r($response);
		/* Statuses
		00	= Pembayaran sukses.
		Invalid parameters = Parameter yang dikirimkan merchant tidak tepat.
		Record not found = Data tidak ditemukan.
		Incorrect amount = Total yang tidak tepat (berbeda).
		Payment fail = Pembayaran gagal.
		Payment Pending = Pembayaran tertunda dan pelanggan harus membayar di mesin ATM.
		Haven’t Paid (0) = Tagihan belum dibayar atau berhenti di laman pembayaran iPay88.
		Haven’t Paid (1) = Tagihan belum dibayar atau berhenti di laman bank.
		M88Admin = Status pembayaran diubah oleh iPay88 Admin (gagal).
		*/
		print_r($result);
	} catch (\Throwable $e) {
		echo 'Inquiry failed: ' . $e->getMessage();
	}
