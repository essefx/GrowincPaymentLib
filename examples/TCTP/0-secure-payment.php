<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('360360000000200', '4AC61F32A209A56B95712E0394E44AE620DD37ACD27C41AB64F4A99B22751420');
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result/');
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form/');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$invoice_no = time();

$transaction = new \Growinc\Payment\Transaction();
$transaction->setInvoiceNo($invoice_no);
$transaction->setDescription('PAYMENT FOR ' . $invoice_no);
$transaction->setAmount(rand(50000, 1000000));
$transaction->setCurrency('IDR');
$transaction->setCurrencyCode(360);
// Customer data
$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081212121313');
// Payment channel selection
// $transaction->setPaymentMethod('CC');
// $transaction->setPaymentMethod('OVO,OVO');
// $transaction->setPaymentMethod('LINKAJA');
$transaction->setPaymentMethod('SSM,123,CIMBVA,ATM');
// $transaction->setPaymentMethod('COUNTER,123,INDOMARET,OVERTHECOUNTER');
// For CC
$transaction->setCardToken('00acGOy9DNhXqSk3bzIt0gLUpjCacQIn7Cz5wkoOpdKGBQW/B0w6kWBVp2RcpoCWb0yire4XlsUP8LG7TiE1SM+5SJOPGWNh5mByjiZm8jBRU2jFbEHZmvOJHcntgq/w2EdkUstqHaM4e/+Zwbl2uvCbl7+Qct+pLdZ54omKJeCVOpI=U2FsdGVkX1+J1KYmioWTmrzlzz6A4rVmZNerY2Y34DyAAttq71vA5xWlRDeXP7y+');

// try {

	// First create payment token
	$payment_token = $vendor->CreatePaymentToken($transaction);
	$content = (object) json_decode($payment_token['response']['content']);
	$payment_token = $content->data->paymentToken;
echo '1. payment_token:';
// print_r($payment_token);
print_r($content);

/*------------------------------ V V V Optional ----------*

	// Call payment option (optional step)
	$payment_option = $vendor->GetPaymentOption(
			$transaction,
			$payment_token
		);
	$content = (object) json_decode($payment_option['response']['content']);
echo '2. payment_option:';
// print_r($payment_option);
print_r($content);
	// Call payment option details (optional step)
	$payment_option_detail = $vendor->GetPaymentOptionDetail(
			$transaction,
			$payment_token,
			// Available payment channel group for GTI
			// 'GCARD', 'CC'
					// MasterCard. Visa, & JCB --- channelCode = CC
			// 'DPAY', 'EWALLET'
					// LinkAja --- channelCode = LINKAJA
					// OVO --- channelCode = OVO
					// ShopeePay --- channelCode = SHPPAY
			// 'COUNTER', 'OTCTR'
					// Indomaret --- channelCode = 123, agentCode = INDOMARET, agentChannelCode = OVERTHECOUNTER
			// 'SSM', 'ATM'
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
	$content = (object) json_decode($payment_option_detail['response']['content']);
echo '3. payment_option_detail:';
// print_r($payment_option_detail);
print_r($content);

/*------------------------------ A A A Optional ---------- */

	// Do payment
	$do_payment = $vendor->DoPayment(
			$transaction,
			$payment_token
		);
	$content = (object) json_decode($do_payment['response']['content']);
echo '4. do_payment:';
print_r($content);

	// Transaction status
	$transaction_status = $vendor->TransactionStatus(
			$payment_token
		);
	$content = (object) json_decode($transaction_status['response']['content']);
echo '5. transaction_status:';
print_r($content);

	// Inquiry payment
	$payment_inquiry = $vendor->PaymentInquiry(
			$payment_token,
			$invoice_no
		);
	$content = (object) json_decode($payment_inquiry['response']['content']);
echo '6. payment_inquiry:';
print_r($content);

// } catch (\Throwable $e) {
// 	echo 'Secure payment failed: ' . $e->getMessage() . ':' . $e->getCode();
// }