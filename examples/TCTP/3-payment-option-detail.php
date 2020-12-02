<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('360360000000200', '4AC61F32A209A56B95712E0394E44AE620DD37ACD27C41AB64F4A99B22751420');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$transaction = new \Growinc\Payment\Transaction();

$payment_token = 'kSAops9Zwhos8hSTSeLTUR+tyAgvRuHaDlsbYFjBq/YD+2OHoRF1EY9XnCPtZpjrDV3iB84FoJEQv21j1H+YGWKF16pVq5LtSz9aefuQJ5p+PSG6xBBJPnMSu9LenTJu';

try {

	$payment_option = $vendor->GetPaymentOption(
			$transaction,
			$payment_token
		);
	extract($payment_option);
	// print_r($response);
	print_r($payment_option);
	// Return array
	/*
	Array
	(
		[content] =>
			{
				"status": "000",
				"data": {
					"paymentToken": "kSAops9Zwhos8hSTSeLTUfltbZLm2Z\/+WNY4PRQyyQ7N+HK4Y9I+mTfRiYSS7Y7czz9ZVDFu8duCksVtIUc17+w2AE\/DHGR9zSQLrwgQVH8Z8KCiGSolxcgjQ886oe\/o",
					"merchantDetails": {
						"id": "360360000000200",
						"name": "PT. Growinc Teknologi Indonesia",
						"address": "Foresta Business Loft 5\n\nLengkong Kulon, Pagedangan, Tangerang, Banten",
						"email": "info@growinc.co.id",
						"logoUrl": "https:\/\/pgw-static-sandbox.s3.amazonaws.com\/images\/merchantlogo\/360360000000200.png",
						"bannerUrl": null
					},
					"transactionDetails": {
						"amount": "100,100.00",
						"currencyCode": "IDR",
						"invoiceNo": "10100",
						"description": "PAYMENT"
					},
					"channelCategories": [{
						"groups": [{
							"sequenceNo": 1,
							"name": "Credit Card Payment",
							"code": "CC",
							"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/cc.png",
							"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
							"default": true,
							"expiration": false
						}],
						"sequenceNo": 1,
						"name": "Global Card",
						"code": "GCARD",
						"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/gcard.png",
						"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
						"default": true,
						"expiration": false
					}, {
						"groups": [{
							"sequenceNo": 1,
							"name": "Wallet Payment",
							"code": "EWALLET",
							"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/wallet.png",
							"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
							"default": false,
							"expiration": false
						}],
						"sequenceNo": 2,
						"name": "Digital Payment",
						"code": "DPAY",
						"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/dpay.png",
						"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
						"default": false,
						"expiration": false
					}, {
						"groups": [{
							"sequenceNo": 1,
							"name": "Pay at Counter",
							"code": "OTCTR",
							"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/apm.png",
							"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
							"default": false,
							"expiration": false
						}],
						"sequenceNo": 3,
						"name": "Over The Counter",
						"code": "COUNTER",
						"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/counter.png",
						"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
						"default": false,
						"expiration": false
					}, {
						"groups": [{
							"sequenceNo": 1,
							"name": "ATM",
							"code": "ATM",
							"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/atm.png",
							"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
							"default": false,
							"expiration": false
						}],
						"sequenceNo": 4,
						"name": "ATM \/ Kiosk",
						"code": "SSM",
						"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/ssm.png",
						"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
						"default": false,
						"expiration": false
					}, {
						"groups": [{
							"sequenceNo": 1,
							"name": "Internet \/ Mobile Banking",
							"code": "IMBANK",
							"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/imbank.png",
							"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
							"default": false,
							"expiration": false
						}],
						"sequenceNo": 5,
						"name": "Internet \/ Mobile Banking",
						"code": "IMBANK",
						"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/imbank.png",
						"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
						"default": false,
						"expiration": false
					}, {
						"groups": [{
							"sequenceNo": 1,
							"name": "Web pay \/ Direct Debit",
							"code": "WEBPAY",
							"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/webpay.png",
							"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
							"default": false,
							"expiration": false
						}],
						"sequenceNo": 6,
						"name": "Web pay \/ Direct Debit",
						"code": "WEBPAY",
						"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/webpay.png",
						"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/.png",
						"default": false,
						"expiration": false
					}],
					"respCode": "0000",
					"respDesc": "Success"
				}
			}
		[status_code] => 200
	)
	*/

	$payment_option_detail = $vendor->GetPaymentOptionDetail(
			$transaction,
			$payment_token,
			// Available payment channel for GTI
			// 'GCARD', 'CC'
				// MasterCard. Visa, & JCB --- channelCode = CC
			// 'DPAY', 'EWALLET'
				// LinkAja --- channelCode = LINKAJA
				// OVO --- channelCode = OVO
				// ShopeePay --- channelCode = SHPPAY
			// 'COUNTER', 'OTCTR'
				// Indomaret --- channelCode = 123, agentCode = INDOMARET, agentChannelCode = OVERTHECOUNTER
			'SSM', 'ATM'
				// Bank Lain --- channelCode = 123, agentCode = BANK_OTHER, agentChannelCode = ATM
				// BCA --- channelCode = 123, agentCode = SPRINT, agentChannelCode = ATM
				// BNI --- channelCode = 123, agentCode = BNI, agentChannelCode = ATM
				// BNI Syariah --- channelCode = 123, agentCode = BNIS, agentChannelCode = ATM
				// CIMB NIAGA --- channelCode = 123, agentCode = CIMBVA, agentChannelCode = ATM
				// MANDIRI --- channelCode = 123, agentCode = MANDIRI, agentChannelCode = ATM
				// MAYBANK --- channelCode = 123, agentCode = BIIVA, agentChannelCode = ATM
				// PERMATA --- channelCode = 123, agentCode = PERMATA, agentChannelCode = ATM
			// 'IMBANK', 'IMBANK'
				// Bank BII --- channelCode = 123, agentCode = IDM2U, agentChannelCode = IBANKING
				// Bank Lain --- channelCode = 123, agentCode = BANK_OTHER, agentChannelCode = IBANKING
				// BNI --- channelCode = 123, agentCode = BNI, agentChannelCode = IBANKING
				// BNI Syariah --- channelCode = 123, agentCode = BNIS, agentChannelCode = IBANKING
				// CIMB NIAGA --- channelCode = 123, agentCode = CIMBVA, agentChannelCode = IBANKING
				// MANDIRI --- channelCode = 123, agentCode = MANDIRI, agentChannelCode = IBANKING
				// MAYBANK --- channelCode = 123, agentCode = MAYBANK, agentChannelCode = IBANKING
				// PERMATA --- channelCode = 123, agentCode = PERMATA, agentChannelCode = IBANKING
			// 'WEBPAY', 'WEBPAY'
				// Octomobile --- channelCode = 123, agentCode = CIMBCLICKS, agentChannelCode = WEBPAY
		);
	extract($payment_option_detail);
	// print_r($response);
	print_r($payment_option);
	// Return array
	/*
	Array
	(
		[content] =>
			{
				"status": "000",
				"data": {
					"totalChannel": 1,
					"name": "Web pay \/ Direct Debit",
					"categoryCode": "WEBPAY",
					"groupCode": "WEBPAY",
					"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/webpay.png",
					"channels": [{
						"sequenceNo": 6,
						"name": "Octomobile",
						"currencyCodes": ["IDR"],
						"iconUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/icon\/CIMBCLICKS.png",
						"logoUrl": "https:\/\/d27uu9vmlo4gwh.cloudfront.net\/images\/v4\/images\/logo\/CIMBCLICKS.png",
						"payment": {
							"code": {
								"channelCode": "123",
								"agentCode": "CIMBCLICKS",
								"agentChannelCode": "WEBPAY"
							},
							"input": {
								"name": "O",
								"email": "M",
								"mobileNo": "O"
							},
							"validation": {
								"name": "^(?!\\s*$)[-a-zA-Z' ''.']{1,}$",
								"email": "^(([^<>()\\[\\]\\\\.,;:\\s@\"]+(\\.[^<>()\\[\\]\\\\.,;:\\s@\"]+)*)|(\".+\"))@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}])|(([a-zA-Z\\-0-9]+\\.)+[a-zA-Z]{2,}))$",
								"mobileNo": "[0-9]+",
								"additional": {
									"cardNo": null,
									"amount": {
										"min": 0,
										"max": 0
									}
								}
							}
						},
						"isDown": false
					}],
					"validation": null,
					"configuration": {
						"payment": {
							"tokenize": false,
							"tokenizeOnly": false,
							"cardTokenOnly": false,
							"immediatePayment": false,
							"fx": {}
						},
						"notification": {
							"facebook": false,
							"whatsApp": false,
							"line": false
						}
					},
					"respCode": "0000",
					"respDesc": "Success"
				}
			}
		[status_code] => 200
	)
	*/

} catch (\Throwable $e) {
	echo 'Get payment option failed: ' . $e->getMessage() . ':' . $e->getCode();
}
