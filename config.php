<?php

require_once 'properties/application.properties.php';

if ($profile == 'test'){
	
    $_USER_NAME='stageplugins';
    $_HOST     ='172.16.0.126';
    $_PASSWORD = 'stageplugins';
    $_DATABASE = 'stageplugins';       
    
}else if($profile == 'prod' || $profile == 'web03'){
    //production DB
    $_USER_NAME='plugins';
    $_HOST     ='172.16.0.100';
    $_PASSWORD = 'plugins';
    $_DATABASE = 'plugins';   
}else if($profile == 'prod2test'){
    $_USER_NAME='plugins';
    $_HOST     ='172.16.0.100';
    $_PASSWORD = 'plugins';
    $_DATABASE = 'plugins';
    
}

$con=mysqli_connect($_HOST, $_USER_NAME,$_PASSWORD,$_DATABASE);

if(!$con)
   {
    echo " Database Connection failed";
    
   }
?>
