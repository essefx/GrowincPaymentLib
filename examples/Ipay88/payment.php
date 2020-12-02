<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  require_once __DIR__ . '/../vendor/autoload.php';

  $init = new \Growinc\Payment\Init('ID01625', '1gUbnGkdKA');
  $init->setBaseURI('https://sandbox.ipay88.co.id');
  $init->setPaymentURL('https://sandbox.ipay88.co.id');
  $init->setResponseUrl('https://your-site/ipay88-response');
  $init->setBackendURL('https://your-site/ipay88-backend');

  $transaction = new \Growinc\Payment\Transaction();
  $transaction->setCustomerName('LOREB');
  $transaction->setCustomerEmail('lorem@gmail.com');
  $transaction->setCustomerPhone('081298983535');
  $transaction->setDescription('Product B00016 Baju Baru');
  // $transaction->setOrderID('123039600');

  // Maybank VA	9
  // Mandiri ATM	17
  // BCA VA	25
  // BNI VA	26
  // Permata VA	31

  $transaction->setPaymentId(9);

  /**
   * Dummy items
  */
  $detail_item = [
    ["id" => "item01", "price" => 50000, "quantity" => 2, "name" => "Ayam Zozozo"],
    ["id" => "item01", "price" => 30000, "quantity" => 5, "name" => "Ayam Zozozo"]
  ];
  // set items
  $transaction->setItem($detail_item);
  $vendor = new \Growinc\Payment\Vendors\Ipay88($init);

  // $result = $vendor->SecurePayment($transaction);
  // print_r($result);exit();
  try {

    $result = $vendor->SecurePayment($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    extract($result);
    print_r($response);
    //
    // print_r($vendor->getRequest());
    // print_r($vendor->getResponse()); // Get  PSR7 object
  } catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getCode();
  }
