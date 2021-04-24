<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		// Growinc
		'xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y', // secret_key
		'xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy' // public_key
	);
$init->setCallbackURL($_SERVER['REQUEST_URI']);

$transaction = new \Growinc\Payment\Transaction();

if (isset($_REQUEST['tokenData'])) {
	exit($_REQUEST['tokenData']);
}

$transaction->setAmount($_REQUEST['setAmount'] ?? '75000');
// $transaction->setCardNumber($_REQUEST['setCardNumber'] ?? '4000000000000002');
// $transaction->setCardNumber($_REQUEST['setCardNumber'] ?? '4259450300667554');
$transaction->setCardNumber($_REQUEST['setCardNumber'] ?? '5543021020044326');
// $transaction->setCardExpMonth($_REQUEST['setCardExpMonth'] ?? '12');
// $transaction->setCardExpMonth($_REQUEST['setCardExpMonth'] ?? '07');
$transaction->setCardExpMonth($_REQUEST['setCardExpMonth'] ?? '09');
// $transaction->setCardExpYear($_REQUEST['setCardExpYear'] ?? '2025');
// $transaction->setCardExpYear($_REQUEST['setCardExpYear'] ?? '2022');
$transaction->setCardExpYear($_REQUEST['setCardExpYear'] ?? '2021');
// $transaction->setCardCVV($_REQUEST['setCardCVV'] ?? '123');
// $transaction->setCardCVV($_REQUEST['setCardCVV'] ?? '831');
$transaction->setCardCVV($_REQUEST['setCardCVV'] ?? '563');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$enccarddata = $vendor->EncryptCardData($transaction);
	echo $enccarddata;
	/*
	{
		"id": "6082ee6cfeebf20020707061",
		"masked_card_number": "554302XXXXXX4326",
		"status": "VERIFIED",
		"metadata": {
			"bank": "PT. Bank Permata",
			"country_code": "ID",
			"type": "CREDIT",
			"brand": "MASTERCARD"
		},
		"card_info": {
			"bank": "PT. Bank Permata",
			"country": "ID",
			"type": "CREDIT",
			"brand": "MASTERCARD",
			"fingerprint": "6082edfe71cd830021e5e847"
		}
	}
	*/
} catch (\Throwable $e) {
	echo 'Encrypt card data failed: ' . $e->getMessage() . ':' . $e->getCode();
}
