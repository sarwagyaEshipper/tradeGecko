<?php


$ecomUsername = 'fivebyseven11459_temp';
$ecomPassword ='YW?R9yd*@mb6JXZf';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://clbeta.ecomexpress.in/apiv2/fetch_awb/");
// curl_setopt($ch, CURLOPT_POST, 1);

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));
$headers = [
    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',   
    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
    ];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);
 curl_setopt($ch, CURLOPT_POSTFIELDS,'username=fivebyseven11459_temp&password=YW?R9yd*@mb6JXZf&type=ppd&count=1');

curl_setopt($ch, CURLOPT_VERBOSE,true);

$data = array(
    'username' => $ecomUsername,
    'password' => $ecomPassword,
    'type' => 'ppd',
    'count'=>'1'
);
//echo http_build_query($data);

//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
 $output = curl_exec($ch);
 var_dump($output);
if (curl_error($ch)) {
    $error_msg = curl_error($ch);
    echo "<pre>";
    var_dump($error_msg);
}
// $info = curl_getinfo($ch);
// var_dump($info);

// curl_close($ch);


die();
$payload ='{
    "reference_id": 581473,
    "success": "yes",
    "awb": [
        107589195
    ]
}';

print_r(json_decode($a));
$awbResult = json_decode($payload);
print_r($awbResult);
echo $r = $awbResult->success;
//we get awb, save in DB & send shipment to Ecom Express
if ($r=='yes') {
	# code...DB CODE here
	//{{}}
	#fetch awb value from response
	echo $awb = $awbResult->awb[0];

}
echo "<pre>";


$x=array(
0=>
array(
"AWB_NUMBER"=> "107589188",
"ORDER_NUMBER"=> "107249072-001",
"PRODUCT"=> "PPD",
"CONSIGNEE"=> "Test API User",
"CONSIGNEE_ADDRESS1"=> "H. No. A10",
"CONSIGNEE_ADDRESS2"=> "Block-T",
"CONSIGNEE_ADDRESS3"=> "Sector 39 Test",
"DESTINATION_CITY"=> "GURGAON",
"PINCODE"=> "111111",
"STATE"=> "DL",
"MOBILE"=> "1111111111",
"TELEPHONE"=> "0123456789",
"ITEM_DESCRIPTION"=> "Kids Bicycle",
"PIECES"=> 1,
"COLLECTABLE_VALUE"=> 0,
"DECLARED_VALUE"=> 1000.0,
"ACTUAL_WEIGHT"=> 0.5,
"VOLUMETRIC_WEIGHT"=> 0,
"LENGTH"=> 12,
"BREADTH"=> 5,
"HEIGHT"=> 2,
"PICKUP_NAME"=> "Pickup Name 1",
"PICKUP_ADDRESS_LINE1"=> "Pickup Addr 1 Changed",
"PICKUP_ADDRESS_LINE2"=> "Pickup Addr 2 Changed",
"PICKUP_PINCODE"=> "111111",
"PICKUP_PHONE"=> "0123456789",
"PICKUP_MOBILE"=> "1234567891",
"RETURN_NAME"=> "Test Return Name 1",
"RETURN_ADDRESS_LINE1"=> "Test Return Addr 1 Changed",
"RETURN_ADDRESS_LINE2"=> "Test Return Addr 2 Changed",
"RETURN_PINCODE"=> "111111",
"RETURN_PHONE"=> "1111111111",
"RETURN_MOBILE"=> "0123456789",
"ADDONSERVICE"=> [""],
"DG_SHIPMENT"=> "false",
"ADDITIONAL_INFORMATION"=> array(
"DELIVERY_TYPE"=> "",
"SELLER_TIN"=> "SELLER_TIN_1234",
"INVOICE_NUMBER"=> "INVOICE_1234",
"INVOICE_DATE"=> "09-06-2018",
"ESUGAM_NUMBER"=> "eSUGAM_1234",
"ITEM_CATEGORY"=> "ELECTRONICS",
"PACKING_TYPE"=> "Box",
"PICKUP_TYPE"=> "WH",
"RETURN_TYPE"=> "WH",
"CONSIGNEE_ADDRESS_TYPE"=> "WH",
"PICKUP_LOCATION_CODE"=> "PICKUP_ADDR_002",
"SELLER_GSTIN"=> "GISTN988787",
"GST_HSN"=> "123456",
"GST_ERN"=> "123456789123",
"GST_TAX_NAME"=> "DELHI GST",
"GST_TAX_BASE"=> 900.0,
"DISCOUNT"=> 0.0,
"GST_TAX_RATE_CGSTN"=> 5.0,
"GST_TAX_RATE_SGSTN"=> 5.0,
"GST_TAX_RATE_IGSTN"=> 0.0,
"GST_TAX_TOTAL"=> 100.0,
"GST_TAX_CGSTN"=> 50.0,
"GST_TAX_SGSTN"=> 50.0,
"GST_TAX_IGSTN"=> 0.0
)
)
);


print_r($x);
echo "<br>";
echo json_encode($x);


?>