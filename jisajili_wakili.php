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
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }        
}

require_once 'functions.php';
$db=login();

$json = file_get_contents('php://input');
$destination="logs/skappjisajiliwakili".date('Y-m-d').".log";
error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $json ",3, $destination);
$obj = json_decode($json);

$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});
$jina = mysqli_real_escape_string($db,$obj->{'full_name'});
$uwakili = mysqli_real_escape_string($db,$obj->{'namba_ya_uwakili'});
$simu = mysqli_real_escape_string($db,$obj->{'namba_ya_simu'});
$anuani = mysqli_real_escape_string($db,$obj->{'anuani'});
$jinsia = mysqli_real_escape_string($db,$obj->{'jinsia'});
$image = mysqli_real_escape_string($db,$obj->{'picha'});
$lat = mysqli_real_escape_string($db,$obj->{'lat'});
$long = mysqli_real_escape_string($db,$obj->{'long'});
$dibaji = mysqli_real_escape_string($db,$obj->{'eneo_bobezi'});
$registration_ticket = mysqli_real_escape_string($db,$obj->{'registration_ticket'});

$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if($api_key==$auth_key)
{   
  
 $name =  time().$simu.'.png';
 
$realImage = base64_decode($image);

$dir_to_save = "uploads/";

    if(file_put_contents($dir_to_save.$name, $realImage))
    {
        if(validate_roll_number($db,$uwakili))
        {
            $sql="INSERT INTO `tbl_wakiliramani`(`registration_ticket`,`image_name`,`jina_kamili`,`n_uwakili`,`phone_num`,`anuani`,`dibaji`,`jinsia`,`lat`,`p_long`) VALUES ('$registration_ticket','$name','$jina','$uwakili','$simu','$anuani','$dibaji','$jinsia','$lat','$long')";
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
                    "statusDesc"=>"Advocate registration Failed, please try again"
                );
                
            }
        }
        else
        {
            $response=array(
                    "status"=>"423",
                    "statusDesc"=>"Duplicate Entry"
                );
        }
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
