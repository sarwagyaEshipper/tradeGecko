<?php
header('p3p: CP="ALL DSP COR PSAa PSDa OUR NOR ONL UNI COM NAV"');
ob_start();
session_start();
set_time_limit(0);
ini_set("display_errors", 1);
ini_set('session.gc_maxlifetime', 36000);
session_set_cookie_params(36000);
error_reporting(E_ALL);
//echo $_SESSION['token'];
//echo isset($_SESSION['shop']);

require_once 'util/loggerUtil.php';

if (!isset($_SESSION['shop'])) {
    # code...
    header('location:/admin/apps');
}
include_once 'config.php';
include 'header.php';
require_once 'functions.php';
?>
</head>
<body>
	
<?php

$pn='';
$nextPage=''; 
$lastPage='';
$outputList = '';
$GetStyle="notfound";

require_once 'properties/application.properties.php';
require_once 'properties/' . $profile . '-application.properties.php';
define('TRADEGECO_CLIENTID', $clientId);
define('TRADEGECO_SECRET', $clientSecret);
define('TRADEGECO_SCOPE', $tradegecoScope);
define('TRADEGECO_URL', $tradegecoURL);

require 'TradeGecoClient.php';


$query = 'SELECT AccessToken from TradeStoreDetail WHERE StoreUrl="'.$_SESSION['shop'].'" LIMIT 1';
$obj = mysqli_query($con,$query);
$resultArray= mysqli_fetch_assoc($obj);


m_log($resultArray['AccessToken'] ."  ". $_SESSION['token']);

if ($resultArray['AccessToken'] !== $_SESSION['token']) {
  # code...
  $isAuth = false;
}
else
{
  $isAuth = true;
}

m_log($_SESSION['shop']."-"."isAuth:".$isAuth);

//require 'config.php';
if(isAuthenticated() && $isAuth){
    m_log($_SESSION['shop']."-"."isAuthenticated: Redirecting to welcome page.");
        header('location:welcomeAboard.php');
    }
    $tradegecoClient = new TradeGecoClient(TRADEGECO_URL,  $_SESSION['token'], TRADEGECO_CLIENTID, TRADEGECO_SECRET);
    m_log($_SESSION['shop']."-"."is Not Authenticated.");
    
    $shopDetails = $tradegecoClient->call('GET','/accounts/current');

$shopId = $shopDetails['account']['id'];
$shopName = ucwords($shopDetails['account']['name']);

if($shopId == '' && $shopName == ''){
    m_log("Shop is not created yet. Redirecting user to tradegecko to create shop first");
    header('location:redirectToReverb.php');
}


?>

<!doctype html>

<html>
<head></head>
<body>
<div class="container page_login">
  <div class="col-md-6 col-sm-6 col-xs-6 logo col-md-offset-3">
    <div class="logo_div">
    <img src=http://www.eshipper.com/hs-fs/hubfs/assets/logo-small.png?t=1504044189408&width=120&height=120&name=logo-small.png>
    </div>
  <div class="form login_form">
    <div class="form-group">
      <input type="text" class="user-id" name="shop" size="45" type="text" value="" placeholder="User ID" id="eshipText" />
    </div>
    <div class="form-group">
      <input type="password" class="pass" size="45" type="text" value="" placeholder="PASSWORD" id="eshipPassword"/>
            <input type="hidden" class="pass" size="45" type="text" value="<?php echo $shopName; ?>" id="storeName"/>

      <input type="hidden" class="pass" size="45" type="text" value="<?php echo $shopId; ?>"  id="storeId"/>

    </div>
    <div class="form-group">
      <input name="commit" type="submit" value="LOGIN" id="eshipperAuth" />
    </div>
    <div><form action="registerForm.php"><a href="" data-toggle="modal" target="_parent" data-target="#myModal" class="sign_up_button">Forgot Password?</a>
      <button class="btn btn-info btn-lg sign_up_button">New User? Sign Up</button></form></div>
    
    <div class="msg">
      <p id="eshipperMsg"> </p>
      
    </div>
  </div>
</div>
</div>

<!-- Modal -->
<form id="registrationForm" method="">
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Forget Password Request</h4>
      </div>
      <div class="modal-body">
         <div class="form-group form_field row">
                <div class="col-sm-6 form_detail_div">  
                  <label class="control-label" for="username">Eshipper Username <span class="required">*</span></label>
                  <div class="input-group input_field_detail">
                    <span class="input-group-addon"><img src="assets/images/man-user.png" alt="user_icon"></span>
                    <input type="text" class="form-control" ata-required="true" name="username" value="">
                  </div>  
                </div>
                <div class="col-sm-6">  
                  <label class="control-label" for="regEmail"> Registered Email Address<span class="required">*</span></label>
                  <div class="input-group input_field_detail">
                    <span class="input-group-addon"><img src="assets/images/close-envelope.png" alt="user_icon"></span>
                    <input type="email" class="form-control" data-required="true" name="regEmail" id="regEmail" required="required">
                  </div>   
                </div>

                    
                </div>
                <div class="form-group form_field row">
                                  <div class="col-sm-12 form_detail_div">
                                    <div class="input-group input_field_detail">
                                      <p id="emailError" style="color:red"></p>
                                      <p id="sucessMail" style="color:red"></p>
                                    </div>

                                  </div>
                </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="forgetPassReq" data-dismiss="">Send request</button>
      </div>
    </div>

  </div>
</div>
</form>

<script src="https://d1g5417jjjo7sf.cloudfront.net/reverb-embedded-sdk.js"/>
  <script>ReverbEmbeddedSDK.init();</script>
<script type="text/javascript">
    $(document).ready(function()
         {
        $('input[name="commit"]').on('click', function(e){
              e.preventDefault();
      		  var dataArray=[];

       //$(this).parents('tr').find('td:nth-last-child(3) > input').val();
      		   
                          //code
                   
      //dataArray.push({orderId:orderId,name:OrderName,days:recurringDays});
      //$(that).parent().html('<span><img src="https://esoftappslive.com/test/order/img/waiting.gif" /></span>');
                       
      		  var uname   =$('#eshipText').val();
              var upass   =$('#eshipPassword').val();
              var sname   =$('#storeName').val();
              var sid      =$('#storeId').val();
              dataArray.push({uname:uname,upass:upass,storeId:sid,storeName:sname});	
      				
      //console.log(dataArray);
       $.ajax({
                url: 'auth.php',
                type: 'post',
                data: {uname:uname,upass:upass,storeId:sid,storeName:sname},
                success: function(r) {
       //            // Re-enable add to cart button.
           		   var m = JSON.parse(r);
            
        var data = m.msg;
        console.log(data);

        if (data.indexOf('verified') !==-1) {
           $('#eshipperMsg').removeClass('bg-danger').addClass('bg-success').html(data);
           location.reload();
          //location.href = "readOrders.php";
		  } else {
            $('#eshipperMsg').removeClass('bg-success').addClass('bg-danger').html(data);
          } 
        
           }, 
           error: function() {
           	//alert();
                        
           }
          });
		});

// http://www.andrewdavidson.com/articles/spinning-wait-icons/wait26.gif});
////////////////////////// registration javascript code /////////////////////////////////////////

$(document).on('change', '#checkout_shipping_address_country', function(){
var x = window.Countries;
var s ='';
var c = $('#checkout_shipping_address_country').val();
var e = $('#addProvinces');
if(c===''){return false;}
console.log(typeof x.Australia.provinces)
var p = x[c].provinces;
if(p){
Object.keys(p).forEach(function(key) {
     s += '<option>'+p[key]+'</option>';
});
if (s ==='') {  }
    e.find('option').remove().end().append(s).show();

}
else
{
  e.find('option').remove().end().hide();return false;
}


});
///////////////////////////////////////////////

$(document).on('click','#eshipperRegister', function(e){

e.preventDefault();
/////////////////////////////

      var c = 0;
$('.form-input').each(function(){


if($(this).attr('data-required')=='true')
{
  
if((!$(this).val()!='') || ($(this).val()=='-1'))
{
c++;

$(this).css('border','1px solid red');
}
else{
$(this).css('border','solid 1px #3fb4c1')
}
if($(this).attr('name')=='regCompanyName')
  {

    var sl = $(this).val();
    if(sl.length < 6)
    {
     
    $(this).css('border','1px solid red');
   
      //alert('sl '+c);

      $('#eshipperRegMsg').html('Company name must be atleast 6 characters long.');
      
    }
    else{
      $(this).css('border','solid 1px #3fb4c1')

      $('#eshipperRegMsg').html('');
      c=0;

    }
  }
}
});

if( c > 0){
$('#eshipperRegMsg').html('Please see the errors above');

return false;
}
 else{
      $('#eshipperRegMsg').html('');

    }

///////////////////////////////
  console.log( $( this ).find('form#registrationForm').serialize()  );
    //alert($('form#registrationForm').serialize());
     $.ajax({
             url: 'register.php',
             type: 'post',
             data: $('form#registrationForm').serialize(),
             success: function(r) {
       //           // Re-enable add to cart button.
                    
             var m = JSON.parse(r);
             var data = m.msg;
             var s = m.state;
             if (s=='true') {
              location.reload();
            } 

            else {
              alert(data);

            }
        
           }, 
           error: function() {
            //alert();
                        
           }
          });


});


/////////////////// forget password modal////////////////////////

$('#forgetPassReq').on('click', function(){
  var c = 0;
  $('.form-control').each(function(){

if($(this).val()=='')
{
c++;
$(this).css('border','solid 1px red');
}

  });
if (c> 0) {


$('#emailError').html('All fields are required');
return false;

};
$('#forgetPassReq').html('<img src="http://www.uclick2ride.com/wp-content/themes/Car-Dealer-UClick2Ride/images/common/loader.gif"/>');
$.ajax({
             url: 'sendmail.php',
             type: 'post',
             data: $('form#registrationForm').serialize(),
             success: function(r) {
              $('#forgetPassReq').text('Send Mail');
       //           // Re-enable add to cart button.
                    
             var m = JSON.parse(r);
             var s = m.state;
             var d = m.msg;
             if (s == 'success') {
              $('#emailError').html('');


                $('#sucessMail').html(d);
             }
             else if(s == 'failed'){
                $('#sucessMail').html('');
              $('#emailError').html(d);
            
             }
             else
             {
               $('#sucessMail').html('');
              $('#emailError').html('Unable to send request to reset password.');
             


             }      
        
           }, 
           error: function() {
            //alert();
                        $('#forgetPassReq').text('Send Mail');
           }
          });

})


////////////////////////////////////////////////////////////////////////////////////////////////
	});
	 </script>
</body>
</html>