<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('360360000000200', '4AC61F32A209A56B95712E0394E44AE620DD37ACD27C41AB64F4A99B22751420');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$payment_token = 'kSAops9Zwhos8hSTSeLTUeewPxlj0L9SBSDFG5q+NfL4R+6NMcBFClkhmhHtgYIOh3FQltsT5zStIxm0njenafc3pWL0PTjzQVxbZ+fxfn9yafTj43cxx1sip/WVaSaG';
$invoice_no = '1606907489';

try {

	$payment_inquiry = $vendor->PaymentInquiry(
			$payment_token,
			$invoice_no
		);
	extract($payment_inquiry);
	// print_r($response);
	print_r($payment_inquiry);
	// Return array
	/*
	// Success
	Array
	(
		[content] =>
			{
				"status": "000",
				"data": {
					"merchantID": "360360000000200",
					"invoiceNo": "1606907489",
					"cardNo": "411111XXXXXX1111",
					"amount": 122657,
					"userDefined1": "",
					"userDefined2": "",
					"userDefined3": "",
					"userDefined4": "",
					"userDefined5": "",
					"currencyCode": "IDR",
					"cardToken": "",
					"recurringUniqueID": "",
					"tranRef": "3417838",
					"referenceNo": "3271261",
					"approvalCode": "159221",
					"eci": "05",
					"transactionDateTime": "20201202181152",
					"agentCode": "KTC",
					"channelCode": "VI",
					"issuerCountry": "US",
					"installmentMerchantAbsorbRate": null,
					"respCode": "0000",
					"respDesc": "Success"
				}
			}
		[status_code] => 200
	)
	*/

} catch (\Throwable $e) {
	echo 'Payment inquiry failed: ' . $e->getMessage() . ':' . $e->getCode();
}
