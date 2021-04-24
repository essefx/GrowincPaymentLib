<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	date_default_timezone_set('Asia/Jakarta');

	require_once __DIR__ . '/../../vendor/autoload.php';

	// $init = new \Growinc\Payment\Init('ID01676', '0XScE2NdvU'); // Selaras
	$init = new \Growinc\Payment\Init('ID01625', '1gUbnGkdKA'); // Growinc

	$init->setBaseURI('https://sandbox.ipay88.co.id');
	$init->setPaymentURL('https://sandbox.ipay88.co.id');

	$init->setCallbackURL('http://sandbox.sec2pay.com/secure/callback/demo');
	$init->setReturnURL('http://sandbox.sec2pay.com/secure/callback/demo');

	$transaction = new \Growinc\Payment\Transaction();
	$transaction->setCustomerName('LOREM');
	$transaction->setCustomerEmail('lorem@ipsum.com');
	$transaction->setCustomerPhone('081293145954');
	$transaction->setItem('Game 01');
	$transaction->setAmount(7000);
	// $transaction->setAmount(rand(5000,10000) * 100);
	$transaction->setDescription('Product Game Baru');
	/*
	1. Credit Card
		Credit Card (BCA) = 52
		Credit Card (BRI) = 35
		Credit Card (CIMB) = 42
		Credit Card (CIMB Authorization) = 56
		Credit Card (CIMB IPG) = 34
		Credit Card (Danamon) = 45
		Credit Card (Mandiri) = 53
		Credit Card (Maybank) = 43
		Credit Card (UnionPay) = 54
		Credit Card (UOB) = 46
	2. Online Banking
		BCA KlikPay = 8
		CIMB Clicks = 11
		Muamalat IB = 14
		Danamon Online Banking = 23
	3. ATM Transfer
			BCA Tf = 30
		Maybank VA = 9
		Mandiri ATM = 17
		BCA VA = 25
		BNI VA = 26
		Permata VA = 31
	4. e-Wallet
			ShopeePay = 75
		LinkAja = 13
		OVO = 63
	5. Others
		PayPal = 6
		Kredivo = 55
		Alfamart = 60
		Indomaret = 65
			Akulaku = 71
			Indodana = 70
	*/
	$transaction->setPaymentMethod('63');
	// $transaction->setPaymentMethod('31');
	// $transaction->setPaymentMethod('75');

	$vendor = new \Growinc\Payment\Vendors\Ipay88($init);

	try {
		$result = $vendor->SecurePayment($transaction); // return payment URL
		print_r($result);
		/*
		// BNI VA
		{
			"status": "000",
			"data": {
				"Status": "6",
				"ErrDesc": "",
				"MerchantCode": "ID01676",
				"PaymentId": "26",
				"Currency": "IDR",
				"RefNo": "0019065493",
				"Amount": "700000",
				"Remark": "Transaction 0019065493",
				"Signature": "eBSCIBO9Pchf1vyyHsE6sQMC0YA=",
				"xfield1": "",
				"TransId": "T0053311300",
				"AuthCode": "",
				"VirtualAccountAssigned": "8228024900002215",
				"TransactionExpiryDate": "4\/23\/2021 11:24:10 AM",
				"CheckoutURL": "https:\/\/sandbox.ipay88.co.id\/epayment\/entryv3.asp?CheckoutID=171f1680f6dde6e4b2309fd29eb79da1321874a938d11e156f133bd38f07ea0a&Signature=E1YHn1tXaCT9HRYzyixZyy95qgk%3d"
			}
		}

		// QRIS ShoopePay
		{
			"status": "000",
			"data": {
				"Status": "6",
				"ErrDesc": "",
				"MerchantCode": "ID01676",
				"PaymentId": "75",
				"Currency": "IDR",
				"RefNo": "0018651323",
				"Amount": "744500",
				"Remark": "Transaction 0018651323",
				"Signature": "QuK\/ILMmyTYazxtcTKwdDIWAodY=",
				"xfield1": "",
				"TransId": "T0053242300",
				"AuthCode": "",
				"VirtualAccountAssigned": "https:\/\/api.uat.wallet.airpay.co.id\/v3\/merchant-host\/qr\/download?qr=fSAPBHKcP9SAw1sQnHuvA6HpRYNhThs2j6Ub644MoX",
				"CheckoutURL": "https:\/\/sandbox.ipay88.co.id\/epayment\/entryv3.asp?CheckoutID=ecbea777ebb16f40d3f79cc6919539bc81a3f6b8e93d6b7c13f8f539596de90c&Signature=oiBhz7uyuFoff2JTCiO53JGwcI8%3d"
			}
		}
		*/
	} catch (\Throwable $e) {
		echo 'Payment failed: ' . $e->getMessage();
	}
