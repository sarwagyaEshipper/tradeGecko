<?php
 require_once 'config.php';

 function xml2js($xmlnode) {
    $root = (func_num_args() > 1 ? false : true);
    $jsnode = array();

    if (!$root) {
        if (count($xmlnode->attributes()) > 0){
            $jsnode["$"] = array();
            foreach($xmlnode->attributes() as $key => $value)
                $jsnode["$"][$key] = (string)$value;
        }

        $textcontent = trim((string)$xmlnode);
        if (count($textcontent) > 0)
            $jsnode["<br>"] = $textcontent;

        foreach ($xmlnode->children() as $childxmlnode) {
            $childname = $childxmlnode->getName();
            if (!array_key_exists($childname, $jsnode))
                $jsnode[$childname] = array();
            array_push($jsnode[$childname], xml2js($childxmlnode, true));
        }
        return $jsnode;
    } else {
        $nodename = $xmlnode->getName();
        $jsnode[$nodename] = array();
        array_push($jsnode[$nodename], xml2js($xmlnode, true));
        return json_encode($jsnode);
    }
}




function callToEshipperAPI($url, $endPoint, $xml)
{


      $apiUrl = $url.$endPoint; 
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: appplication/xml'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
      $res =  curl_exec($ch);
      curl_close($ch);
      return $res;
  }

  function sendOrders($url,$endPoint,$array,$token)

    {
    
      global $con;
      $headers = array(

     'Content-Type'=>'application/json',
     'Authorization'=>$token

        );
      
           $apiUrl = $url.$endPoint;
           
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: appplication/json','Authorization:'.$token));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$array);
      $res =  curl_exec($ch);
      curl_close($ch);
      $arr= json_decode($res);
    
     // $LastOrder = $arr->LastSychOrderNumber;
      if(isset($arr->Status))
         {
           //header('location:eship_settings.php');
          return true;
          // $q= "SELECT * from OrderDetails WHERE StoreUrl = '".$_SESSION['shop']."'";
          // $r = mysqli_query($con,$q);
          // if(mysqli_num_rows($r))
          //   {
          //    $q= "UPDATE OrderDetails SET LastOrder = ".(int)$LastOrder." WHERE StoreUrl = '".$_SESSION['shop']."'";
          //   $res = mysqli_query($con,$q);
          //   if($res)
          //    {
          //     header('location:eshipperSettings.php');
          //    }
          // }
          
          // else
          // {
          //   $q = "INSERT INTO OrderDetails VALUES ('".$_SESSION['shop']."','".$LastOrder."',NOW(),'Shopify')";
          //   $res = mysqli_query($con,$q);
          //   if($res)
          //   {
             
          //   }
           
          // }
          
         
         }
      //var_dump(json_decode($res));

    }
    function getDetails($shop){
        global $con;
       $query = 'Select * from TradeStoreDetail Where StoreUrl="'.$shop.'" LIMIT 1';
       $r = mysqli_query($con, $query );
       if ($r) {
         # code...
        $res = mysqli_fetch_array($r);
        return $res;
          }
        }

function getCustomerId()
  {

  global $con;
   $query = 'Select EsCustomerId from TradeStoreDetail Where StoreUrl="'.$_SESSION['shop'].'" LIMIT 1';
   $r = mysqli_query($con, $query );
   if ($r) {
     # code...
    $res = mysqli_fetch_array($r);

    return $res['EsCustomerId'];
   }



  }

  function isAuthenticated()

        {
  global $con;
   $query = 'Select * from TradeStoreDetail Where StoreUrl="'.$_SESSION['shop'].'" LIMIT 1';
   $r = mysqli_query($con, $query );
   if ($r) {
     # code...
    $res = mysqli_fetch_array($r);

    return ((int)$res['IsAuthenticated']);

        }

}

function saveShippingMode($shop, $shippingMode,$resShip,$showLiveRates,$signatureRequired,$tailgateRequiredSource,$tailgateRequiredDestination,$selectedServices)

  {
   global $con;
   $query = "Select * from reverbShippingSettings WHERE StoreUrl='".$shop."'";
   $r = mysqli_query($con, $query );
   if (mysqli_num_rows($r)) {
     # code...
        $updateQuery = "UPDATE reverbShippingSettings SET ShippingMode ='".$shippingMode."' ,ResidentialShipToAdd ='".$resShip."',ShowLiveRates=".$showLiveRates.",SignatureRequired='".$signatureRequired."',TailgateRequiredSource=".$tailgateRequiredSource.",TailgateRequiredDestination=".$tailgateRequiredDestination.",SelectedServices='".$selectedServices."' WHERE StoreUrl='".$shop."'";
        $updateObj = mysqli_query($con,$updateQuery);
          if ($updateObj) {
            # code...
            return "success";
          }
          else
          {
            return "fail";
          }
    }
    else
    {
      $q = "INSERT INTO reverbShippingSettings VALUES ('".$_SESSION['shop']."','".$shippingMode."','".$resShip."','Reverb',".$showLiveRates.",'".$signatureRequired."',".$tailgateRequiredSource.",".$tailgateRequiredDestination.",'".$selectedServices."')";
	  
    
         $updateObj = mysqli_query($con,$q);
                if ($updateObj) {
                  # code...
                  return "success";
                }
                else
                {
                  return "fail";
                }

        }
}

function getShipmentSettings()
  {
    global $con;
      $query = "Select * from reverbShippingSettings WHERE StoreUrl='".$_SESSION['shop']."'";
   $r = mysqli_query($con, $query );
   return mysqli_fetch_assoc($r);
  }
  
  function getShipmentCost($customerId){
      global $$conEshipper;
      $query = "select total_charge, tracking_url from shipping_order where reference_code='#6248' and customer_id='.$customerId.';";
      $r = mysqli_query($conEshipper, $query );
      $res = mysqli_fetch_array($r);
      return $res;
      
  }
  
  function getRefreshToken($token){
      global $con;
      m_log("Refresh Token request from DB recieved : ".$token);
      $query = 'select * from TradeStoreDetail where AccessToken = "'.$token.'" and  InstallationSource = "tradegecko"';
      $r = mysqli_query($con, $query);
      if ($r) {
          # code...
          $res = mysqli_fetch_array($r);
          return $res;
      }
  }
  
  function saveTokens($accessToken, $refreshToken, $shopURL){
    global $con;

    $query = 'select * from TradeStoreDetail where StoreUrl = "'.$shopURL.'" and  InstallationSource = "tradegecko"';
    $resultSet = mysqli_query($con, $query);
    $resultCount = mysqli_num_rows($resultSet);
    if ($resultCount) {
        $query = 'update TradeStoreDetail set AccessToken = "'.$accessToken.'" , RefreshToken = "'.$refreshToken.'" where InstallationSource ="tradegecko" and StoreUrl = "'.$shopURL.'"';
        m_log("UPDATE Query->" . $query);
        $obj = mysqli_query($con, $query);
    }else{
        $query = 'INSERT INTO TradeStoreDetail (`StoreUrl`, `Platform`, RefreshToken, IsAuthenticated, InstallationSource) VALUES ("'.$shopURL.'","tradegecko","'.$refreshToken.'","0","tradegecko")';
        m_log("PARTIAL INSERT Query->" . $query);
        $obj = mysqli_query($con, $query);
    }
}
  
?>