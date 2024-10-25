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
$destination="logs/skappjisajiliwakili".date('Y-m-d').".log";
error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $json ",3, $destination);
$obj = json_decode($json);

$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});
$foram_title = mysqli_real_escape_string($db,$obj->{'forum_title'});
$phone_number = mysqli_real_escape_string($db,$obj->{'phone_number'});
$foram_description=mysqli_real_escape_string($db,$obj->{'foram_description'});

$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if($api_key==$auth_key)
{   

$sql="INSERT INTO `tbl_forum`(`foram_title`,`phone_number`,`foram_description`) VALUES ('$foram_title','$phone_number','$foram_description')";
if(mysqli_query($db,$sql))
{
    $data=array();
    
    $sql="SELECT * FROM tbl_forum order by reg_date desc";
    
    $result_set=mysqli_query($db,$sql);
    while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC))
    { 
    
    	$forum_id=$row['forum_id'];
    	$foram_title=$row['foram_title'];
    	$foram_description=$row['foram_description'];
    	$reg_date=$row['reg_date'];
    	
    	$data[]=array(
    			 "forum_id"=>$forum_id,
    			 "forum_title"=>$foram_title,
    			 "forum_description"=>$foram_description,
    			 "created_at"=>$reg_date
    			);
    	
    }
    
    $response=array(
			 "status"=>"200",
			 "statusDesc"=>"success",
			"data"=>$data	
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
        "statusDesc"=>"Invalid API KEY"        
    );

}

$response=json_encode($response);
error_log("\n".date('Y-m-d H:i:s')." Outgoing Response :\n $response ",3, $destination);
header('Content-Type: application/json; charset=utf-8');
print_r($response);
die();   



?>
