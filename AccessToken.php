<?php
    require_once 'config.php';
    require_once 'functions.php';
    require_once 'util/loggerUtil.php';
    
    require_once 'properties/application.properties.php';
    require_once 'properties/' . $profile . '-application.properties.php';
    define('TRADEGECO_CLIENTID', $clientId);
    define('TRADEGECO_SECRET', $clientSecret);
    define('TRADEGECO_SCOPE', $tradegecoScope);
    define('TRADEGECO_URL', $tradegecoURL);
    
    require 'TradeGecoClient.php';
    
    $shop = trim($_GET['shop']);
    $isEshipper = trim($_GET['eshipper']);
    
    if($isEshipper == TRUE){
        m_log("Request Recieved for Access Token: ".$shop);
        $shopDetails = getDetails($shop);
        
        $tradegecoClient = new TradeGecoClient(TRADEGECO_URL, $shopDetails['AccessToken'], TRADEGECO_CLIENTID, TRADEGECO_SECRET);
        $shopDetailsFromAPIs = $tradegecoClient->call('GET','/accounts/current');
        m_log("Response from Validating Access Token : ".json_encode($shopDetailsFromAPIs));
        
        m_log($shopDetailsFromAPIs['account']['id']." == ".$shopDetails['StoreId']);
        if(isset($shopDetailsFromAPIs) && ($shopDetailsFromAPIs['account']['id'] == $shopDetails['StoreId'])){
            $shopDetails = getDetails($shop);
            
            $res-> validate = true;
            $res->shop = $shop;
            $res->accessToken = $shopDetails['AccessToken'];
            
            m_log("Message Prepard for EShipper ".json_encode($res));
            echo json_encode($res);
        }else{
			if(shopDetailsFromAPIs['error'] == 'You have exceeded the API rate limit. Please retry later.'){
				$res-> validate = true;
            }else{
				$res-> validate = false;
			}
            $res->shop = $shop;
            $res->accessToken = $shopDetails['AccessToken'];
            
            m_log("Message Prepard for EShipper ".json_encode($res));
            echo json_encode($res);
        }
    }else{
        $res-> validate = false;
        $res->shop = $shop;
        m_log("Phisher Message Prepard for EShipper ".json_encode($res));
        echo json_encode($res);
    }
    
   
    
?>