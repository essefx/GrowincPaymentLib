<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Winpay extends Requestor implements VendorInterface
{

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		
	}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		
	}

	public function Callback(object $request)
	{
		// Example incoming data
		/*
		{
		  "id_transaksi": "5757636",
		  "no_reff": "7891092505",
		  "response_code": "00",
		  "id_produk": "SCPIMNDRCP",
		  "method_code": "MANDIRICP",
		  "keterangan": "Transaksi anda berhasil"
		}
		*/
		try {
			$result = 'ACCEPTED';
			$content = [
					'status' => '000',
					'data' => (array) $request,
				];
			$result = [
					'request' => (array) $request,
					'response' => [
							'content' => $result,
							'status_code' => 200,
						],
				];
			
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
		try {
			SELF::Validate($request, ['order_id']);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL() . $request->order_id . '/status';

			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic '.base64_encode($this->init->getMID().':'),
			];

			$get = $this->DoRequest('GET', $this->request);

			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (	!empty($content->status_code)
						&& $content->status_code == "201"
						&& $content->order_id == $request->order_id
				) {
					// Success
					/*
					{
						"va_numbers": [
							{
								"bank": "bca",
								"va_number": "53042878140"
							}
						],
						"payment_amounts": [],
						"transaction_time": "2020-10-29 15:42:50",
						"gross_amount": "150000.00",
						"currency": "IDR",
						"order_id": "0003960969",
						"payment_type": "bank_transfer",
						"signature_key": "b8d7baceab8967af2fdebb82f497fbf4be957e0147f34e910fe9abfc533f883f1206e6c7a72d111ff61331254e3ff9f609c16cc81762e15d9ee6c53de36c65ff",
						"status_code": "201",
						"transaction_id": "e84972ee-b1e1-4f84-8fcd-a8a561999f08",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"status_message": "Success, transaction is found",
						"merchant_id": "G345053042"
					}
					*/
					$content = [
							'status' => '000',
							'data' => (array) $content,
						];
					$result = [
							'request' => (array) $request,
							'response' => [
									'content' => json_encode($content),
									'status_code' => 200,
								],
						];
				} else {
					throw new \Exception($content->status_message);
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