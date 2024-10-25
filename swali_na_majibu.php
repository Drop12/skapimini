<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(0);


if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');   // cache for 1 day
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
$destination="logs/skappmaswalinamajibu".date('Y-m-d').".log";
error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $json ",3, $destination);
$obj = json_decode($json);

$api_key = mysqli_real_escape_string($db,$obj->{'api_key'});
$mada = mysqli_real_escape_string($db,$obj->{'mada_id'});

$auth_key="YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if($api_key==$auth_key)
{   
    
$data=array();


/*if($mada=="COVID-19 Na Ardhi")
{
    $sql="SELECT * FROM tbl_content where categorylike '%$mada%' Limit 9";
}
else
{
    $sql="SELECT * FROM tbl_content where category like '%$mada%' Limit 101 ";
}*/

$sql="SELECT * FROM tbl_content where category='$mada' order by remarks asc Limit 101";


//die($sql);

//error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $sql ",3, $destination);

$result_set=mysqli_query($db,$sql);
while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC))
{ 


	$swali=$row['ref_key'];
	$jibu=$row['remarks'];
	//$jibu=escapeJsonString($jibu);
	
	$swali=addslashes($swali);
	$jibu=addslashes($jibu);
	
	
	$data[]=array(
			 "swali"=>$swali,
			 "jibu"=>$jibu
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
