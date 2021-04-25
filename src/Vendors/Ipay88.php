<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;
use Growinc\Payment\Transaction;

class Ipay88 extends Requestor implements VendorInterface
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

	public function RedirectPayment(Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(Transaction $transaction)
	{
		try {

			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['item'] = $this->transaction->getItem();
			$this->form['amount'] = (float) $this->transaction->getAmount() * 100;
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['item_detail'] = [
					[
						'id' => $this->form['invoice_no'],
						'price' => $this->form['amount'],
						'quantity' => 1,
						'name' => $this->form['item'],
						'brand' => '',
						'category' => '',
						'merchant_name' => '',
					]
				]; // only cc
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['customer_postal_code'] = $this->transaction->getPostalCode();
			$this->form['customer_city'] = $this->transaction->getCustomerCity();
			$this->form['customer_country_code'] = $this->transaction->getCountryCode();
			//
			$this->form['cc_name'] = $this->transaction->getCardHolderName();
			$this->form['cc_no'] = $this->transaction->getCardNumber();
			$this->form['cc_cvv'] = $this->transaction->getCardCVV();
			$this->form['cc_month'] = $this->transaction->getCardExpMonth();
			$this->form['cc_year'] = $this->transaction->getCardExpYear();
			//
			$this->form['billing_address'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => '',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => $this->form['customer_address'],
					'postal_code' => $this->form['customer_postal_code'],
					'city' => $this->form['customer_city'] ?? 'Jakarta',
					'country_code' => $this->form['customer_country_code'] ?? 'IDN',
				];
			$this->form['shipping_address'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => '',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => $this->form['customer_address'],
					'postal_code' => $this->form['customer_postal_code'],
					'city' => $this->form['customer_city'] ?? 'Jakarta',
					'country_code' => $this->form['customer_country_code'] ?? 'IDN',
				];
			$this->form['seller_detail']  =[
					'Id' => '',
					'Name' => '',
					'Url' => '',
					'SellerIdNumber' => '',
					'Email' => '',
					'Address' => [
							'FirstName' => '',
							'LastName' => '',
							'Address' => '',
							'City' => '',
							'PostalCode' => '',
							'Phone' => '',
							'CountryCode' => "ID",
						],
				];
			//
			$this->form['item_details'] = $this->form['item_detail'];
			$this->form['payment_method'] = $this->transaction->getPaymentMethod();
			// Signature
			$signature =
				$this->init->getSecret() .
				$this->init->getMID() .
				$this->form['order_id'] .
				$this->form['cc_no'] .
				$this->form['cc_month'] .
				$this->form['cc_year'] .
				$this->form['cc_cvv'] .
				$this->form['amount'] .
				$this->form['currency'];
			$encode_signature = base64_encode(hex2bin(sha1($signature)));
			$this->form['callback_url'] = $this->init->getCallbackURL();
			$this->form['return_url'] = $this->init->getReturnURL();
			// Data
			$this->request['data'] = [
					'MerchantCode' => $this->init->getMID(),
					'PaymentId' => $this->form['payment_method'],
					'Currency' => $this->form['currency'],
					'RefNo' => $this->form['order_id'],
					'Amount' => $this->form['amount'],
					'ProdDesc' => $this->form['description'],
					'UserName' => $this->form['customer_name'],
					'UserEmail' => $this->form['customer_email'],
					'UserContact' => $this->form['customer_phone'],
					'Remark' => 'Transaction ' . $this->form['order_id'],
					'CCHolderName' => $this->form['cc_name'],
					'CCNo' => $this->form['cc_no'],
					'CCCVV' => $this->form['cc_cvv'],
					'CCMonth' => $this->form['cc_month'],
					'CCYear' => $this->form['cc_year'],
					'Lang' => 'UTF-8',
					'ResponseURL' => $this->form['return_url'],
					'BackendURL' => $this->form['callback_url'],
					'Signature' => $encode_signature,
					'xfield1' => '',
					// 'itemTransactions' => $this->form['item_details'],
					// 'ShippingAddress' => $this->form['shipping_address'],
					// 'BillingAddress' => $this->form['billing_address'],
					// 'Sellers' => $this->form['seller_detail'],
				];
print_r(json_encode($this->request['data']));
exit();
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			switch ($this->form['payment_method']) {
				case '63': // OVO
					// Go
					$this->form['payment_url'] =
							filter_var(
									$this->init->getPaymentURL() .
									// '/epayment/entry.asp',
									'/epayment/entry_v2.asp',
									FILTER_SANITIZE_URL
						);
					$this->request['url'] = 'http://103.5.45.182:13571/parse/' .
						'ipay88' . '/' .
						'ovo' . '/' .
						base64_encode($this->form['payment_url']) . '/' .
						base64_encode(json_encode($this->request['data']));
					$this->request['headers'] = [
							'Content-Type' => 'application/x-www-form-urlencoded',
						];
					$this->request['option'] = [
							'as_json' => false,
						];
					$req = $this->DoRequest('GET', $this->request);
					break;
				default: // Others
					// Go
					// $this->form['payment_url'] = $this->init->getPaymentURL() . '/ePayment/WebService/PaymentAPI/Checkout';
					$this->form['payment_url'] =
							filter_var(
									$this->init->getPaymentURL() .
									'/ePayment/WebService/PaymentAPI/Checkout',
									FILTER_SANITIZE_URL
						);
					$this->request['url'] = $this->form['payment_url'];
					$this->request['headers'] = [
							'Content-Type' => 'application/json',
							'Accept' => 'application/json',
							'Authorization' => '',
							'Content-Length' => strlen(json_encode($this->request['data'])),
						];
					$this->request['option'] = [
							'as_json' => true,
						];
					$req = $this->DoRequest('POST', $this->request);
					break;
			}
			// Go
			$response = (array) $req['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					empty($content->ErrDesc)
					&& $content->ErrDesc === ""
				) {
					/*
						Status pembayaran
						"1" – Success.
						"0" – Fail.
						"6" – Pending (hanya tersedia untuk metode pembayaran ATM Transfer).
					*/
					if ($content->Status == 6) {
						// $status = 'pending';
					}
					/*
					// VA
					{
						"Status": "6",
						"ErrDesc": "",
						"MerchantCode": "ID01676",
						"PaymentId": "9",
						"Currency": "IDR",
						"RefNo": "0018650259",
						"Amount": "504800",
						"Remark": "Transaction 0018650259",
						"Signature": "44F568Dmfoa02uxQIGwmLDstvxE=",
						"xfield1": "",
						"TransId": "T0053241400",
						"AuthCode": "",
						"VirtualAccountAssigned": "7828705000002278",
						"TransactionExpiryDate": "18-04-2021 16:04",
						"CheckoutURL": "https://sandbox.ipay88.co.id/epayment/entryv3.asp?CheckoutID=89546bbe3d63694741e243b8e071e58b8c823dc07c632a0fa5f35b0c131c4575&Signature=8jk9e2l16oDyeqCgYak8PJ5rXIE%3d"
					}
					// Wallet
					{
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
				} else {
					throw new \Exception($content->ErrDesc);
				}
			} else {
				throw new \Exception($content);
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function Callback(object $request)
	{
		// Example incoming data
		/*
			{
				"MerchantCode": "ID01625",
				"PaymentId": "30",
				"RefNo": "0007609625",
				"Amount": "250000",
				"Currency": "IDR",
				"Remark": "",
				"TransId": "T0051206200",
				"AuthCode": "",
				"Status": "6",
				"ErrDesc": "",
				"Signature": "lkvo2Xuy7BImSfMoTBznJSUOEC8=",
				"CheckoutURL": "https://sandbox.ipay88.co.id/epayment/entry.asp?CheckoutID=5F822C024A102470C16A762C19EA29D7879A47B2EFF7C4151E309F00EDEADC6F&Signature=Nv2ub5JULwXf1X2x7B9CLe3z7K4%3d",
				"xfield1": ""
			}
			{
				"MerchantCode": "ID01676",
				"PaymentId": "26",
				"RefNo": "0018824953",
				"Amount": "700000",
				"Currency": "IDR",
				"Remark": "Transaction 0018824953",
				"TransId": "T0053259100",
				"AuthCode": "8228024900002200",
				"Status": "1",
				"ErrDesc": "",
				"Signature": "0h8XPhRJN\/ioARBGwoiGdy5DwP8=",
				"VirtualAccountAssigned": "8228024900002200",
				"TransactionExpiryDate": "20-04-2021 16:35",
				"PaymentDate": "19-04-2021 16:36"
			}
		*/
		try {
			if (!empty($request)) {
				SELF::Validate($request, [
						'PaymentId',
						'RefNo',
						'Amount',
						'Currency',
						'Status',
					]);
				$input =
					$this->init->getSecret() .
					$this->init->getMID() .
					$request->PaymentId .
					$request->RefNo .
					$request->Amount .
					$request->Currency .
					$request->Status;
				$signature = base64_encode(hex2bin(sha1($input)));
				if (strcmp($signature, $request->Signature) === 0) {
					$res = [
							'status' => '000',
							'data' => (array) $request,
						];
					$result = [
							'request' => (array) $request,
							'response' => [
									'content' => json_encode($res),
									'status_code' => 200,
								],
						];
				} else {
					throw new \Exception('Signature check failed');
				}
			} else {
				throw new \Exception('Callback is empty');
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		/*  */
		try {
			SELF::Validate($request, [
					'order_id',
					'amount'
				]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] =
				$this->init->getRequestURL() .
				'/epayment/enquiry.asp?' .
				'MerchantCode=' . $this->init->getMID() .
				'&RefNo=' . $request->order_id .
				'&Amount=' . $request->amount;
			$this->request['data'] = [];
			$this->request['headers'] = [];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (string) $content;
				if (
					isset($content)
					&& $content == '00'
				) {
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
					$res = [
						'status' => '000',
						'data' => $content,
					];
					$result = [
						'request' => (array) $request,
						'response' => [
							'content' => json_encode($res),
							'status_code' => 200,
						],
					];
				} else {
					throw new \Exception($content);
				}
			} else {
				throw new \Exception($content);
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
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
