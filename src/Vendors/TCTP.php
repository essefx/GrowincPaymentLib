<?php

namespace Growinc\Payment\Vendors;

use Firebase\JWT\JWT;
use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class TCTP extends Requestor implements VendorInterface
{

	protected $form;

	// Inapplicable
	public function Index(){}
	public function GetToken($args){}
	public function CreateDummyForm($args){}
	public function RedirectPayment(\Growinc\Payment\Transaction $transaction){}
	public function SecurePayment(\Growinc\Payment\Transaction $transaction){}

	/*==========================================================================================
												Start of Function
	==========================================================================================**/

		public function CreatePaymentToken(
			\Growinc\Payment\Transaction $transaction
		) {
			try {
				$this->transaction = $transaction;
				//
				$this->form['merchantID'] = $this->init->getMID();
				// $this->form['orderID'] = $this->transaction->getOrderID(); // Not applicable
				$this->form['invoiceNo'] = $this->transaction->getInvoiceNo();
				$this->form['description'] = $this->transaction->getDescription();
				// Special for 2C2P amount must be padded with zeros
				$this->form['amount'] = str_pad($this->transaction->getAmount(), 12, 0, STR_PAD_LEFT);
				$this->form['currency'] = $this->transaction->getCurrency(); // IDR
				$this->form['currencyCode'] = $this->transaction->getCurrencyCode(); // 360
				// https://developer.2c2p.com/v4.0.0/docs/reference-payment-channel-matrix
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
				$this->form['request3DS'] = $this->transaction->param['request3DS'] ?? 'Y';
				// To enable tokenization feature
				// N - Disable tokenization option (default)
				// Y - Enable tokenization option
				$this->form['tokenize'] = $this->transaction->param['tokenize'] ?? 'false';
				$this->form['cardTokens'] = $this->transaction->param['cardTokens'] ?? '';
				// Specify whether allow card token only. This is only applicable for card payment page.
				// If merchant set to card token only, new card option is not allow in the payment page.
				$this->form['cardTokenOnly'] = $this->transaction->param['cardTokenOnly'] ?? 'false';
				// To tokenize customer's credit card without charging anything to the card.
				// N - request will do Authorization to the card (default).
				// Y - request will not do Authorization to the card.
				$this->form['tokenizeOnly'] = $this->transaction->param['tokenizeOnly'] ?? 'false';
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
				$this->form['paymentExpiry'] = $this->transaction->param['paymentExpiry'] ?? date('Y-m-d H:i:s', $this->transaction->getTime()+(24*3600*2)); // Default is 2 days
				// Promotion Code for the payment. Example: PromoMC for MasterCard payment only, PromoVC for Visa card payment only.
				$this->form['promotionCode'] = $this->transaction->param['promotionCode'] ?? '';
				// Payment routing rules based on custom configuration
				$this->form['paymentRouteID'] = $this->transaction->param['paymentRouteID'] ?? '';
				// Forex provide code used for enable multiple payment currency
				$this->form['fxProviderCode'] = $this->transaction->param['fxProviderCode'] ?? '';
				// To trigger payment immediately
				$this->form['immediatePayment'] = $this->transaction->param['immediatePayment'] ?? 'false';
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
							'as_json' => true,
					];
				$this->request['data_raw'] = [
						'merchantID' => $this->form['merchantID'],
						// 'orderID' => $this->form['orderID'], // Not applicable
						'invoiceNo' => $this->form['invoiceNo'],
						'description' => $this->form['description'],
						'amount' => $this->form['amount'],
						// 'currencyCode' => $this->form['currencyCode'],
						'currencyCode' => $this->form['currency'],
						'paymentChannel' => [
								// 'ALL'
								explode(',', $this->form['paymentChannel'])[0]
							],
						'request3DS' => $this->form['request3DS'],
						'tokenize' => $this->form['tokenize'],
						'cardTokens' => $this->form['cardTokens'],
						'cardTokenOnly' => $this->form['cardTokenOnly'],
						'tokenizeOnly' => $this->form['tokenizeOnly'],
						// 'interestType' => $this->form['interestType'],
						// 'installmentPeriodFilter' => $this->form['installmentPeriodFilter'],
						// 'productCode' => $this->form['productCode'],
						// 'recurring' => $this->form['recurring'],
						// 'invoicePrefix' => $this->form['invoicePrefix'],
						// 'recurringAmount' => $this->form['recurringAmount'],
						// 'allowAccumulate' => $this->form['allowAccumulate'],
						// 'maxAccumulateAmount' => $this->form['maxAccumulateAmount'],
						// 'recurringInterval' => $this->form['recurringInterval'],
						// 'recurringCount' => $this->form['recurringCount'],
						// 'chargeNextDate' => $this->form['chargeNextDate'],
						// 'chargeOnDate' => $this->form['chargeOnDate'],
						'paymentExpiry' => $this->form['paymentExpiry'],
						// 'promotionCode' => $this->form['promotionCode'],
						// 'paymentRouteID' => $this->form['paymentRouteID'],
						// 'fxProviderCode' => $this->form['fxProviderCode'],
						'immediatePayment' => $this->form['immediatePayment'],
						// 'userDefined1' => $this->form['userDefined1'],
						// 'userDefined2' => $this->form['userDefined2'],
						// 'userDefined3' => $this->form['userDefined3'],
						// 'userDefined4' => $this->form['userDefined4'],
						// 'userDefined5' => $this->form['userDefined5'],
						// 'statementDescriptor' => $this->form['statementDescriptor'],
						// 'subMerchants' => $this->form['subMerchants'],
						// 'locale' => $this->form['locale'],
						'frontendReturnUrl' => $this->form['frontendReturnUrl'],
						'backendReturnUrl' => $this->form['backendReturnUrl'],
						// 'nonceStr' => $this->form['nonceStr'],
						// 'uiParams' => $this->form['uiParams'],
					];
				$data = [
						'payload' => JWT::encode($this->request['data_raw'], $this->init->getSecret(), 'HS256'),
					];
				$this->request['data'] = $data;
				// Go
				$post = $this->DoRequest('POST', $this->request);
				$response = (array) $post['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					// Parse payment token
					$content = (object) json_decode($content);
					if (
						isset($content->payload) && !empty($content->payload)
					) {
						$content = JWT::decode($content->payload, $this->init->getSecret(), array('HS256'));
						if (
							!empty($content->respCode)
							&& $content->respCode == '0000'
						) {
							// Success
							/*
							 {
								"status": "000",
								"data": {
									"webPaymentUrl": "https:\/\/sandbox-pgw-ui.2c2p.com\/payment\/4.1\/#\/token\/kSAops9Zwhos8hSTSeLTUZEpgf%2f0iZKRIGg9MPnMfVU%2b9paJbUWAj1re%2boWEMlZnuJOeh1t2eL%2fJ20AB3E%2bkdDmxf7vS9cne%2fcj%2bdHimzcQ8xJ70bhbv0d8QXHfA0892",
									"paymentToken": "kSAops9Zwhos8hSTSeLTUZEpgf\/0iZKRIGg9MPnMfVU+9paJbUWAj1re+oWEMlZnuJOeh1t2eL\/J20AB3E+kdDmxf7vS9cne\/cj+dHimzcQ8xJ70bhbv0d8QXHfA0892",
									"respCode": "0000",
									"respDesc": "Success"
								}
							}
							*/
							$res = [
									'status' => '000',
									'data' => (array) $content,
								];
						} else {
							$res = [
									'status' => $content->respCode ?? '999',
									'data' => (array) $content,
								];
						}
						$result = [
								'request' => (array) $this->request,
								'response' => [
										'content' => json_encode($res),
										'status_code' => 200,
									],
							];
					} else {
						throw new \Exception(implode('. ', ["Payment token is empty", ($content->respDesc ?? '')]), 1);
					}
				}
			} catch (\Throwable $e) {
				throw new \Exception($this->ThrowError($e));
			}
			return $result ?? [];
		}



		public function GetPaymentOption(
			\Growinc\Payment\Transaction $transaction,
			$payment_token
		) {
			try {
				$this->transaction = $transaction;
				// Get Payment Options from vendor
				$this->request['form'] = [];
				$this->request['time'] = $this->transaction->getTime();
				$this->request['url'] = 'https://sandbox-pgw.2c2p.com/payment/4.1/paymentOption';
				$this->request['headers'] = [
						'Accept' => 'text/plain',
						'Content-Type' => 'application/*+json',
						// '-debug-merchantID' => $this->init->getMID(),
						// '-debug-secret' => $this->init->getSecret(),
					];
				$this->request['option'] = [
						'as_json' => true,
					];
				$this->request['data_raw'] = [];
				$this->request['data'] = [
						'paymentToken' => $payment_token,
						'locale' => 'en',
					];
				// Go
				$post = $this->DoRequest('POST', $this->request);
				$response = (array) $post['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					// Parse data
					$content = (object) json_decode($content);
					if (
						isset($content) && !empty($content)
					) {
						if (
							!empty($content->respCode)
							&& $content->respCode == '0000'
						) {
							$res = [
									'status' => '000',
									'data' => (array) $content,
								];
						} else {
							$res = [
									'status' => $content->respCode ?? '999',
									'data' => (array) $content,
								];
						}
						$result = [
								'request' => (array) $this->request,
								'response' => [
										'content' => json_encode($res),
										'status_code' => 200,
									],
							];
					} else {
						throw new \Exception("Payment option is empty", 1);
					}
				}
			} catch (\Throwable $e) {
				throw new \Exception($this->ThrowError($e));
			}
			return $result ?? [];
		}



		public function GetPaymentOptionDetail(
			\Growinc\Payment\Transaction $transaction,
			$payment_token,
			$category_code = '',
			$group_code = ''
		) {
			try {
				$this->transaction = $transaction;
				// Get Payment Option Details from vendor
				// https://developer.2c2p.com/v4.0.0/docs/reference-payment-channel-matrix
				// channelCategories
				// 	GCARD | Global Card Payment
				// 		groups:
				// 			CC | Credit Card
				// 			IPP | Installment Plan Payment
				// 			GTPTY | Global 3 Party Payment
				// 	LCARD | Local Card Payment
				// 		groups:
				// 			PCC | Proprietary / Loan Card
				// 			LTPTY | Local 3 Party Payment
				// 	WEBPAY | Web pay / Direct Debit
				// 		groups:
				// 			WEBPAY | Web pay / Direct Debit
				// 	IMBANK | Internet / Mobile Banking
				// 		groups:
				// 			IMBANK | Internet / Mobile Banking
				// 	COUNTER | Pay at Counter
				// 		groups:
				// 			BCTR | Bank Counter
				// 			OTCTR | Over The Counter
				// 	SSM | Self Service Machines
				// 		groups:
				// 			ATM | Automatic Teller Machine
				// 			KIOSK | Kiosk Machine
				// 	DPAY | Digital Payment
				// 		groups:
				// 			EWALLET | E-Wallet Payment
				// 			MPASS | Master Pass
				// 			SSPAY | Samsung Pay
				// 	QR | Scan QR Payment
				// 		groups:
				// 			QRC | QR Code Payment
				// 			CSQR | Card Scheme QR Payment
				// 			THQR | Thai QR Payment
				// 			SGQR | Singapore QR Payment
				// 	LCARDIPP | Local Card IPP
				// 		groups:
				// 			LIPP | Installment Plan Payment Loan Card
				$this->request['form'] = [];
				$this->request['time'] = $this->transaction->getTime();
				$this->request['url'] = 'https://sandbox-pgw.2c2p.com/payment/4.1/paymentOptionDetails';
				$this->request['headers'] = [
						'Accept' => 'text/plain',
						'Content-Type' => 'application/*+json',
						// '-debug-merchantID' => $this->init->getMID(),
						// '-debug-secret' => $this->init->getSecret(),
					];
				$this->request['option'] = [
						'as_json' => true,
					];
				$this->request['data_raw'] = [];
				$this->request['data'] = [
						'paymentToken' => $payment_token,
						'locale' => 'en',
						'categoryCode' => $category_code,
						'groupCode' => $group_code,
					];
				// Go
				$post = $this->DoRequest('POST', $this->request);
				$response = (array) $post['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					// Parse data
					$content = (object) json_decode($content);
					if (
						isset($content) && !empty($content)
					) {
						if (
							!empty($content->respCode)
							&& $content->respCode == '0000'
						) {
							$res = [
									'status' => '000',
									'data' => (array) $content,
								];
						} else {
							$res = [
									'status' => $content->respCode ?? '999',
									'data' => (array) $content,
								];
						}
						$result = [
								'request' => (array) $this->request,
								'response' => [
										'content' => json_encode($res),
										'status_code' => 200,
									],
							];
					} else {
						throw new \Exception("Payment option detail is empty", 1);
					}
				}
			} catch (\Throwable $e) {
				throw new \Exception($this->ThrowError($e));
			}
			return $result ?? [];
		}



		public function DoPayment(
			\Growinc\Payment\Transaction $transaction,
			$payment_token
		) {
			try {
				$this->transaction = $transaction;
				// Do Payment
				$payment_channel = $this->transaction->getPaymentMethod();
				$payment_method = explode(',', $payment_channel);
				$channel_code = $payment_method[1] ?? null;
				$agent_code = $payment_method[2] ?? null;
				$agent_channel_code = $payment_method[3] ?? null;
				$this->request['form'] = [];
				$this->request['time'] = $this->transaction->getTime();
				$this->request['url'] = 'https://sandbox-pgw.2c2p.com/payment/4.1/payment';
				$this->request['headers'] = [
						'Accept' => 'text/plain',
						'Content-Type' => 'application/*+json',
						// '-debug-merchantID' => $this->init->getMID(),
						// '-debug-secret' => $this->init->getSecret(),
					];
				$this->request['option'] = [
						'as_json' => true,
					];
				$this->request['data_raw'] = [];
				$this->request['data'] = [
						'paymentToken' => $payment_token,
						'locale' => 'en',
						'responseReturnUrl' => $this->init->getReturnURL(),
						'payment' => [
								'code' => [
										'channelCode' => $channel_code,
										'agentCode' => $agent_code,
										'agentChannelCode' => $agent_channel_code,
									],
								'data' => [
										'name' => $this->transaction->getCustomerName(),
										'email' => $this->transaction->getCustomerEmail(),
										'mobileNo' => $this->transaction->getCustomerPhone(),
										'qrType' => 'URL',
										'accountNo' => $this->transaction->getCustomerPhone(),
										'securePayToken' => $this->transaction->getCardToken(),
									],
							],
					];
				// Go
				$post = $this->DoRequest('POST', $this->request);
				$response = (array) $post['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					// Parse data
					$content = (object) json_decode($content);
					if (
						isset($content->data) && !empty($content->data)
					) {
						// Success
						/*
						{
							"status": "000",
							"data": {
								"data": "https:\/\/demo2.2c2p.com\/2C2PFrontEnd\/storedCardPaymentV2\/MPaymentProcess.aspx?token=BCywhWAw+R3DhAoGEu7hhCp+yURTqRhmK\/6fPR3Nm+VlljAlF2EUHTJddU7yVd7p",
								"channelCode": "123",
								"respCode": "1003",
								"respDesc": "Transaction is pending for payment, please wait for backend notification."
							}
						}
						*/
						$res = [
								'status' => '000',
								'data' => (array) $content,
							];
						$result = [
								'request' => (array) $this->request,
								'response' => [
										'content' => json_encode($res),
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



		public function ParsePaymentPage($channel_code, $payment_url, $param = '')
		{
			try {
				$this->request['url'] = 'http://103.5.45.182:13579/parse/' .
					'2c2p' . '/' .
					$channel_code . '/' .
					base64_encode($payment_url) . '/' . $param;
				// $this->request['data'] = [
				// 		'vendor' => '2C2P',
				// 		'type' => $channel_code,
				// 		'url' => base64_encode($payment_url),
				// 	];
				// Go
				$get = $this->DoRequest('GET', $this->request);
				$response = (array) $get['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					// Parse data
					$content = (object) json_decode($content);
					if (
						isset($content) && !empty($content)
					) {
						if (
							!empty($content->status)
							&& $content->status == '000'
						) {
							$res = [
									'status' => '000',
									'data' => (array) $content->data,
								];
						} else {
							$res = [
									'status' => $content->status,
									'data' => (array) $content->data,
								];
						}
						$result = [
								'request' => (array) $this->request,
								'response' => [
										'content' => json_encode($res),
										'status_code' => 200,
									],
							];
					} else {
						throw new \Exception("Parsed data is empty", 1);
					}
				}
			} catch (\Throwable $e) {
				throw new \Exception($this->ThrowError($e));
			}
			return $result ?? [];
		}



		public function TransactionStatus(
			$payment_token
		) {
			try {
				// Hit Transaction Status Inquiry
				$this->request['form'] = [];
				$this->request['time'] = $this->transaction->getTime();
				$this->request['url'] = 'https://sandbox-pgw.2c2p.com/payment/4.1/transactionStatus';
				$this->request['headers'] = [
						'Accept' => 'text/plain',
						'Content-Type' => 'application/*+json',
						// '-debug-merchantID' => $this->init->getMID(),
						// '-debug-secret' => $this->init->getSecret(),
					];
				$this->request['option'] = [
						'as_json' => true,
					];
				$this->request['data_raw'] = [];
				$this->request['data'] = [
						'paymentToken' => $payment_token,
						'locale' => 'en',
						'additionalInfo' => true,
					];
				// Go
				$post = $this->DoRequest('POST', $this->request);
				$response = (array) $post['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					$content = (object) json_decode($content);
					// Parse data
					if (
						isset($content) && !empty($content)
					) {
						if (
							!empty($content->respCode)
							&& $content->respCode == '0000'
						) {
							// Success
							/*
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
							*/
							$res = [
									'status' => '000',
									'data' => (array) $content,
								];
						} else {
							// Failed
							/*
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
							*/
							$res = [
									'status' => $content->respCode ?? '999',
									'data' => (array) $content,
								];
						}
						$result = [
								'request' => (array) $this->request,
								'response' => [
										'content' => json_encode($res),
										'status_code' => 200,
									],
							];
					} else {
						throw new \Exception("Transaction status is empty", 1);
					}
				}
			} catch (\Throwable $e) {
				throw new \Exception($this->ThrowError($e));
			}
			return $result ?? [];
		}



		public function PaymentInquiry(
			$payment_token,
			$invoice_no
		) {
			try {
				// Hit Payment Inquiry
				$this->request['form'] = [];
				$this->request['time'] = $this->transaction->getTime();
				$this->request['url'] = 'https://sandbox-pgw.2c2p.com/payment/4.1/paymentInquiry';
				$this->request['headers'] = [
						'Accept' => 'text/plain',
						'Content-Type' => 'application/*+json',
						// '-debug-merchantID' => $this->init->getMID(),
						// '-debug-secret' => $this->init->getSecret(),
					];
				$this->request['option'] = [
						'as_json' => true,
					];
				$this->request['data_raw'] = [
						'paymentToken' => $payment_token,
						'merchantID' => $this->init->getMID(),
						'invoiceNo' => $invoice_no,
						'locale' => 'en',
					];
				$this->request['data'] = [
						'payload' => JWT::encode($this->request['data_raw'], $this->init->getSecret(), 'HS256'),
					];
				// Go
				$post = $this->DoRequest('POST', $this->request);
				$response = (array) $post['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					// Parse data
					$content = (object) json_decode($content);
					if (
						isset($content->payload) && !empty($content->payload)
					) {
						$content = JWT::decode($content->payload, $this->init->getSecret(), array('HS256'));
						if (
							!empty($content->respCode)
							&& $content->respCode == '0000'
						) {
							$res = [
									'status' => '000',
									'data' => (array) $content,
								];
						} else {
							$res = [
									'status' => $content->respCode ?? '999',
									'data' => (array) $content,
								];
						}
						$result = [
								'request' => (array) $this->request,
								'response' => [
										'content' => json_encode($res),
										'status_code' => 200,
									],
							];
					} else {
						throw new \Exception("Payment inquiry is empty", 1);
					}
				}
			} catch (\Throwable $e) {
				throw new \Exception($this->ThrowError($e));
			}
			return $result ?? [];
		}

	/*=================================   End of Function    ==================================*/

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

	/*==========================================================================================
												Start of More Function
	==========================================================================================**/

		public function GetEncryptCardData(\Growinc\Payment\Transaction $transaction)
		{
			$this->request['form'] = [];
			$this->request['time'] = $transaction->getTime();
			$this->request['url'] = 'http://localhost/git/GrowincPaymentLib/examples/tctp/enccard.php';
			$this->request['headers'] = [];
			$this->request['option'] = [
					'to_json' => 1,
				];
			$this->request['data_raw'] = [];
			$this->request['data'] = [
					'setCardNumber' => $transaction->getCardNumber(),
					'setCardExpMonth' => $transaction->getCardExpMonth(),
					'setCardExpYear' => $transaction->getCardExpYear(),
					'setCardCvv' => $transaction->getCardCVV(),
				];
			// Go
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			return $content;
		}

		public function EncryptCardData(\Growinc\Payment\Transaction $transaction)
		{
			if (isset($_POST['submit']) && !empty($_POST['submit'])) {
				echo $_POST['encryptedCardInfo'];
			} else {
				$html = '<!DOCTYPE HTML>
<html>
<head>
	<title>Secure Payment</title>
	<script src="https://code.jquery.com/jquery-1.11.3.js" type="text/javascript"></script>
	<script>
		$(document).ready(function() { $(\'#submit\').click(); });
	</script>
</head>
<body>
	<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" name="2c2p-payment-form" id="2c2p-payment-form" style="display:inline;">
cardnumber: <input type="text" data-encrypt="cardnumber" maxlength="16" placeholder="Credit Card Number" value="' . $transaction->getCardNumber() . '" /><br/>
month: <input type="text" data-encrypt="month" maxlength="2" placeholder="MM" value="' . $transaction->getCardExpMonth() . '" /><br/>
year: <input type="text" data-encrypt="year" maxlength="4" placeholder="YYYY" value="' . $transaction->getCardExpYear() . '" /><br/>
cvv: <input type="password" data-encrypt="cvv" maxlength="4" autocomplete="off" placeholder="CVV2/CVC2" value="' . $transaction->getCardCVV() . '" /><br/>
country_code: <input type="text" name="country_code" value="360" /><br/>
<hr>
<input type="submit" name="submit" id="submit" value="Submit" />
	</form>
	<!--<script src="https://t.2c2p.com/SecurePayment/api/my2c2p.1.6.9.min.js" type="text/javascript"></script>-->
	<script src="https://demo2.2c2p.com/2C2PFrontEnd/SecurePayment/api/my2c2p.1.6.9.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		My2c2p.onSubmitForm("2c2p-payment-form", function(errCode,errDesc){
				if(errCode!=0){
					alert(errDesc+" ("+errCode+")");
				}
		});
	</script>
</body>
</html>';
				echo $html;
			}
		}

	/*=================================   End of More Function   ==================================*/


}