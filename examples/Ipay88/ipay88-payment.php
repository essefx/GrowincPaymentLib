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
  $transaction->setCustomerPhone('081293145954');
  $transaction->setPostalcode('12345');
  

    /* start optional*/ 
  $transaction->setCountryCode('IDN');
  // $transaction->setAmount(100000);
  $transaction->setCustomerCity('Jakarta');
    /* end optional*/ 
  $transaction->setDescription('Product B00016 Baju Baru');
  $transaction->setCustomerAddress('Jl. Maju mundur kena');
  
    /*	paymentType:

    --> bank_transfer
      Maybank VA  => bank_transfer,maybank
      BCA VA      => bank_transfer,bca
      Mandiri ATM => bank_transfer,mandiri
      BNI VA      => bank_transfer,bni
      Permata VA  => bank_transfer,permata

    --> ewallet
      shopeepay => ewallet,shopeepay
      
    --> others
      alfamart => others,alfamart
      indomaret => others,indomaret
      indodana => others,indodana
      akulaku => others,akulaku
      kredivo => others,kredivo

    */

  $transaction->setPaymentMethod('ewallet,ovo');

  /* set Seller */ 
  $seller_detail = [
    "Id"		          => "your-seller-id",
    "Name"		        => "Your Seller Name",
    "Url"		          => "https://your-seller-site",
    "SellerIdNumber"	=> "your-seller-id-number",
    "Email"		        => "seller@ipay88.co.id",
    'address'         => [
        "FirstName"	=> "Seller first name",
        "LastName"	=> "Seller last name",
        "Address"	=> "Jl. Letjen S. Parman No.22-24",
        "City"		=> "Jakarta Barat",
        "PostalCode"	=> "11480",
        "Phone"		=> "08788888888",
        "CountryCode"	=> "ID"
      ],
  ];
  
  $transaction->setSeller($seller_detail);
  

  $item_detail = [
    ["id" => "a1", "price" => 10000, "quantity" => 1, "name" => "apel", "brand" => "Fuji Apple", "category" => "Fruit","merchant_name" => "Fruit-store"] //only cc
  ];
  $transaction->setItem($item_detail);
  
  $vendor = new \Growinc\Payment\Vendors\Ipay88($init);

  try {
    
    $result = $vendor->SecurePayment($transaction); // return payment URL
    print_r($result);

  } catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getCode();
  }
  