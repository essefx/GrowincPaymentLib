<?php

namespace Growinc\Payment\Vendors;

use Firebase\JWT\JWT;
use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class TCTP extends Requestor implements VendorInterface
{

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		// Inapplicable
	}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Inapplicable
		/*

		try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			// Special for 2C2P amount must be padded with zeros
			$this->form['amount'] = str_pad($this->transaction->getAmount(), 12, 0, STR_PAD_LEFT);
			$this->form['description'] = $this->transaction->getDescription();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['country_code'] = $this->transaction->getCountryCode();
			//
			$this->form['payment_url'] = $this->init->getPaymentURL(); // 'https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment';
			//
			// API version number. Current latest version is 8.5
			$this->form['version'] = '8.5';
			$this->form['merchant_id'] = $this->init->getMID();
			// For merchant to submit merchant's specific data
			$this->form['user_defined_1'] = $this->transaction->param['user_defined_1'] ?? '';
			$this->form['user_defined_2'] = $this->transaction->param['user_defined_2'] ?? '';
			$this->form['user_defined_3'] = $this->transaction->param['user_defined_3'] ?? '';
			$this->form['user_defined_4'] = $this->transaction->param['user_defined_4'] ?? '';
			$this->form['user_defined_5'] = $this->transaction->param['user_defined_5'] ?? '';
			// Promotion Code for the payment. Example: PromoMC for MasterCard payment only, PromoVC for Visa card payment only.
			$this->form['promotion'] = $this->transaction->param['promotion'] ?? '';
			// Merchant’s predefined payment category code for reporting purpose.
			$this->form['pay_category_id'] = $this->transaction->param['pay_category_id'] ?? '';
			// Frontend return url for 2C2P PGW to redirect customer back to merchant after completing the payment. Use "https" to ensure secure communication returned back to merchant.
			// $this->form['result_url_1'] = $request->return_url_frontend ?? url('oanwef4851ashrb/pg/tp/redapi_form');
			$this->form['result_url_1'] = $this->init->getReturnURL();
			// Backend return url for 2C2P PGW to notify payment result to merchant after payment completed. This URL will also be used to notify merchant when offline payment (such as CASH payments) is completed. Use "https" to ensure secure communication returned back to merchant.
			// $this->form['result_url_2'] = $request->return_url_backend ?? url('oanwef4851ashrb/pg/tp/redapi_result');
			$this->form['result_url_2'] = $this->init->getCallbackURL();
			// 123 - Alternative Payment Method
			// ALIPAY - Alipay
			// ALL - All available options
			// BANK - 123 BANK
			// BILDK - Billdesk
			// BOOST - Boost Wallet
			// CC - Credit Card Payment
			// EMVQR - EMV QR (Merchant QR)
			// FULL - Full amount (No IPP Payment) payment only
			// GCASH - GCash
			// GRAB - GrabPay Wallet
			// IPP - IPP (Installment Payment Plan) payment only
			// KCP - Korean Cyper Payments
			// KIOSK - KIOSK
			// LINE - LinePay
			// LOAN - Loan Card
			// MOMO - MOMO Wallet
			// MPASS - Masterpass
			// MPU - Myanmar Payment Union
			// OCTPAY - Octopus
			// OTC - 123 OVER THE COUNTER
			// PAYMAYA - PayMaya
			// PAYPAL - Paypal
			// SSPAY - Samsung Pay
			// TNG - Touch 'n Go Wallet
			// TRUEMONEY - TRUEMONEY
			// UPOP - China UnionPay
			// WAVE - WavePay
			// WCQR - Wechat Pay (Merchant QR)
			$this->form['payment_option'] = $this->transaction->getPaymentMethod();
			// To specify IPP Interest Type.
			// A – All available options
			// C – Customer Pay Interest Option ONLY
			// M – Merchant Pay Interest Option ONLY
			// By default, all available options will be shown
			$this->form['ipp_interest_type'] = $this->transaction->param['ipp_interest_type'] ?? '';
			$this->form['payment_expiry'] = $this->transaction->param['payment_expiry'] ?? '';
			$this->form['default_lang'] = $this->transaction->param['default_lang'] ?? '';
			// To enable tokenization feature
			// N - Disable tokenization option (default)
			// Y - Enable tokenization option
			$this->form['enable_store_card'] = 'N';
			// To make payment with tokenized card
			$this->form['stored_card_unique_id'] = $this->transaction->param['stored_card_unique_id'] ?? '';
			// To enable / disable / Force 3DS authentication
			// Y - Do 3DS authentication (default)
			// N - No 3DS authentication
			// F - Force 3DS authentication (only ECI 02/05 are accepted)
			$this->form['request_3ds'] = $this->transaction->param['request_3ds'] ?? '';
			// To enable RPP (Recurring Payment Plan) transaction feature.
			// recurring unique ID will be returned on response message if this option is enabled.
			// N - Disable RPP feature (default)
			// Y - Enable RPP feature
			$this->form['recurring'] = $this->transaction->param['recurring'] ?? '';
			// RPP transaction will add 5 additional digit behind order_prefix as invoice number.
			// Only required if RPP is enabled.
			$this->form['order_prefix'] = $this->transaction->param['order_prefix'] ?? '';
			$this->form['recurring_amount'] = $this->transaction->param['recurring_amount'] ?? '';
			$this->form['allow_accumulate'] = $this->transaction->param['allow_accumulate'] ?? '';
			$this->form['max_accumulate_amount'] = $this->transaction->param['max_accumulate_amount'] ?? '';
			$this->form['recurring_interval'] = $this->transaction->param['recurring_interval'] ?? '';
			$this->form['recurring_count'] = $this->transaction->param['recurring_count'] ?? '';
			$this->form['charge_next_date'] = $this->transaction->param['charge_next_date'] ?? '';
			$this->form['charge_on_date'] = $this->transaction->param['charge_on_date'] ?? '';
			// To set dynamic statement descriptor.
			// only alphanumeric in latin character is allowed.
			$this->form['statement_descriptor'] = $this->transaction->param['statement_descriptor'] ?? '';
			// To force payment to be made only with the tokenized card. customer will not be allowed to change to other card when making payment.
			// N - Card holder may change card (default).
			// Y - Force card holder to use the passed token.
			$this->form['use_storedcard_only'] = $this->transaction->param['use_storedcard_only'] ?? '';
			// To tokenize customer's credit card without charging anything to the card.
			// N - request will do Authorization to the card (default).
			// Y - request will not do Authorization to the card.
			$this->form['tokenize_without_authorization'] = $this->transaction->param['tokenize_without_authorization'] ?? '';
			$this->form['product_code'] = $this->transaction->param['product_code'] ?? '';
			// IPP product code
			$this->form['ipp_period_filter'] = $this->transaction->param['ipp_period_filter'] ?? '';
			$this->form['hash_value'] =
				$this->form['version'] .
				$this->form['merchant_id'] .
				$this->form['description'] .
				$this->form['order_id'] .
				$this->form['invoice_no'] .
				$this->form['currency'] .
				$this->form['amount'] .
				$this->form['customer_email'] .
				$this->form['pay_category_id'] .
				$this->form['promotion'] .
				$this->form['user_defined_1'] .
				$this->form['user_defined_2'] .
				$this->form['user_defined_3'] .
				$this->form['user_defined_4'] .
				$this->form['user_defined_5'] .
				$this->form['result_url_1'] .
				$this->form['result_url_2'] .
				$this->form['enable_store_card'] .
				$this->form['stored_card_unique_id'] .
				$this->form['request_3ds'] .
				$this->form['recurring'] .
				$this->form['order_prefix'] .
				$this->form['recurring_amount'] .
				$this->form['allow_accumulate'] .
				$this->form['max_accumulate_amount'] .
				$this->form['recurring_interval'] .
				$this->form['recurring_count'] .
				$this->form['charge_next_date'] .
				$this->form['charge_on_date'] .
				$this->form['payment_option'] .
				$this->form['ipp_interest_type'] .
				$this->form['payment_expiry'] .
				$this->form['default_lang'] .
				$this->form['statement_descriptor'];
				// $this->form['use_storedcard_only'] .
				// $this->form['tokenize_without_authorization'] .
				// $this->form['product_code'] .
				// $this->form['ipp_period_filter;
			$this->form['hash_value'] = hash_hmac('sha256', $this->form['hash_value'], $this->init->getSecret(), false);	//Compute hash value
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];
			$this->request['data'] = [
					'version' => $this->form['version'],
					'merchant_id' => $this->init->getMID(),
					'payment_description' => $this->form['description'],
					'order_id' => $this->form['order_id'],
					'invoice_no' => $this->form['invoice_no'],
					'currency' => $this->form['currency'],
					'amount' => $this->form['amount'],
					'customer_email' => $this->form['customer_email'],
					'result_url_1' => $this->form['result_url_1'],
					'result_url_2' => $this->form['result_url_2'],
					'pay_category_id' => $this->form['pay_category_id'],
					'promotion' => $this->form['promotion'],
					'user_defined_1' => $this->form['user_defined_1'],
					'user_defined_2' => $this->form['user_defined_2'],
					'user_defined_3' => $this->form['user_defined_3'],
					'user_defined_4' => $this->form['user_defined_4'],
					'user_defined_5' => $this->form['user_defined_5'],
					'enable_store_card' => $this->form['enable_store_card'],
					'stored_card_unique_id' => $this->form['stored_card_unique_id'],
					'request_3ds' => $this->form['request_3ds'],
					'recurring' => $this->form['recurring'],
					'order_prefix' => $this->form['order_prefix'],
					'recurring_amount' => $this->form['recurring_amount'],
					'allow_accumulate' => $this->form['allow_accumulate'],
					'max_accumulate_amount' => $this->form['max_accumulate_amount'],
					'recurring_interval' => $this->form['recurring_interval'],
					'recurring_count' => $this->form['recurring_count'],
					'charge_next_date' => $this->form['charge_next_date'],
					'charge_on_date' => $this->form['charge_on_date'],
					'payment_option' => $this->form['payment_option'],
					'ipp_interest_type' => $this->form['ipp_interest_type'],
					'payment_expiry' => $this->form['payment_expiry'],
					'default_lang' => $this->form['default_lang'],
					'statement_descriptor' => $this->form['statement_descriptor'],
					'use_storedcard_only' => $this->form['use_storedcard_only'],
					'tokenize_without_authorization' => $this->form['tokenize_without_authorization'],
					'product_code' => $this->form['product_code'],
					'ipp_period_filter' => $this->form['ipp_period_filter'],
					'hash_value' => $this->form['hash_value'],
				];
			$this->request['headers'] = [
					'Content-Type' => 'multipart/form-data',
				];
			$this->request['option'] = [
					// 'to_uri' => true,
				];
			$post = $this->DoRequest('POST', $this->request);
			// print_r($post);
			// exit();
			$response = (array) $post['response'];
			extract($response);
			// echo($content);
			// exit();
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (	!empty($content->statusMessage)
						&& $content->statusMessage == "SUCCESS"
				) {
					$content = [
							'status' => '000',
							'data' => (array) $content,
						];
					$result = [
							'request' => (array) $this->request,
							'response' => [
									'content' => json_encode($content),
									'status_code' => 200,
								],
						];
				}
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
		*/
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			//
			$this->form['merchantID'] = $this->init->getMID();
			$this->form['orderID'] = $this->transaction->getOrderID();
			$this->form['invoiceNo'] = $this->transaction->getInvoiceNo();
			$this->form['description'] = $this->transaction->getDescription();
			// Special for 2C2P amount must be padded with zeros
			$this->form['amount'] = str_pad($this->transaction->getAmount(), 12, 0, STR_PAD_LEFT);
			$this->form['currency'] = $this->transaction->getCurrency();
			$this->form['currencyCode'] = $this->transaction->getCurrencyCode();
			// 123 - Alternative Payment Method
			// ALIPAY - Alipay
			// ALL - All available options
			// BANK - 123 BANK
			// BILDK - Billdesk
			// BOOST - Boost Wallet
			// CC - Credit Card Payment
			// EMVQR - EMV QR (Merchant QR)
			// FULL - Full amount (No IPP Payment) payment only
			// GCASH - GCash
			// GRAB - GrabPay Wallet
			// IPP - IPP (Installment Payment Plan) payment only
			// KCP - Korean Cyper Payments
			// KIOSK - KIOSK
			// LINE - LinePay
			// LOAN - Loan Card
			// MOMO - MOMO Wallet
			// MPASS - Masterpass
			// MPU - Myanmar Payment Union
			// OCTPAY - Octopus
			// OTC - 123 OVER THE COUNTER
			// PAYMAYA - PayMaya
			// PAYPAL - Paypal
			// SSPAY - Samsung Pay
			// TNG - Touch 'n Go Wallet
			// TRUEMONEY - TRUEMONEY
			// UPOP - China UnionPay
			// WAVE - WavePay
			// WCQR - Wechat Pay (Merchant QR)
			// To specify IPP Interest Type.
			// A – All available options
			// C – Customer Pay Interest Option ONLY
			// M – Merchant Pay Interest Option ONLY
			// By default, all available options will be shown
			$this->form['paymentChannel'] = $this->transaction->getPaymentMethod();
			// To enable / disable / Force 3DS authentication
			// Y - Do 3DS authentication (default)
			// N - No 3DS authentication
			// F - Force 3DS authentication (only ECI 02/05 are accepted)
			$this->form['request3DS'] = $this->transaction->param['request3DS'] ?? '';
			// To enable tokenization feature
			// N - Disable tokenization option (default)
			// Y - Enable tokenization option
			$this->form['tokenize'] = 'N';
			$this->form['cardTokens'] = $this->transaction->param['cardTokens'] ?? '';
			// Specify whether allow card token only. This is only applicable for card payment page.
			// If merchant set to card token only, new card option is not allow in the payment page.
			$this->form['cardTokenOnly'] = $this->transaction->param['cardTokenOnly'] ?? '';
			// To tokenize customer's credit card without charging anything to the card.
			// N - request will do Authorization to the card (default).
			// Y - request will not do Authorization to the card.
			$this->form['tokenizeOnly'] = $this->transaction->param['tokenizeOnly'] ?? '';
			// Installment interest type
			// A – All available options (Default)
			// C – Customer Pay Interest Option ONLY
			// M – Merchant Pay Interest Option ONLY
			$this->form['interestType'] = $this->transaction->param['interestType'] ?? '';
			// IPP product code
			$this->form['installmentPeriodFilter'] = $this->transaction->param['installmentPeriodFilter'] ?? '';
			$this->form['productCode'] = $this->transaction->param['productCode'] ?? '';
			// To enable RPP (Recurring Payment Plan) transaction feature.
			// recurring unique ID will be returned on response message if this option is enabled.
			// N - Disable RPP feature (default)
			// Y - Enable RPP feature
			$this->form['recurring'] = $this->transaction->param['recurring'] ?? '';
			// RPP transaction will add 5 additional digit behind order_prefix as invoice number.
			// Only required if RPP is enabled.
			$this->form['invoicePrefix'] = $this->transaction->param['invoicePrefix'] ?? '';
			$this->form['recurringAmount'] = $this->transaction->param['recurringAmount'] ?? '';
			$this->form['allowAccumulate'] = $this->transaction->param['allowAccumulate'] ?? '';
			$this->form['maxAccumulateAmount'] = $this->transaction->param['maxAccumulateAmount'] ?? '';
			$this->form['recurringInterval'] = $this->transaction->param['recurringInterval'] ?? '';
			$this->form['recurringCount'] = $this->transaction->param['recurringCount'] ?? '';
			$this->form['chargeNextDate'] = $this->transaction->param['chargeNextDate'] ?? '';
			$this->form['chargeOnDate'] = $this->transaction->param['chargeOnDate'] ?? '';
			$this->form['paymentExpiry'] = $this->transaction->param['paymentExpiry'] ?? '';
			// Promotion Code for the payment. Example: PromoMC for MasterCard payment only, PromoVC for Visa card payment only.
			$this->form['promotionCode'] = $this->transaction->param['promotionCode'] ?? '';
			// Payment routing rules based on custom configuration
			$this->form['paymentRouteID'] = $this->transaction->param['paymentRouteID'] ?? '';
			// Forex provide code used for enable multiple payment currency
			$this->form['fxProviderCode'] = $this->transaction->param['fxProviderCode'] ?? '';
			// To trigger payment immediately
			$this->form['immediatePayment'] = $this->transaction->param['immediatePayment'] ?? '';
			// For merchant to submit merchant's specific data
			$this->form['userDefined1'] = $this->transaction->param['userDefined1'] ?? '';
			$this->form['userDefined2'] = $this->transaction->param['userDefined2'] ?? '';
			$this->form['userDefined3'] = $this->transaction->param['userDefined3'] ?? '';
			$this->form['userDefined4'] = $this->transaction->param['userDefined4'] ?? '';
			$this->form['userDefined5'] = $this->transaction->param['userDefined5'] ?? '';
			// To set dynamic statement descriptor.
			// only alphanumeric in latin character is allowed.
			$this->form['statementDescriptor'] = $this->transaction->param['statementDescriptor'] ?? '';
			// Sub merchant list
			// - merchantID
			// - invoiceNo
			// - amount
			// - description
			$this->form['subMerchants'] = [
					'merchantID' => $this->transaction->param['subMerchants.merchantID'] ?? '',
					'invoiceNo' => $this->transaction->param['subMerchants.invoiceNo'] ?? '',
					'amount' => $this->transaction->param['subMerchants.amount'] ?? '',
					'description' => $this->transaction->param['subMerchants.description'] ?? '',
				];
			$this->form['locale'] = $this->transaction->param['locale'] ?? '';
			// Frontend return url for 2C2P PGW to redirect customer back to merchant after completing the payment. Use "https" to ensure secure communication returned back to merchant.
			// $this->form['result_url_1'] = $request->return_url_frontend ?? url('oanwef4851ashrb/pg/tp/redapi_form');
			$this->form['frontendReturnUrl'] = $this->init->getReturnURL();
			// Backend return url for 2C2P PGW to notify payment result to merchant after payment completed. This URL will also be used to notify merchant when offline payment (such as CASH payments) is completed. Use "https" to ensure secure communication returned back to merchant.
			// $this->form['result_url_2'] = $request->return_url_backend ?? url('oanwef4851ashrb/pg/tp/redapi_result');
			$this->form['backendReturnUrl'] = $this->init->getCallbackURL();
			$this->form['nonceStr'] = $this->transaction->param['nonceStr'] ?? '';
			// uiParams
			// - userInfo
			// - - name
			// - - email
			// - - mobileNo
			// - - countryCode
			// - - mobileNoPrefix
			// - - currencyCode
			$this->form['uiParams'] = [
					'userInfo' => [
							'name' => $this->transaction->getCustomerName(),
							'email' => $this->transaction->getCustomerEmail(),
							'mobileNo' => $this->transaction->getCustomerPhone(),
							'countryCode' => $this->transaction->getCountryCode(),
							'mobileNoPrefix' => '',
							'currencyCode' => $this->transaction->getCurrencyCode(),
							// 'address' => $this->transaction->getCustomerAddress(), // Not applicable
						],
				];
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = 'https://sandbox-pgw.2c2p.com/payment/4.1/paymentToken';
			$this->request['headers'] = [
					'Accept' => 'text/plain',
					'Content-Type' => 'application/*+json',
					// '-debug-merchantID' => $this->init->getMID(),
					// '-debug-secret' => $this->init->getSecret(),
				];
			$this->request['option'] = [
					// 'to_uri' => true,
					'request_opt' => 'json'
				];
			$this->request['data_raw'] = [
					'merchantID' => $this->form['merchantID'],
					// 'orderID' => $this->form['orderID'], // Not applicable
					'invoiceNo' => $this->form['invoiceNo'],
					'description' => $this->form['description'],
					'amount' => $this->form['amount'],
					'currencyCode' => $this->form['currencyCode'],
					'paymentChannel' => $this->form['paymentChannel'],
					'request3DS' => $this->form['request3DS'],
					'tokenize' => $this->form['tokenize'],
					'cardTokens' => $this->form['cardTokens'],
					'cardTokenOnly' => $this->form['cardTokenOnly'],
					'tokenizeOnly' => $this->form['tokenizeOnly'],
					'interestType' => $this->form['interestType'],
					'installmentPeriodFilter' => $this->form['installmentPeriodFilter'],
					'productCode' => $this->form['productCode'],
					'recurring' => $this->form['recurring'],
					'invoicePrefix' => $this->form['invoicePrefix'],
					'recurringAmount' => $this->form['recurringAmount'],
					'allowAccumulate' => $this->form['allowAccumulate'],
					'maxAccumulateAmount' => $this->form['maxAccumulateAmount'],
					'recurringInterval' => $this->form['recurringInterval'],
					'recurringCount' => $this->form['recurringCount'],
					'chargeNextDate' => $this->form['chargeNextDate'],
					'chargeOnDate' => $this->form['chargeOnDate'],
					'paymentExpiry' => $this->form['paymentExpiry'],
					'promotionCode' => $this->form['promotionCode'],
					'paymentRouteID' => $this->form['paymentRouteID'],
					'fxProviderCode' => $this->form['fxProviderCode'],
					'immediatePayment' => $this->form['immediatePayment'],
					'userDefined1' => $this->form['userDefined1'],
					'userDefined2' => $this->form['userDefined2'],
					'userDefined3' => $this->form['userDefined3'],
					'userDefined4' => $this->form['userDefined4'],
					'userDefined5' => $this->form['userDefined5'],
					'statementDescriptor' => $this->form['statementDescriptor'],
					'subMerchants' => $this->form['subMerchants'],
					'locale' => $this->form['locale'],
					'frontendReturnUrl' => $this->form['frontendReturnUrl'],
					'backendReturnUrl' => $this->form['backendReturnUrl'],
					'nonceStr' => $this->form['nonceStr'],
					'uiParams' => $this->form['uiParams'],
				];
			$data = [
					'payload' => JWT::encode($this->request['data_raw'], $this->init->getSecret(), 'HS256'),
				];
			$this->request['data'] = $data;
			// print_r($this->request);
			// print_r($data);
			// print_r(json_encode($data));
			// $decode_data = JWT::decode($data, $this->init->getSecret(), array('HS256'));
			// print_r($decode_data);
			$post = $this->DoRequest('POST', $this->request);
			print_r($post);



// $url = 'https://sandbox-pgw.2c2p.com/payment/4.1/paymentToken';
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->request['data']));
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 	'Accept: text/plain',
// 	'Content-Type: application/*+json',
// ));
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

// $request_result = curl_exec($ch);
// $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// $result ="";
// if ($httpCode == 200) {
// }
// print_r($request_result);
// exit();



// $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjaGFudElEIjoiSlQwMSIsImludm9pY2VObyI6IjE1MjM5NTM2NjEiLCJkZXNjcmlwdGlvbiI6Iml0ZW0gMSIsImFtb3VudCI6MTAwMCwiY3VycmVuY3lDb2RlIjoiU0dEIn0.mfQOdnH7tJUzPHEm1iF1ZYX2oZV29VbT7d9KdbxK5zM';
// print_r(JWT::decode($jwt, 'merchant_secret_key', array('HS256')));

			exit();


			$post = $this->DoRequest('POST', $this->request);


			print_r($post);
			exit();
			$response = (array) $post['response'];
			extract($response);
			// echo($content);
			// exit();
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (	!empty($content->statusMessage)
						&& $content->statusMessage == "SUCCESS"
				) {
					$content = [
							'status' => '000',
							'data' => (array) $content,
						];
					$result = [
							'request' => (array) $this->request,
							'response' => [
									'content' => json_encode($content),
									'status_code' => 200,
								],
						];
				}
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function Callback(object $request)
	{
		// Inapplicable
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		// Inapplicable
	}

	public function Cancel(object $request)
	{
		// Inapplicable
	}

	public function Settle(object $request)
	{
		// Inapplicable
	}

	public function Refund(object $request)
	{
		// Inapplicable
	}

	public function RefundStatus(object $request)
	{
		// Inapplicable
	}

}