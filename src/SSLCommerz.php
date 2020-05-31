<?php

namespace SSLCommerz;

class SSLCommerz{

  private $direct_api_url;

  private $store_id;
  private $store_passwd;
  private $success_url;
  private $fail_url;
  private $cancel_url;
  private $sandbox;

  private $total_amount;
  private $currency = 'BDT';

  private $emi = false;
  private $emi_option;
  private $emi_max_inst_option;
  private $emi_selected_inst;




  public function __construct($store_id, $store_password, $success_url, $fail_url, $cancel_url, $sandbox = true){
    $this->store_id = $store_id;
    $this->store_passwd = $store_password;
    $this->success_url = $success_url;
    $this->fail_url = $fail_url;
    $this->cancel_url = $cancel_url;
    $this->sandbox = $sandbox;
  }

  /**
   * @param mixed $total_amount
   */
  public function setTotalAmount($total_amount): void{
    $this->total_amount = $total_amount;
  }

  /**
   * @param string $currency
   */
  public function setCurrency(string $currency): void{
    $this->currency = $currency;
  }


  /**
   * @param $emi_option
   * @param $emi_max_inst_option
   * @param $emi_selected_inst
   */
  public function setEMI($emi_option, $emi_max_inst_option, $emi_selected_inst){
    $this->emi_option = $emi_option;
    $this->emi_max_inst_option = $emi_max_inst_option;
    $this->emi_selected_inst = $emi_selected_inst;
    $this->emi = true;
  }

  /**
   *
   */
  public function init(){
    $post_data = $this->makeData();

//    dd($post_data);
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $this->direct_api_url );
    curl_setopt($handle, CURLOPT_TIMEOUT, 30);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($handle, CURLOPT_POST, 1 );
    curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


    $content = curl_exec($handle );

    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

    if($code == 200 && !( curl_errno($handle))) {
      curl_close( $handle);
      $sslcommerzResponse = $content;
    } else {
      curl_close( $handle);
      echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
      exit;
    }

    # PARSE THE JSON RESPONSE
    $sslcz = json_decode($sslcommerzResponse, true );
//    dd($sslcz);

    if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
      # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
      # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
      echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
      # header("Location: ". $sslcz['GatewayPageURL']);
      exit;
    } else {
      echo "JSON Data parsing error!";
    }
  }

  private function makeData() {
    $post_data = array();
    // store_id
    if(isset($this->store_id)){
      $post_data['store_id'] = $this->store_id;
    }
    // store_passwd
    if(isset($this->store_passwd)){
      $post_data['store_passwd'] = $this->store_passwd;
    }
    // success_url
    if(isset($this->success_url)){
      $post_data['success_url'] = $this->success_url;
    }
    // fail_url
    if(isset($this->fail_url)){
      $post_data['fail_url'] = $this->fail_url;
    }
    // cancel_url
    if(isset($this->cancel_url)){
      $post_data['cancel_url'] = $this->cancel_url;
    }

    // sandbox
    if($this->sandbox){
      $this->direct_api_url = 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php';
      $post_data['tran_id'] = "SSLCZ_TEST_".uniqid();
    }else{
      $this->direct_api_url = 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
      $post_data['tran_id'] = "SSLCZ_".uniqid();
    }

    // total_amount
    if(isset($this->total_amount)) {
      $post_data['total_amount'] = $this->total_amount;
    }

    $post_data['currency'] = $this->currency;

    if($this->emi) {
      $post_data['emi_option'] = $this->emi_option;
      $post_data['emi_max_inst_option'] = $this->emi_max_inst_option;
      $post_data['emi_selected_inst'] = $this->emi_selected_inst;
    }
    return $post_data;
  }

}




//$post_data = array();
//$post_data['store_id'] = "testbox";
//$post_data['store_passwd'] = "qwerty";
//$post_data['total_amount'] = "103";
//$post_data['currency'] = "BDT";
//$post_data['tran_id'] = "SSLCZ_TEST_".uniqid();
//$post_data['success_url'] = "http://localhost/new_sslcz_gw/success.php";
//$post_data['fail_url'] = "http://localhost/new_sslcz_gw/fail.php";
//$post_data['cancel_url'] = "http://localhost/new_sslcz_gw/cancel.php";
//# $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE
//
//# EMI INFO
//$post_data['emi_option'] = "1";
//$post_data['emi_max_inst_option'] = "9";
//$post_data['emi_selected_inst'] = "9";
//
//# CUSTOMER INFORMATION
//$post_data['cus_name'] = "Test Customer";
//$post_data['cus_email'] = "test@test.com";
//$post_data['cus_add1'] = "Dhaka";
//$post_data['cus_add2'] = "Dhaka";
//$post_data['cus_city'] = "Dhaka";
//$post_data['cus_state'] = "Dhaka";
//$post_data['cus_postcode'] = "1000";
//$post_data['cus_country'] = "Bangladesh";
//$post_data['cus_phone'] = "01711111111";
//$post_data['cus_fax'] = "01711111111";
//
//# SHIPMENT INFORMATION
//$post_data['ship_name'] = "Store Test";
//$post_data['ship_add1 '] = "Dhaka";
//$post_data['ship_add2'] = "Dhaka";
//$post_data['ship_city'] = "Dhaka";
//$post_data['ship_state'] = "Dhaka";
//$post_data['ship_postcode'] = "1000";
//$post_data['ship_country'] = "Bangladesh";
//
//# OPTIONAL PARAMETERS
//$post_data['value_a'] = "ref001";
//$post_data['value_b '] = "ref002";
//$post_data['value_c'] = "ref003";
//$post_data['value_d'] = "ref004";
//
//# CART PARAMETERS
//$post_data['cart'] = json_encode(array(
//  array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
//  array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
//  array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
//  array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
//));
//$post_data['product_amount'] = "100";
//$post_data['vat'] = "5";
//$post_data['discount_amount'] = "5";
//$post_data['convenience_fee'] = "3";


//# REQUEST SEND TO SSLCOMMERZ
//$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";
//
//$handle = curl_init();
//curl_setopt($handle, CURLOPT_URL, $direct_api_url );
//curl_setopt($handle, CURLOPT_TIMEOUT, 30);
//curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
//curl_setopt($handle, CURLOPT_POST, 1 );
//curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
//curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC
//
//
//$content = curl_exec($handle );
//
//$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
//
//if($code == 200 && !( curl_errno($handle))) {
//  curl_close( $handle);
//  $sslcommerzResponse = $content;
//} else {
//  curl_close( $handle);
//  echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
//  exit;
//}
//
//# PARSE THE JSON RESPONSE
//$sslcz = json_decode($sslcommerzResponse, true );
//
//if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
//  # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
//  # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
//  echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
//  # header("Location: ". $sslcz['GatewayPageURL']);
//  exit;
//} else {
//  echo "JSON Data parsing error!";
//}
