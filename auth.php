<?php
session_start();
$redirect = true;
$register = false;

require_once 'properties/application.properties.php';
require_once 'properties/' . $profile . '-application.properties.php';

require_once 'TradeGecoClient.php';
require_once 'config.php';
require_once 'functions.php';
require_once 'util/loggerUtil.php';


define('TRADEGECO_CLIENTID', $clientId);
define('TRADEGECO_SECRET', $clientSecret);
define('TRADEGECO_SCOPE', $tradegecoScope);
define('TRADEGECO_URL', $tradegecoURL);

$n = $_POST['uname'];
$p = $_POST['upass'];
$id = $_POST['storeId'];
$sn = $_POST['storeName'];

m_log("PROFILE-".$profile);
m_log("Auth.php|".$_SESSION['shop']."-"."");

$url = 'https://web.eshipper.com/shopify/auth';
$xml = '<?xml version="1.0" encoding="UTF-8"?>
              <EShipper xmlns="https://web.eshipper.com/shopify/auth" version="3.0.0">
              <CREDENTIALS username="' . $n . '" password="' . $p . '" version="3.0.1.2"></CREDENTIALS>
              <AUTHORIZATION name="' . $sn . '" domain="' . $_SESSION['shop'] . '"  token="' . $_SESSION['token'] . '" id="' . $id . '"></AUTHORIZATION>
              </EShipper>';

if (isset($_POST['redi'])) {
    $redirect = false;
    // code...
}
m_log("Auth.php|".$_SESSION['shop']."-"."Authenticating from eShipper:".$xml);

m_log("Auth.php|".$_SESSION['shop']."-"."EshipperAPIURL:".$EshipperAPIUrl);

$res = callToEshipperAPI($EshipperAPIUrl, '/shopify/auth', $xml);

$xml = simplexml_load_string($res);
$r = array();
$arr_data = json_decode(xml2js($xml), true);
$array = (array) $arr_data;

$arr = $array['EShipper'];

foreach ($arr[0] as $x => $val) {
    
    if (is_array($val)) {
        if (isset($val[0]['Error'])) {
            // code...
            $r['msg'] = $val[0]['Error'][0]['$']['Message'];
            m_log("Auth.php|".$_SESSION['shop']."-"."Response from eShipper: ERROR".$r['msg']);
        }
        // code...
        
        foreach ($val as $y => $value) {
            
            if ($x == 'AuthReply') {
                
                $r['msg'] = $value['Auth'][0]['$']['Message'];
                $r['cId'] = $value['Customer'][0]['ID'][0]['<br>'];
                $r['cName'] = $value['Customer'][0]['NAME'][0]['<br>'];
                m_log("Auth.php|".$_SESSION['shop']."-"."Response from eShipper:".$r['msg']."|".$r['cId']."|".$r['cName']);
                
                $query = 'SELECT * FROM TradeStoreDetail WHERE StoreUrl="'.$_SESSION['shop'].'"';
                $obj = mysqli_query($con, $query);
                $DetailCount = mysqli_num_rows($obj);
                if ($DetailCount) {
                    $query = 'UPDATE TradeStoreDetail SET AccessToken="' . $_SESSION['token'] . '", EsApiUsername="' . $n . '", EsApiPassword="' . $p . '", `StoreId` ="'.$id.'", `IsAuthenticated`="1",  `EsCustomerId` = "'.$r['cId'].'", `EsCustomerName` = "'. $r['cName'] .'" WHERE StoreUrl="' . $_SESSION['shop'] . '"';
                    m_log("Auth.php|".$_SESSION['shop']."-"."UPDATE Query->".$query);
                    $obj = mysqli_query($con, $query);
                } else {
                    $q = 'INSERT INTO TradeStoreDetail (`StoreUrl`, `StoreId`, `Platform`, `AccessToken`, `EsApiUsername`, `EsApiPassword`, `IsAuthenticated`, `EsCustomerId`, `EsCustomerName`, `InstallationSource`) VALUES ("'. $_SESSION['shop'].'",'.$id.',"tradegecko","'. $_SESSION['token'] .'","' . $n . '","' . $p . '",1,"' . $r['cId'] . '","'. $r['cName'] .'","tradegecko")';
                    m_log("Auth.php|".$_SESSION['shop']."-"."INSERT Query->".$q);
                    $res = mysqli_query($con, $q);
                }
            }
        }
    }
}
echo json_encode($r);
?>
