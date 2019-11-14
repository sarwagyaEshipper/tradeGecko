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

///////////////////////////////////////
$shop = $_SESSION['shop'];

$tradegecoClient = new TradeGecoClient(TRADEGECO_URL,  $_SESSION['token'], TRADEGECO_CLIENTID, TRADEGECO_SECRET);
$shopDetails = $tradegecoClient->call('GET','/accounts/current');

$shopId = $shopDetails['account']['id'];
$shopName = ucwords($shopDetails['account']['name']);
$data = getDetails($shop);
m_log("Data recieved on welcomeAboard.php: ".$data);
//////////////////////////////////////////// NEW CODE //////////////////////////////////


$xml='<?xml version="1.0" encoding="UTF-8"?>
    
<EShipper xmlns="http://www.eshipper.net/XMLSchema" username="'.$data['EsApiUsername'].'" password="'.$data['EsApiPassword'].'" version="3.1.9">
    
<ValidateUserRequest username="'.$data['EsApiUsername'].'" password="'.$api_password.'">
    
</ValidateUserRequest>
    
</EShipper>';

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


////////////////////////////////////
$query = 'SELECT * from TradeStoreDetail WHERE StoreUrl="'.$_SESSION['shop'].'" LIMIT 1';
$obj = mysqli_query($con,$query);
$resultArray= mysqli_fetch_assoc($obj);
$eShipperAccountName = $resultArray['EsApiUsername'];
if (isset($resultArray['EsCustomerName'])) {
    # code...
    $name=$resultArray['EsCustomerName'];
}
else
{
    $name='';
}

/*We need to load the webhooks for orders here START*/

        try{
            $product_hook_added = 0;
            $webhooks = $tradegecoClient->call('GET','/webhooks');
            m_log("Count: count(webhooks)". count($webhooks));
            if(count($webhooks)>0){
                for($i=0;$i<count($webhooks);$i++){
                    m_log("webhook: ".$webhooks['webhooks'][$i]['event']);
                    if($webhooks['webhooks'][$i]['event']==="order.finalized"){
                        $product_hook_added = 1;
                        break;
                    }
                }
            }
            m_log("Count: count(product_hook_added)". $product_hook_added);
            if(count($webhooks) == 0 || $product_hook_added==0){
                /* Add Order webhook */
                m_log("Creating webhook for client");
                $orderWebhook['webhook'] = array(
                    "event"=> "order.finalized",
                    "address"=> "$EshipperAPIUrl/tradeGecko?shop=$shop"
                );
                m_log("Order webhook payload prepared: ".json_encode($orderWebhook));
                $webhookResponse = $tradegecoClient->call('POST','/webhooks/',$orderWebhook);
                
                m_log("Created webhook for client ->".json_encode($webhookResponse));
                /* Add Product webhook */
            }
         }
        catch (TradeGecoApiException $e){
            m_log("EXCEPTION: Adding orders webhooks ->".$e);
        }
        /*We need to load the webhooks for orders here END*/
?>
<link rel="stylesheet" type="text/css" href="assets/css/style-welcome.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>


<body>
  <section class="head_banner">
	<div class="container">
		<div class="row">
			<div class="head_content">
				<div class="logo_sec">
					<a href="#"><img src="assets/images/logo.png" alt="logo"></a>
				</div>
				<h1 class="heading_content">Welcome to eShipper,<?php echo $name; ?><span class="head_simble">!</span></h1>
			</div>
		</div>
	</div>
  </section>
  
  <section class="detail_sec">
	<div class="container">
		<div class="row">
			<div class="wrapper_div">
				<?php
				if ($state=='true') {
				    echo'
			    <div class="apps-custom" style="background-color:white">
			    <h5>Your TradeGecko Store is successfully linked to your eShipper account. Here are the details:</h5>
			    <ul class="api-list">
			             <li><span>Store Name:</span> <span>' .$shopName.'</span></li>
			             <li><span>eShipper Account:</span> <span>. '.$eShipperAccountName.'</span></li>
    
			    </ul>
    
                <h5>You can access eShipper to see TradeGecko Orders. Log In at <a href="'.$EshipperAPIUrl.'/login.jsp">eShipper.</a></h5> <br>
			    <p>
    
                    <a href="" data-toggle="modal" data-target="#modalSubscriptionForm">Click Here </a>
                        to link your TradeGecko Store to a different eShipper Account.
                </p>
                <p style="margin-top:-10px;">
                    <a href="#" id="fancyLaunch">Click Here </a>
                        for a quick tutorial on How to Ship your TradeGecko Orders on eShipper.
                </p>
			    </div>';
				}
			else{ //Need to say there is problem - need kanika assistance
			    echo'
			    <div class="apps-custom" style="background-color:white">
			    <h5>Your TradeGecko Store is successfully linked to your eShipper account. Here are the details:</h5>
			    <ul class="api-list">
			             <li><span>Store Name:</span> <span>' .$shopName.'</span></li>
			             <li><span>eShipper Account:</span> <span>. '.$eShipperAccountName.'</span></li>
			    
			    </ul>
			    
                <h5>You can access eShipper to see TradeGecko Orders. Log In at <a href="'.$EshipperAPIUrl.'/login.jsp">eShipper.</a></h5> <br>
			    <p>
                   
                    <a href="" data-toggle="modal" data-target="#modalSubscriptionForm">Click Here </a> 
                        to link your TradeGecko Store to a different eShipper Account.
                </p>
                <p style="margin-top:-10px;">
                    <a href="#" id="fancyLaunch">Click Here </a> 
                        for a quick tutorial on How to Ship your TradeGecko Orders on eShipper.
                </p>
			    </div>';
			}
				?>
			</div>	
		</div>	
	</div>

<div class="modal fade" id="modalSubscriptionForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header text-center">
			<h3>eShipper Account</h3>
		</div>
	  <div class="form fancybox-content" id="login_form" style="width:100%">
					<div class="form-group">
					  <input type="text" class="user-id" name="shop" style="width:100%" type="text" value="" placeholder="API USERNAME" id="eshipText" />
					</div>
					<div class="form-group">
					  <input type="password" class="pass" style="width:100%" type="text" value="" placeholder="API PASSWORD" id="eshipPassword"/>
							<input type="hidden" class="pass" size="45" type="text" value="<?php echo $shopName; ?>" id="storeName"/>

					  <input type="hidden" class="pass" size="45" type="text" value="<?php echo $shopId; ?>"  id="storeId"/>

					</div>
					<div class="form-group">
					  <input name="commit" type="submit" value="Save" id="eshipperAuth" />
					</div>
					
					<div class="msg">
					  <p id="eshipperMsg"> </p>
					  
					</div>
		</div>

    </div>
  </div>
</div>

				
	<ul id="images" style="display:none;">
		
		<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/4.png"><img class="last" alt="example4" src="./gallery/4.png" /></a>
				<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/5.png"><img class="last" alt="example4" src="./gallery/5.png" /></a>

		<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/6.png"><img class="last" alt="example4" src="./gallery/6.png" /></a>

		<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/7.png"><img class="last" alt="example4" src="./gallery/7.png" /></a>

		<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/8.png"><img class="last" alt="example4" src="./gallery/8.png" /></a>
				<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/9.png"><img class="last" alt="example4" src="./gallery/9.png" /></a>

		<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/10.png"><img class="last" alt="example4" src="./gallery/10.png" /></a>

		<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/11.png"><img class="last" alt="example4" src="./gallery/11.png" /></a>

		<a class="fancybox" data-fancybox="gallery" id="example4" href="./gallery/12.png"><img class="last" alt="example4" src="./gallery/12.png" /></a>


	</ul>
  </section>
  

  <script src="https://d1g5417jjjo7sf.cloudfront.net/reverb-embedded-sdk.js"/>
  <script>ReverbEmbeddedSDK.init();</script>
  
  <script type="text/javascript">
$(document).ready(function($) {
    var fancyGallery = $("#images").find("a");
    fancyGallery.attr("data-fancybox","gallery").fancybox({
        type: "image"
    });
    $('#fancyLaunch').on('click', function(e) {
    	e.preventDefault();
        fancyGallery.eq(0).click(); 
    });

    $(document).ready(function() {
				$("#opener").fancybox();
			});

    $('#eshipperAuth').on('click', function(e){
 			e.preventDefault();
   			var dataArray=[];
                
       var uname   =$('#eshipText').val();
       var upass   =$('#eshipPassword').val();
       var sname   =$('#storeName').val();
      var sid      =$('#storeId').val();
      var redi   = false;
       dataArray.push({uname:uname,upass:upass,storeId:sid,storeName:sname});  
       
$.ajax({
         url: 'updateEshipperDetails.php',
         type: 'post',
         data: {uname:uname,upass:upass,storeId:sid,storeName:sname,redi:redi},
         success: function(r) {
//            // Re-enable add to cart button.
     
     
       var m = JSON.parse(r);
     
       var data = m.msg;
       console.log(data);

       if (data.indexOf('Congrats') !==-1) {
          $('#eshipperMsg').removeClass('bg-danger').addClass('bg-success').html('Details updated successfully');
		   $( ".api-list li:first-child" ).html("<span>API Username:</span>&nbsp;<span>"+uname.toUpperCase()+"</span>")
}

          else {
           $('#eshipperMsg').removeClass('bg-success').addClass('bg-danger').html(data);
         } 
       
          }, 
          error: function() {
           //alert();
                       
          }
         });
   });
 });
</script>
