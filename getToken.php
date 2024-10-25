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
$destination = "logs/skaptoken" . date('Y-m-d') . ".log";

$json = file_get_contents('php://input');
$obj = json_decode($json);

@error_log("\n" . date('Y-m-d H:i:s') . " Incoming Request :\n $json ", 3, $destination);
$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});
$msisdn = mysqli_real_escape_string($db,$obj->{'msisdn'});


$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";
if($api_key==$auth_key)
{
    
    if(validate_phonenumber($msisdn))
    {
        $smsthreshold="3";
        $account_id="6319885";
        $channel="6319885";
        if(check_balance($smsthreshold,$account_id,$dbsms))
        {
            
                $text=get_token($db,$msisdn);
                send_sms($channel,$text,$msisdn,$dbsms,$destination);
           
            
            
            
            $response=array(
                    "status"=>"200",
                    "statusDesc"=>"success"
                );
        }
        else
        {
            $response=array(
            "status"=>"422",
            "statusDesc"=>"Innsuficient SMS Balance"
                );
        }
         
    }
    else
    {
        $response=array(
            "status"=>"422",
            "statusDesc"=>"Invalid Phone Number"
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
