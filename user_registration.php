<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(0);

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
    {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");  
    }
               

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
    {
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }        
}

require_once 'functions.php';
$db=login();

$json = file_get_contents('php://input');
$destination="logs/skappuserregistration".date('Y-m-d').".log";
error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $json ",3, $destination);
$obj = json_decode($json);

$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});
$full_name = mysqli_real_escape_string($db,$obj->{'full_name'});
$region = mysqli_real_escape_string($db,$obj->{'region'});
$email = mysqli_real_escape_string($db,$obj->{'email'});
$phone_number = mysqli_real_escape_string($db,$obj->{'phone_number'});
$gender = mysqli_real_escape_string($db,$obj->{'jinsia'});
$data = array();

$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if($api_key==$auth_key)
{   
     $statement="select * from tbl_device where phone_number like '%$phone_number%'";
     $result=mysqli_query($db,$statement);
     
      
     $rowcount=mysqli_num_rows($result);
     
     if($rowcount == 0)
     {
        $sql="INSERT INTO tbl_device(`full_name`,`phone_number`,`gender`,`mkoa`,`user_email`) VALUES('$full_name','$phone_number','$gender','$region','$email')";
        if(mysqli_query($db,$sql))
        {
        	 $response=array(
                "status"=>"201",
                "statusDesc"=>"Success"                   
            );	
        }
        else
        {
        	 $response=array(
                "status"=>"422",
                "statusDesc"=>"User Registartion Failed Please Contact Administrator"                   
            );
        }
     }
     else
     {
            $response=array(
                "status"=>"201",
                "statusDesc"=>"Success"                   
            );	 
     }
 
}
else 
{
    $response=array(
        "status"=>"403",
        "statusDesc"=>"Invalid API KEY"        
    );

}

$response=json_encode($response);
error_log("\n".date('Y-m-d H:i:s')." Outgoing Response :\n $response ",3, $destination);
header('Content-Type: application/json; charset=utf-8');
print_r($response);
die();   



?>
