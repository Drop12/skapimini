<?php
/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(0);*/

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(0);

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
$input=file_get_contents('php://input');
$data = json_decode($input, true);

$destination="logs/minipayment".date('Y-m-d').".log";
error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $input ",3, $destination);

require_once 'functions.php';
$db=login();
//$dbsms=loginsms();

$amount = doubleval($data['amount']);
$txn_date = $data['txn_date'];
$phone = $data['phone_number'];
$api_key = $data['api_key'];
$duration = $data['duration'];
$txn_id=$data['txn_id'];
$txn_date=$data['txn_date'];

//print_r($data);
//die();
$auth_key ="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if ($api_key == $auth_key) {

date_default_timezone_set("Africa/Dar_es_Salaam");
$date = date("Y-m-d h:i");
$expires_at= date("Y-m-d h:i",strtotime("+$duration days"));
$sql="INSERT INTO `tbl_payments`(`service`, `amount`, `phone_number`, `duration`, `expires_at`,`status`,`order_id`,`trans_id`,`from_p`) 
VALUES ('Kuwasiliana','$amount','$phone','$duration','$expires_at','SUCCESS','$txn_id','$txn_id','MiniApp')";
if(mysqli_query($db,$sql))
{
    $response=array(
        "status"=>"200",
        "statusDesc"=>"Success"
    );   
    $response=json_encode($response);
}
else
{
    $response=array(
        "status"=>"422",
        "statusDesc"=>"Failed To Inser MiniAPP Payment"
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
