<?php

namespace Growinc\Payment;

interface Blueprint
{
	public function Index();
	public function GetToken($args);
	//
	public function RedirectPaymentCreateForm($args);
	public function RedirectPayment($args);
	public function RedirectPaymentCallback($args);
	public function RedirectPaymentCallbackAlt($args);
	//
	public function SecurePaymentCreateForm($args);
	public function SecurePayment($args);
	public function SecurePaymentCallback($args);
	public function SecurePaymentCallbackAlt($args);
	//
	public function PaymentOptionInquiry($args);
	public function PaymentOptionCancel($args);
	public function PaymentOptionSettle($args);
	public function PaymentOptionrefund($args);
	public function PaymentOptionRefundStatus($args);
}
