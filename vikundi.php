<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

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
$group_name = mysqli_real_escape_string($db,$obj->{'group_name'});
$group_objectives = mysqli_real_escape_string($db,$obj->{'group_objectives'});
$address = $obj->{'address'};
//$constitution = $obj->{'constitution'};
$registration_ticket = mysqli_real_escape_string($db,$obj->{'registration_ticket'});
$members = $obj->{'members'};
$latitude = mysqli_real_escape_string($db,$obj->{'address'});
$longitude = mysqli_real_escape_string($db,$obj->{'address'});


//var_dump($address_of_business);
//die($latitude);

$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";
if($api_key==$auth_key)
{
    
if($constitution = $obj->{'constitution'})
{
    $constitution = $obj->{'constitution'};
    $name =  time().$registration_ticket.'.pdf';
 
    $realImage = base64_decode($constitution);
    
    $dir_to_save = "uploads/";
    
    file_put_contents($dir_to_save.$name, $realImage);   
}
else
{
    $name = $obj->{'constitution_url'};
}

 $sql="INSERT INTO `tbl_vikundi`(`group_name`,`group_objectives`,`latitude`,`longitude`,`constitution`,`registration_ticket`) VALUES ('$group_name','$group_objectives','$latitude','$longitude','$name','$registration_ticket')";
 if(mysqli_query($db,$sql))
 {
    foreach ($members as $shareholder) {
        $id = $shareholder->id;
        $full_name = $shareholder->full_name;
        $phone_number = $shareholder->phone_number;
        $position = $shareholder->position;
        $identification_number = $shareholder->identification_number;
        
        $sql="INSERT INTO `tbl_vikundi_members`(`id_founder`,`full_name`,`phone_number`,`position`,`identification_number`,`registration_ticket`) VALUES ('$id','$full_name','$phone_number','$position','$identification_number','$registration_ticket')";
        if(!mysqli_query($db,$sql))
         {
             die($sql);
         }
    }
     
    $response=array(
        "status"=>"201",
        "statusDesc"=>"success"
    );
    
 }
 else
 {
    $response=array(
        "status"=>"422",
        "statusDesc"=>"Advocate registration Failed, please try again"
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
