<?php

    session_start();
    include_once 'header.php';
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
    
    $data     =(file_get_contents('php://input'));
    $product    =json_decode($data);
    
    /* GET DOMAIN FROM HEDAERS  */
    $_HEADERS = apache_request_headers();
    $_DOMAIN  =$_HEADERS['X-Shopify-Shop-Domain'];
    m_log($_HEADERS);
    m_log($_DOMAIN);
    m_log("DATA from WEBHOOK - >".json_encode($data));

?>