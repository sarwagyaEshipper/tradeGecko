<?php
session_start();
$redirect = true;
$register = false;

require_once 'TradeGecoClient.php';

require_once 'properties/application.properties.php';
require_once 'properties/' . $profile . '-application.properties.php';

define('TRADEGECO_CLIENTID', $clientId);
define('TRADEGECO_SECRET', $clientSecret);
define('TRADEGECO_SCOPE', $tradegecoScope);
define('TRADEGECO_URL', $tradegecoURL);

require_once 'util/loggerUtil.php';
require_once 'config.php';
require_once 'functions.php';

//$myshopify_domain = $_SESSION['shop'];
$shop = $_SESSION['shop'];
m_log("register.php|".$_SESSION['shop']."-");
$tradegecoClient = new TradeGecoClient(TRADEGECO_URL,  $_SESSION['token'], TRADEGECO_CLIENTID, TRADEGECO_SECRET);

$shopDetails = $tradegecoClient->call('GET','/accounts/current');

$shopId = $shopDetails['account']['id'];
$shopName = ucwords($shopDetails['account']['name']);

if(isset($shopDetails['name'])){
    $myshopify_store = $shopDetails['account']['name'];
}

$myshopify_domain = $shop;
$token  = $_SESSION['token'];



$xml = '<?xml version="1.0" encoding="UTF-8"?>
<EShipper xmlns="http://www.eshipper.net/XMLSchema" franchise_username="cwwfranchise1" franchise_password="frpass123" version="3.0.1.2">
<CustomerCreationRequest><User password="'.$_POST['regPassword'].'" username="'.$_POST['username'].'"/> 
<Address invoicingEmail="'.$_POST['regEmail'].'" email="'.$_POST['regEmail'].'" phone="'.$_POST['regPhone'].'" zip="'.$_POST['regZip'].'" country="'.$_POST['customerCountry'].'" province="'.$_POST['selectedProvince'].'" city="'.$_POST['regCity'].'" address="'.$_POST['regadd1'].'" contactName="'.$_POST['regContactName'].'" companyName="'.$_POST['regCompanyName'].'"/>
<Store storeId="'.$shopName.'" domainName="'.$myshopify_domain.'"/>
</CustomerCreationRequest>
</EShipper>';

      m_log("register.php|".$_SESSION['shop']."-"."Customer Creation Request to eShipper->".$xml);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $EshipperAPIUrl.'/rpc2');
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: appplication/xml'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
      $res =  curl_exec($ch);
      //(curl_error($ch));
      curl_close($ch);



          $xml = simplexml_load_string($res);
          
          $r = array();
     //($xml);
          $arr_data = json_decode(xml2js($xml),true);
          $array = (array) $arr_data;
       
         $arr = $array['EShipper'][0];
        
        // ($arr['CustomerCreationReply']);
         if (isset($array['EShipper'][0]['CustomerCreationReply'][0]['Errors'])) {
           # code...
           $errors = ($array['EShipper'][0]['CustomerCreationReply'][0]['Errors'][0]['Error'][0]['$']['Message']);
           $msgResponse = array('state'=>'Failed','msg'=>$errors);
           m_log("register.php|".$_SESSION['shop']."-"."Customer Creation Response->".$msgResponse);
           echo json_encode($msgResponse);
           die();
           
         }

         m_log("register.php|".$_SESSION['shop']."-"."Customer Creation Response: Success->".$xml);
         
	 	 $userId       = ($arr['CustomerCreationReply'][0]['Customer'][0]['$']['id']);
	 	 $api_username = ($arr['CustomerCreationReply'][0]['User'][0]['$']['api_username']);
	 	 $api_password = ($arr['CustomerCreationReply'][0]['User'][0]['$']['api_password']);
 	 	 $username     = ($arr['CustomerCreationReply'][0]['User'][0]['$']['username']);
 		 $shop         = ($_SESSION['shop']);
 		 $token        = ($_SESSION['token']);


     $xml='<?xml version="1.0" encoding="UTF-8"?><EShipper xmlns="http://www.eshipper.net/XMLSchema" username="'.$api_username.'" password="'.$api_password.'" version="3.1.9">
            <ValidateUserRequest username="'.$api_username.'" password="'.$api_password.'">
            </ValidateUserRequest>
            </EShipper>';

     m_log("register.php|".$_SESSION['shop']."-"."ValidateRequest to eShipper: ".$xml);
     
     $res = callToEshipperAPI($EshipperAPIUrl, '/rpc2', $xml);

    $xml = simplexml_load_string($res);
          $r = array();
     //($xml);
          $arr_data = json_decode(xml2js($xml),true);
          $array = (array) $arr_data;
          $state='false';
          $result='Failed';

          $result = ($array['EShipper'][0]['ValidateUserReply'][0]['UserDetails'][0]['$']['result']);
          $state  =($array['EShipper'][0]['ValidateUserReply'][0]['UserDetails'][0]['$']['Active']);

         $r['msg']=$result;
         $r['state']=$state;
         m_log("register.php|".$_SESSION['shop']."-"."ValidateRequest Response: ".$result."|".$state);
         
         /*Auth Token Fix for Sign-Up By Sarwagya Khosla START*/
         if($result == 'Success'){ //If validated from Eshipper then goahead
             
             $xml = '<?xml version="1.0" encoding="UTF-8"?>
                  <EShipper xmlns="https://devweb.eshipper.com/shopify/auth" version="3.0.0">
                  <CREDENTIALS username="'.$api_username.'" password="'.$api_password.'" version="3.0.1.1"></CREDENTIALS>
                  <AUTHORIZATION name="'.$myshopify_store.'" domain="'.$myshopify_domain.'"  token="'.$_SESSION['token'].'" id="'.$shopId.'"></AUTHORIZATION>
                  </EShipper>';
             m_log("Authorization Request: ".$_SESSION['shop']);
             m_log("Authorization Request: XML: ".$_SESSION['shop'].$xml);
             
             $res = callToEshipperAPI($EshipperAPIUrl, '/shopify/auth', $xml);
             m_log("Authorization Response: ".$_SESSION['shop']."=>".$res);
             $xml = simplexml_load_string($res);
             $arr_data = json_decode(xml2js($xml),true);
             m_log("Authorization Response: ".$_SESSION['shop']."=>".$arr_data);
             $array = (array) $arr_data;
             $arr = $array['EShipper'];
             $isPosted = true;
             foreach($arr[0] as $x=>$val){
                 if (is_array($val)) {
                     if (isset($val[0]['Error'])) {
                         # code...
                         $r['msg'] = $val[0]['Error'][0]['$']['Message'];
                         $isPosted = false;
                     }
                 }
             }
             
             if($isPosted){ //When user profile created and validated at Eshipper then we push the details to plugins DB
                 // otherwise it will keep allowing user to login without putting any credentials because access token matched.
                 m_log("Request Posted".$_SESSION['shop']);
                 $query = "SELECT * FROM TradeStoreDetail WHERE StoreUrl='".$_SESSION['shop']."'";
                 $obj = mysqli_query($con,$query);
                 $DetailCount = mysqli_num_rows($obj);
                 if ($DetailCount) {
                     $query = 'UPDATE TradeStoreDetail SET AccessToken="' . $_SESSION['token'] . '", EsApiUsername="' . $api_username . '", EsApiPassword="' . $api_password . '", `IsAuthenticated`="1" WHERE StoreUrl="' . $_SESSION['shop'] . '"';
                     m_log("Auth.php|".$_SESSION['shop']."-"."UPDATE Query->".$query);
                     $obj = mysqli_query($con, $query);
                 } else {
                     $q = 'INSERT INTO TradeStoreDetail (`StoreUrl`, `StoreId`, `Platform`, `AccessToken`, `EsApiUsername`, `EsApiPassword`, `IsAuthenticated`, `EsCustomerId`, `EsCustomerName`, `InstallationSource`) VALUES ("'. $_SESSION['shop'].'",'.$id.',"tradegecko","'. $_SESSION['token'] .'","' . $api_username . '","' . $api_password . '",1,"' . $userId . '","'. $username .'","tradegecko")';
                     m_log("Auth.php|".$_SESSION['shop']."-"."INSERT Query->".$q);
                     $res = mysqli_query($con, $q);
                 }
                 
             }
             
         }
         /*Auth Token Fix for Sign-Up By Sarwagya Khosla END*/
         
           
              
       echo json_encode($r);
       die();
?>
