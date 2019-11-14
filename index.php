<?php
/*
 * index page
 * @author Sarwagya Khosla
 * @Description: The first page to redirect user on click of install plugin / application
 */
ob_start();
session_unset();
session_start();
require_once 'properties/application.properties.php';
require_once 'properties/' . $profile . '-application.properties.php';
ini_set("display_errors", 1);
error_reporting(E_ALL);

define('TRADEGECO_CLIENTID', $clientId);
define('TRADEGECO_SECRET', $clientSecret);
define('TRADEGECO_SCOPE', $tradegecoScope);
define('TRADEGECO_URL', $tradegecoURL);

require 'TradeGecoClient.php';
require_once 'util/loggerUtil.php';
require 'functions.php';


if (isset($_GET['code'])) {
    // if the code param has been sent to this page... we are in Step 2
    // Step 2: do a form POST to get the access token
    m_log("Code Recieved for from URL - >".$_GET['code']);
    $reverbClient = new TradeGecoClient(TRADEGECO_URL, "", TRADEGECO_CLIENTID, TRADEGECO_SECRET);
    session_unset();
    
    // Now, request the token and store it in your session.
    $response = $reverbClient->getAccessToken($_GET['code']);
    m_log("Access_token : ".$response['access_token']." Refresh_token :". $response['refresh_token']);
    $_SESSION['token'] = $response['access_token'];
    $_SESSION['refresh_token'] = $response['refresh_token'];
    
    $accessToken = $_SESSION['token'];
    $refreshToken =  $_SESSION['refresh_token'];
    
    // We are making tradeGeco base URL as shopURL because we are using the existing shopify functionality in our java code.
    $tradegecoClient = new TradeGecoClient(TRADEGECO_URL,  $_SESSION['token'], TRADEGECO_CLIENTID, TRADEGECO_SECRET);
    $shopDetails = $tradegecoClient->call('GET','/accounts/current');
    $shopName = ucwords($shopDetails['account']['name']);
    $shopURL = TRADEGECO_URL.'/'.$shopName;
    m_log("Access Token: ".$accessToken." for Shop URL - >".$shopURL);
    
    saveTokens($accessToken, $refreshToken, $shopURL);
    
    if ($_SESSION['token'] != '')
        $_SESSION['shop'] = $shopURL;

    header("location:login.php");
    exit();
} else if (isset($_POST['install']) || isset($_GET['install'])) {
    m_log("Request to Install Application");
    $tradegecoClient = new TradeGecoClient(TRADEGECO_URL, "", TRADEGECO_CLIENTID, TRADEGECO_SECRET);
    $authURL = $tradegecoClient->getAuthorizeUrl(REVERB_SCOPE, '');
    m_log("AuthorizeURL ->".$authURL);
    header("Location: " . $authURL);
    exit();
}

?>


<!doctype html>
<html>
<head>
<style>
.eshipper-container:focus {
	outline: none;
}

input#shop:focus {
	outline: 1px #ddd;
}

.eshipper-container {
	position: absolute;
	top: 50%;
	left: 0;
	right: 0;
	transform: translateY(-50%);
}

.front-end {
	width: 100%;
	text-align: center;
	display: inline-block;
	padding-top: 3%;
	padding-bottom: 1%;
}

.eshipper_img {
	margin-bottom: 2%;
}

input#shop {
	width: 320px;
	padding: 14px 20px;
	font-size: 14px;
	box-shadow: 0 0 0 1px #ddd;
	border: 0;
	border-radius: 5px;
	background-color: #fff;
	transition: all 150ms;
}

input[type="submit"] {
	width: 357px;
	padding: 10px 20px;
	border-radius: 5px;
	background: #472f92;
	border: none;
	color: #fff;
	font-size: 16px;
}

.eshipper-container {
	width: 55%;
	margin: 0 auto;
	border: 1px solid rgba(71, 47, 146, 0.65);
	background: rgba(249, 249, 249, 0.65);
}
</style>
</head>
<body>
	<div class="eshipper-container">
		<div class="front-end">
			<!-- div class="eshipper_img">
				<img
					src=http://www.eshipper.com/hs-fs/hubfs/assets/logo.png?t=1504044189408&width=279&height=80&name=logo.png>
			</div-->
			<h2>Install eShipper App to your TradeGecko Application.</h2>



			<form action="" method="post">
				<p>
					<input type='hidden' id="install" name="install" size="45" type="text" value=""/>
				</p>
				<p>
					<input name="commit" type="submit" value="Install" />
				</p>
				<p>
					
				</p>
			</form>
		</div>
	</div>
</body>
</html>

