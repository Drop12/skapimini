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
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }        
}

require_once 'smsfunctions.php';
$dbsms=login();
$db=logintoken();
$destination = "logs/soleproprietor" . date('Y-m-d') . ".log";

$json = file_get_contents('php://input');
$obj = json_decode($json);

//print_r($obj);
//die();
@error_log("\n" . date('Y-m-d H:i:s') . " Incoming Request :\n $json ", 3, $destination);
$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});
$business_name = mysqli_real_escape_string($db,$obj->{'business_name'});
$nature_of_business = mysqli_real_escape_string($db,$obj->{'nature_of_business'});
$address_of_business = $obj->{'address_of_business'};
$name_of_proprietor = mysqli_real_escape_string($db,$obj->{'name_of_proprietor'});
$phone_number = mysqli_real_escape_string($db,$obj->{'phone_number'});
$identification = $obj->{'identification'};
$registration_ticket = mysqli_real_escape_string($db,$obj->{'registration_ticket'});
$latitude = mysqli_real_escape_string($db,$obj->{'address_of_business'});
$longitude = mysqli_real_escape_string($db,$obj->{'address_of_business'});


//var_dump($address_of_business);
//die($latitude);

$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";
if($api_key==$auth_key)
{
    
 $name =  time().$phone_number.'.pdf';
 
 $realImage = base64_decode($identification);

 $dir_to_save = "uploads/";

 file_put_contents($dir_to_save.$name, $realImage);

 $sql="INSERT INTO `tbl_soleproprietor`(`business_name`,`nature_of_business`,`latitude`,`longitude`,`name_of_proprietor`,`phone_number`,`registration_ticket`,`identification`) VALUES ('$business_name','$nature_of_business','$latitude','$longitude','$name_of_proprietor','$phone_number','$registration_ticket','$name')";
 //die($sql);
 if(mysqli_query($db,$sql))
 {
    $response=array(
        "status"=>"201",
        "statusDesc"=>"success"
    );
    
 }
 else
 {
    $response=array(
        "status"=>"422",
        "statusDesc"=>"Registration Failed, please try again",
        "sql"=>$sql
    );
    
 }
}
else 
{
    $response=array(
        "status"=>"403",
        "statusDesc"=>"Invalid Password OR Channel"        
    );

}
$response=json_encode($response);
@error_log("\n" . date('Y-m-d H:i:s') . " Response :\n $response ", 3, $destination);
header('Content-Type: application/json; charset=utf-8');
print_r($response);
die();
?>
