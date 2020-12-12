<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 1);
 
  require_once __DIR__ . '/../../vendor/autoload.php';

  $init = new \Growinc\Payment\Init('ID01625', '1gUbnGkdKA');
  $init->setBaseURI('https://sandbox.ipay88.co.id');
  $init->setPaymentURL('https://sandbox.ipay88.co.id/ePayment/WebService/PaymentAPI/Checkout');
  $init->setResponseUrl('https://your-site/ipay88-response');
  $init->setBackendURL('https://your-site/ipay88-backend');

  $transaction = new \Growinc\Payment\Transaction();
  $transaction->setCustomerName('LOREB');
  $transaction->setCustomerEmail('lorem@gmail.com');
  $transaction->setCustomerPhone('081298983535');
  $transaction->setDescription('Product B00016 Baju Baru');
  
  /*  paymentType:

  --> bank_transfer

    Maybank VA  => bank_transfer,maybankva
    BCA VA      => bank_transfer,bcava
    Mandiri ATM => bank_transfer,mandiriatm
    BNI VA      => bank_transfer,bniva
    Permata VA  => bank_transfer,permatava

  --> internet_banking 
  
    BCA KlikPay => internet_banking,bcakp
    CIMB Clicks => internet_banking,cimbkp
    Muamalat IB => internet_banking,muamalatkp
    Danamon Online internet_banking,Banking => danamonkp

  --> credit_card
  */

  $transaction->setPaymentMethod('bank_transfer,permata');

  // $transaction->setPaymentId(9);
  // $transaction->setPaymentId(9);

  $item_detail = [
    ["id" => "a1", "price" => 50000, "quantity" => 5, "name" => "apel", "brand" => "Fuji Apple", "category" => "Fruit","merchant_name" => "Fruit-store"] //only cc
  ];
  $transaction->setItem($item_detail);
  
  $vendor = new \Growinc\Payment\Vendors\Ipay88($init);

  try {
    
    $result = $vendor->SecurePayment($transaction); // return payment URL
    extract($result);
    print_r($response);

  } catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getCode();
  }
  