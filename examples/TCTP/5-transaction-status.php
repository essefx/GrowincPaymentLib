<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('360360000000200', '4AC61F32A209A56B95712E0394E44AE620DD37ACD27C41AB64F4A99B22751420');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$payment_token = 'kSAops9Zwhos8hSTSeLTUeewPxlj0L9SBSDFG5q+NfL4R+6NMcBFClkhmhHtgYIOh3FQltsT5zStIxm0njenafc3pWL0PTjzQVxbZ+fxfn9yafTj43cxx1sip/WVaSaG';

try {

	$transaction_status = $vendor->TransactionStatus(
			$payment_token
		);
	extract($transaction_status);
	// print_r($response);
	print_r($transaction_status);
	// Return array
	/*
	// Success
	Array
	(
		[content] =>
			{
				"status": "2000",
				"data": {
					"locale": "en",
					"additionalInfo": {
						"merchantDetails": {
							"name": "PT. Growinc Teknologi Indonesia",
							"address": "Foresta Business Loft 5\n\nLengkong Kulon, Pagedangan, Tangerang, Banten",
							"email": "info@growinc.co.id",
							"logoUrl": "https:\/\/pgw-static-sandbox.s3.amazonaws.com\/images\/merchantlogo\/360360000000200.png"
						},
						"transactionDetails": {
							"dateTime": "20201202181152",
							"agentCode": "KTC",
							"channelCode": "VI",
							"data": "411111XXXXXX1111",
							"amount": "122,657.00",
							"currencyCode": "IDR",
							"description": "PAYMENT FOR 1606907489",
							"invoiceNo": "1606907489"
						},
						"paymentResultDetails": {
							"code": "00",
							"description": "Transaction is successful.",
							"autoRedirect": false,
							"redirectImmediately": false,
							"autoRedirectionTimer": 5000,
							"frontendReturnUrl": "https:\/\/ibank.growinc.dev\/oanwef4851ashrb\/pg\/dk\/redapi_form\/",
							"frontendReturnData": "eyJsb2NhbGUiOm51bGwsImludm9pY2VObyI6IjE2MDY5MDc0ODkiLCJjaGFubmVsQ29kZSI6IkNDIiwicmVzcENvZGUiOiIyMDAwIiwicmVzcERlc2MiOiJUcmFuc2FjdGlvbiBpcyBjb21wbGV0ZWQsIHBsZWFzZSBkbyBwYXltZW50IGlucXVpcnkgcmVxdWVzdCBmb3IgZnVsbCBwYXltZW50IGluZm9ybWF0aW9uLiJ9"
						}
					},
					"invoiceNo": "1606907489",
					"channelCode": "CC",
					"respCode": "2000",
					"respDesc": "Transaction is completed, please do payment inquiry request for full payment information."
				}
			}
		[status_code] => 200
	)

	// Failed
	Array
	(
		[content] =>
			{
				"status": "2000",
				"data": {
					"locale": "en",
					"additionalInfo": {
						"merchantDetails": {
							"name": "PT. Growinc Teknologi Indonesia",
							"address": "Foresta Business Loft 5\n\nLengkong Kulon, Pagedangan, Tangerang, Banten",
							"email": "info@growinc.co.id",
							"logoUrl": "https:\/\/pgw-static-sandbox.s3.amazonaws.com\/images\/merchantlogo\/360360000000200.png"
						},
						"transactionDetails": {
							"dateTime": "20201201162427",
							"agentCode": "OVO",
							"channelCode": "OV",
							"data": "",
							"amount": "100,100.00",
							"currencyCode": "IDR",
							"description": "PAYMENT",
							"invoiceNo": "10100"
						},
						"paymentResultDetails": {
							"code": "01",
							"description": "Transaction failed (4093)",
							"autoRedirect": false,
							"redirectImmediately": false,
							"autoRedirectionTimer": 5000,
							"frontendReturnUrl": "https:\/\/ibank.growinc.dev\/oanwef4851ashrb\/pg\/dk\/redapi_form\/",
							"frontendReturnData": "eyJsb2NhbGUiOm51bGwsImludm9pY2VObyI6IjEwMTAwIiwiY2hhbm5lbENvZGUiOiJPVk8iLCJyZXNwQ29kZSI6IjIwMDAiLCJyZXNwRGVzYyI6IlRyYW5zYWN0aW9uIGlzIGNvbXBsZXRlZCwgcGxlYXNlIGRvIHBheW1lbnQgaW5xdWlyeSByZXF1ZXN0IGZvciBmdWxsIHBheW1lbnQgaW5mb3JtYXRpb24uIn0="
						}
					},
					"invoiceNo": "10100",
					"channelCode": "OVO",
					"respCode": "2000",
					"respDesc": "Transaction is completed, please do payment inquiry request for full payment information."
				}
			}
		[status_code] => 200
	)
	*/

} catch (\Throwable $e) {
	echo 'Transaction status failed: ' . $e->getMessage() . ':' . $e->getCode();
}
