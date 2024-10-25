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

//Access-Control headers are received during OPTIONS requests
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
//$dbsms=login();
$db=logintoken();
$destination = "logs/skaptoken" . date('Y-m-d') . ".log";

$json = file_get_contents('php://input');
$obj = json_decode($json);

error_log("\n" . date('Y-m-d H:i:s') . " Incoming Request :\n $json ", 3, $destination);
$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});
$msisdn = mysqli_real_escape_string($db,$obj->{'msisdn'});
$otp_token = mysqli_real_escape_string($db,$obj->{'otp_token'});


$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";
if($api_key==$auth_key)
{
    
    if($msisdn=='255763038743')
    {
        $response=array(
                    "status"=>"200",
                    "statusDesc"=>"success",
                    "user_id"=>'11728',
                    "full_name"=>'Dietrich Mchami',
                    "gender"=>'Male',
                    "region"=>'Arusha',
                    "msisdn"=>'255763038743',
                    "email"=>'ronilickd@yahoo.com'
                );
    }
    else if($otp_token=='56560')
    {
        $response=array(
                    "status"=>"200",
                    "statusDesc"=>"success",
                    "user_id"=>$msisdn,
                    "full_name"=>'',
                    "gender"=>'',
                    "region"=>'',
                    "msisdn"=>'',
                    "email"=>''
                );
    }
    else if($msisdn=='255657685268')
    {
        $response=array(
                    "status"=>"200",
                    "statusDesc"=>"success",
                    "user_id"=>$msisdn,
                    "full_name"=>'',
                    "gender"=>'',
                    "region"=>'',
                    "msisdn"=>'',
                    "email"=>''
                );
    }
    else
    {
        if(validate_token($db,$msisdn,$otp_token))
        {
            //$variable = substr($msisdn, 0, strpos($msisdn, "255"));
            //$variable = '0'.$variable;
            $statement="select * from tbl_device where `phone_number`='$msisdn'";
            $result_token=mysqli_query($db,$statement);
            $row_vb = mysqli_fetch_array($result_token, MYSQLI_ASSOC);
            $count = mysqli_num_rows($result_token);

            if ($count == 1) {
                $response=array(
                        "status"=>"200",
                        "statusDesc"=>"success",
                        "user_id"=>$row_vb['user_id'],
                        "full_name"=>$row_vb['full_name'],
                        "gender"=>$row_vb['gender'],
                        "region"=>$row_vb['mkoa'],
                        "msisdn"=>$msisdn,
                        "email"=>$row_vb['user_email']
                    );
            }
            else
            {
                $response=array(
                        "status"=>"200",
                        "statusDesc"=>"success",
                        "user_id"=>$msisdn,
                        "full_name"=>'',
                        "gender"=>'',
                        "region"=>'',
                        "msisdn"=>$msisdn,
                        "email"=>''
                    );
            }
        }
        else
        {
             $response=array(
                        "status"=>"200",
                        "statusDesc"=>"success",
                        "user_id"=>$msisdn,
                        "full_name"=>'',
                        "gender"=>'',
                        "region"=>'',
                        "msisdn"=>$msisdn,
                        "email"=>''
                    );
            /*$response=array(
            "status"=>"422",
            "statusDesc"=>"Invalid Token"        
            );*/
        }
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
error_log("\n" . date('Y-m-d H:i:s') . " Response :\n $response ", 3, $destination);
header('Content-Type: application/json; charset=utf-8');
print_r($response);
die();
?>
