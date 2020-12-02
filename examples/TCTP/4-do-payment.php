<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('360360000000200', '4AC61F32A209A56B95712E0394E44AE620DD37ACD27C41AB64F4A99B22751420');
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result/');
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form/');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('SEAN');
$transaction->setCustomerEmail('essefx@gmail.com');
$transaction->setCustomerPhone('081298983535');
// Choose payment channel
$transaction->setPaymentMethod('123,CIMBVA,ATM');
// $transaction->setPaymentMethod('123,INDOMARET,OVERTHECOUNTER');
// $transaction->setPaymentMethod('OVO');
// $transaction->setPaymentMethod('LINKAJA');
// $transaction->setPaymentMethod('CC');

// For CC payment:
// $transaction->setCardToken('00acbvK78uZDwzpRbVjWkrKGzPyl0cCo5iGsirE1AZvPRXg58PAy1LsaEudBINbZbFHDgpX5Fu/sWHTcPJhByOYbnfApYMzIuWVj2yxE69MCMRHH0Qd3BqwM+PaoJMptmfh4w6/gtQ7KzrnDbwqfz+JbVtp7iyWA2IFlVrOp4onXEBA=U2FsdGVkX18Eaq1ZRNF3/Hvq18qUErwokA2fNRtrFFIPb/pUvXPM03b00zBSn0ko');
// Card token can be generated by example num 2-enc-card

$payment_token = 'kSAops9Zwhos8hSTSeLTUR+tyAgvRuHaDlsbYFjBq/YD+2OHoRF1EY9XnCPtZpjrDV3iB84FoJEQv21j1H+YGWKF16pVq5LtSz9aefuQJ5p+PSG6xBBJPnMSu9LenTJu';

try {

	$do_payment = $vendor->DoPayment(
			$transaction,
			$payment_token
		);
	extract($do_payment);
	// print_r($response);
	print_r($do_payment);
	// Return array
	/*
	Array
	(
		[content] =>
			{
				"status": "000",
				"data": {
					"data": "https:\/\/demo2.2c2p.com\/2C2PFrontEnd\/storedCardPaymentV2\/MPaymentProcess.aspx?token=+\/Kyg6mkWF9mU0vLawzH2iDbYFGWLfBXjsODQSPnMrtbdCqrmKHvkjqI4XKrGnM0",
					"channelCode": "123",
					"respCode": "1003",
					"respDesc": "Transaction is pending for payment, please wait for backend notification."
				}
			}
		[status_code] => 200
	)
	*/

} catch (\Throwable $e) {
	echo 'Do payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
