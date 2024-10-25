<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(0);

function login()
{
     /* Database credentials. Assuming you are running MySQL
    server with default setting (user 'root' with no password) */
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'bluefins_admin');
    define('DB_PASSWORD', 'I$,ANGlSTDIG');
    define('DB_NAME', 'bluefins_blue');

    /* Attempt to connect to MySQL database */
    $db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($db === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    return $db;
}
function logintoken()
{
    /* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVERtk', 'localhost');
define('DB_USERNAMEtk', 'sheriakiganjani_sheria');
define('DB_PASSWORDtk', 'pqa0}j^QoDWu');
define('DB_NAMEtk', 'sheriakiganjani_sheria');
 
/* Attempt to connect to MySQL database */
$db = mysqli_connect(DB_SERVERtk, DB_USERNAMEtk, DB_PASSWORDtk, DB_NAMEtk);
 
// Check connection
if($db === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

return $db;
}
function insert_extra_text($db,$msisdn,$maelezo,$mhuri,$barua_pepe,$doc_name)
{
    $sql="INSERT INTO tbl_pay_detailsf(phone_number,maelezo,mhuri,barua_pepe,doc_name)
    VALUES('$msisdn','$maelezo','$mhuri','$barua_pepe','$doc_name')";

    $result=mysqli_query($db,$sql);    
    if(!$result)
    {
        echo $sql;
        die();
    }
    
    return;
}
function get_token($db,$msisdn)
{
    $FiveDigitRandomNumber = rand(10000,99999);
    
    $sql="INSERT INTO `tbl_token`(`phone_number`,`token`)VALUES('$msisdn','$FiveDigitRandomNumber')";
    if(!mysqli_query($db, $sql))
    {
        die($sql);
    }
    
    return $FiveDigitRandomNumber;
}
function validate_token($db,$msisdn,$otp_token)
{
     $statement="select * from tbl_token where `phone_number`='$msisdn' AND `token`='$otp_token' AND `isverified`='0'";
     $result=mysqli_query($db,$statement);
     
      
     $rowcount=mysqli_num_rows($result);
     
     if($rowcount == 1)
     {
     	$sqlupdate="UPDATE tbl_token SET `isverified`='1' where `phone_number`='$msisdn' AND `token`='$otp_token'";
     	mysqli_query($db,$sqlupdate);
     	
     	return true;
     }
     else
     {
     	return false;
     }
}
function initiate_txn($account_id,$phone,$sender,$sms,$reference_id,$isSuccessful,$responsee,$db)
{
    date_default_timezone_set('Africa/Dar_es_Salaam');
    $d_date = date('Y-m-d H:i:s');
    $count_sms = strlen($sms);
    $mazima = floor($count_sms / 160);
    $modulus = $count_sms % 160;

    if ($modulus > 0) {
        $sms_count = $mazima + 1;
    }

    $balance_before=get_balance($db);
    $balance_after=$balance_before - $sms_count;
    update_smsvolume($balance_after, $db);
    $sql = "INSERT INTO `tbl_smsledger`(`submited_response`,`sms_unique_id`,`sms_count_after`,`sms_count_before`,`account_id`, `senderid`, `phonenumber`,`textsent`,`textcharacters`,`sms_status`,`sms_date`,`group_id`,`sms_stage`,`sms_counntused`)
               VALUES ('$responsee','$reference_id','$balance_after','$balance_before','$account_id','$sender','$phone','$sms','$count_sms','submitted','$d_date','Single SMS','submitted','$sms_count')";
   if(!mysqli_query($db, $sql))
   {
        die($sql);
   }
   
   return;
}
function Validate_sent_tokentt($db,$msisdn)
{
    $sql="Select * FROM tbl_token where `phone_number`='$msisdn' AND `isverified`=0";
    $result_set=mysqli_query($db,$sql);
    $count = mysqli_num_rows($result_set);

    if ($count == 1) {
        return false;
    }
    else
    {
        return true;
    }
}
function send_sms($channel,$text,$msisdn,$db,$destination)
{
    $text = trim($text);
    $data[] = array(
        "text" => $text,
        "msisdn" => $msisdn,
        "source" => "IJUE SHERIA"
    );

    $req = array(
        "channel" => array(
            "channel" => "119234",
            "password" => "YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlNTc4ZjU3N2QwMDVmMw=="
        ),
        "messages" => $data
    );
    

    $kkt = json_encode($req,JSON_UNESCAPED_UNICODE); 
    
    @error_log("\n" . date('Y-m-d H:i:s') . " Sent Request :\n $kkt ", 3, $destination);

    $curl = curl_init();

    $post_url = "https://bulksms.fasthub.co.tz/fasthub/messaging/json/api";    
    curl_setopt_array($curl, array(
        CURLOPT_URL => $post_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $kkt,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    //print_r($kkt);
    //die();
    @error_log("\n" . date('Y-m-d H:i:s') . " Returned Response :\n $response ", 3, $destination);

    if ($response === FALSE) {
        die();
      
    } else {
        $responsee = json_decode($response, true);
        $isSuccessful = $responsee['isSuccessful'];        
        $reference_id = $responsee['reference_id'];        

        if ($isSuccessful == "true") {            
            initiate_txn("6319885",$msisdn,"IJUE SHERIA",$text,$reference_id,$isSuccessful,$response,$db);
        }
    }
    
    curl_close($curl);    
}
function validate_phonenumber($phone_number)
{
    $pattern = '/^[0-9]*$/';
    if ((strlen($phone_number) == 12) && preg_match($pattern, $phone_number)) 
    {
       return TRUE;
    }
    else
    {
        return FALSE;
    }   
}
function get_balance($db)
{
    $sql = "SELECT `sms_count` from tbl_balance  where account_id='6319885'";
    $result_set = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($result_set, MYSQLI_ASSOC)) {
        $sms_count = $row['sms_count'];
    }
    return $sms_count;
}
function check_balance($sms_counntused,$account_id,$db)
{
    $sql = "SELECT `sms_count` from tbl_balance  where account_id='$account_id'";
    $result_set = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($result_set, MYSQLI_ASSOC)) {
        $scount = $row['sms_count'];
    }
    if ($scount >= $sms_counntused) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function update_smsvolume($balance_after, $db)
{
    $sql = "UPDATE tbl_balance SET `sms_count`='$balance_after'  where account_id='6319885'";
    if(!mysqli_query($db, $sql))
    {
        die($sql);
    }
}
function Process_confirm($sms_unique_id, $id, $db)
{
    $sms_unique_id = preg_replace('/\s+/', '', $sms_unique_id);

    $request = array(
        "channel" => "119234",
        "reference_id" => $sms_unique_id
    );


    $request = json_encode($request);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://secure-gw.fasthub.co.tz/api/dlr/request/polling/handler",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $request,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));

    //http://162.144.65.205/

    $response = curl_exec($curl);

    if ($response === FALSE) {
        die();
        //echo "cURL Error: " . curl_error($curl);
    } else {
        $responseee = json_decode($response, true);
        $status = $responseee[0]['status'];

        $response = strval($response);
        update_statusconfirmed($status, $id, $response, $db);
    }
    curl_close($curl);
    
    return $response;
}
function update_statusconfirmed($status, $id, $responsee, $db)
{

    if ($status == "D") {
        $status = "Delivered";
    } elseif ($status == "E") {
        $status = "Expired";
    } elseif ($status == "F") {
        $status = "Failed";
    } elseif ($status == "R") {
        $status = "Rejected";
    }

    $sql = "UPDATE tbl_smsledger SET sms_status='$status',sms_stage='$status',is_confirmed=1,confirmed_response='$responsee' where id=$id";
    mysqli_query($db, $sql);

    return;
}
