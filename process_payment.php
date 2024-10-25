<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(0);

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
$input=file_get_contents('php://input');
$data = json_decode($input, true);

$destination="logs/payment".date('Y-m-d').".log";
error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $input ",3, $destination);

require_once 'functions.php';
$db=login();
//$dbsms=loginsms();

$amount = doubleval($data['amount']);
$service = $data['service'];
$phone = $data['phone_number'];
$api_key = $data['api_key'];
$duration = $data['duration'];
$duration=get_duration($duration);

if($duration=='DAY')
{
    $duration='1';
}
elseif($duration=='WEEK')
{
    $duration='7';
}
elseif($duration=='MONTH')
{
    $duration='31';
}


//print_r($data);
//die();

$auth_key ="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if ($api_key == $auth_key) {

date_default_timezone_set("Africa/Dar_es_Salaam");
$date = date("Y-m-d h:i");
$expires_at= date("Y-m-d h:i",strtotime("+$duration days"));
$sql="INSERT INTO `tbl_payments`(`service`, `amount`, `phone_number`, `duration`, `expires_at`,`status`,`order_id`,`trans_id`) VALUES ('$service','$amount','$phone','$duration','$expires_at','APP_RECEIVED','0','0')";
if(mysqli_query($db,$sql))
{
    /*$resp = createOrder($amount,$phone);
    if($resp['code'] == 200 && $resp['result']['data']['resultcode'] == "000") {
        $order_id= $resp['result']['order_id']; */
        $resp_pay = pay($amount,$phone,$destination); // push ussd
        if($resp_pay['success']) {
            $trans_id = $resp_pay['transactionId']; // success
            $sql="UPDATE `tbl_payments` SET `order_id`=$trans_id,`trans_id`=$trans_id,`status`='PENDING' WHERE `phone_number`=$phone AND `service`= '$service' ORDER BY id DESC LIMIT 1";
            if(mysqli_query($db,$sql)){
                $response=array(
                    "status"=>"200",
                    "message"=>"success",
                    "result"=>true
                );
            } else {
                $response=array(
                    "status"=>"422",
                    "message"=>"Failure",
                    "result"=>mysqli_error($db),
                );
            }

        }else{
            $response=array(
                "status"=>"422",
                "message"=>"Failure",
                "result"=>false
            );
        }
    /*} else {
        $response=array(
            "status"=>"400",
            "message"=>"Failure",
            "result"=>false
        );
    }
    */

    $response=json_encode($response);
}
else
{
    $response=array(
        "status"=>"422",
        "message"=>$db->error,
        "result"=>false
    );
    $response=json_encode($response);
}
} else {
    $response = [
        "status" => "403",
        "statusDesc" => "Invalid API KEY",
    ];
}

error_log("\n".date('Y-m-d H:i:s')." Response :\n $response ",3, $destination);
header('Content-Type: application/json; charset=utf-8');
print_r($response);
die();
