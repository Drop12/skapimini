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
$destination="logs/skappmakala".date('Y-m-d').".log";
error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $json ",3, $destination);
$obj = json_decode($json);

$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});


$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if($api_key==$auth_key)
{   
    
    $data=array();
    $sql="SELECT * FROM tbl_busines_formalization order by id asc";
    $result_set=mysqli_query($db,$sql);
    while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC))
    { 
    
    	$data[]=array(
    			 "service_type"=>$row['service_type'],
    			 "registration_requirements"=>$row['registration_requirements'],
    			 "professional_fees"=>$row['professional_fees'],
    			 "tra_fees"=>$row['tra_fees'],
    			 "brella_fees"=>get_registration_fee($db,$row['id']),
    			 "municipal_fees"=>$row['municipal_fees']
    			);
    	
    }
    
    $response=array(
			 "status"=>"200",
			 "statusDesc"=>"success",
			 "utangulizi"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum",
			"data"=>$data	
			);
 
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
