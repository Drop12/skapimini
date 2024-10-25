<?php

function login()
{
    /* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', 'admin@4321');
define('DB_NAME', 'skminiapp');
 
/* Attempt to connect to MySQL database */
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($db === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

return $db;
}
function escapeJsonString($value) {
    # list from www.json.org: (\b backspace, \f formfeed)    
    $escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
}
function get_duration($duration)
{
    if($duration=='DAY')
    {
        return 1;
    }
    elseif($duration=='WEEKLY')
    {
        return 7;
    }
    elseif($duration=='MONTHLY')
    {
        return 31;
    }
    elseif($duration=='TERMINAL')
    {
        return 186;
    }
    elseif($duration=='YEARLY')
    {
        return 365;
    }
    else
    {
        return $duration;
    }
}
function get_token()
{
    $FiveDigitRandomNumber = rand(10000,99999);
    return $FiveDigitRandomNumber;
}
function get_registration_fee($db,$id)
{
    $xx=1;
    $d=0;
    
    $sql="SELECT fee_range,registartion_fee FROM  tbl_busines_formalization_fees where `category`='$id' order by id ASC";
    $result_set=mysqli_query($db,$sql);
    $rowcount=mysqli_num_rows($result_set);
    while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC)) 
    { 
        $range=get_range_name($xx);
        
        if($id==1)
        {
            $data = array(
            'FIRST_RANGE' => array(
                    'range' => get_range_data($db,"1"),
                    'registration_fee' => get_range_fee_data($db,"1")
                ),
                'SECOND_RANGE' => array(
                    'range' => get_range_data($db,"2"),
                    'registration_fee' => get_range_fee_data($db,"2")
                ),
                'THIRD_RANGE' => array(
                    'range' => get_range_data($db,"3"),
                    'registration_fee' => get_range_fee_data($db,"3")
                ),
                'FOURTH_RANGE' => array(
                    'range' => get_range_data($db,"4"),
                    'registration_fee' => get_range_fee_data($db,"4")
                )
            );
        }
        elseif($id==4)
        {
             $data = array(
            'FIRST_RANGE' => array(
                    'range' => get_range_data($db,"7"),
                    'registration_fee' => get_range_fee_data($db,"7")
                ),
                'SECOND_RANGE' => array(
                    'range' => get_range_data($db,"8"),
                    'registration_fee' => get_range_fee_data($db,"8")
                ),
                'THIRD_RANGE' => array(
                    'range' => get_range_data($db,"9"),
                    'registration_fee' => get_range_fee_data($db,"9")
                ),
                'FOURTH_RANGE' => array(
                    'range' => get_range_data($db,"10"),
                    'registration_fee' => get_range_fee_data($db,"10")
                )
            );
        }
        else
        {
            $data=array($range=> array(
    			 "range"=>$row['fee_range'],
    			 "registration_fee"=>$row['registartion_fee']
    			));
        }
        
        $d++;
        $xx++;
    }
    
    return $data;
}
function get_range_name($xx)
{
    if($xx==1)
    {
        return 'FIRST_RANGE';
    }
    elseif($xx==2)
    {
        return 'SECOND_RANGE';
    }
    elseif($xx=3)
    {
        return 'THIRD_RANGE';
    }
    elseif($xx==4)
    {
        return 'FOURTH_RANGE';
    }
}
function get_range_fee_data($db,$id)
{
    $sql="SELECT `registartion_fee` FROM tbl_busines_formalization_fees where `id`='$id'  ";
    $result_set=mysqli_query($db,$sql);
    while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC)) 
    { 
    
        $registartion_fee=$row['registartion_fee'];
        
    }
    
    return $registartion_fee;
}
function validate_category_date($db,$cid)
{
    $sql="SELECT * FROM tbl_content where category='$cid'";
    $result_set=mysqli_query($db,$sql);
    
    return mysqli_num_rows ($result_set);
}
function get_range_data($db,$id)
{
    $sql="SELECT `fee_range` FROM tbl_busines_formalization_fees where `id`='$id'  ";
    $result_set=mysqli_query($db,$sql);
    while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC)) 
    { 
    
        $fee_range=$row['fee_range'];
        
    }
    
    return $fee_range;
}
function add_sms($db)
{
    //$db=login();
    
    $sql="SELECT * FROM tbl_sms_count  ";
    $result_set=mysqli_query($db,$sql);
    while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC)) 
    { 
    
        $sms_balance=$row['sms_balance'];
        $sms_sent=$row['sms_sent'];
        
    }
    
    
    $new_sms = $sms_sent + 1;
    
    $sql= "UPDATE tbl_sms_count set sms_sent = '$new_sms' ";
    
    $result = mysqli_query($db,$sql);  
    
      if($result)
    {
        
    }
    else
    {
        echo $sql;
        die();
    }

    return;
}

function bulk_engine($phone,$sender,$sms,$name,$db)
{
    
    date_default_timezone_set('Africa/Dar_es_Salaam');
    $d_date=date('Y-m-d H:i:s');
    $phone = floor($phone);
    $sql="INSERT INTO tbl_sentsms(phone_number,sendor,sms_sent,sent_date,customer_name)
    VALUES('$phone','$sender','$sms','$d_date','$name')";
    $result=mysqli_query($db,$sql);    
        
    if($result)
    {
        return true;
    }
    else
    {
        return false;
        //echo $sql;
        //die();
    }
}
function update_likes($db,$m_id)
{
    $likes=get_likes($db,$m_id) + 1;
    
    $sql_update="UPDATE tbl_media SET liked='$likes' where `m_id`='$m_id'";
    if(!mysqli_query($db,$sql_update))
    {
        die($sql_update);
    }

    return;
}

function get_likes($db,$m_id)
{
    $sql="SELECT `liked` FROM tbl_media where `m_id`='$m_id'";
    $result_set=mysqli_query($db,$sql);
    while($row=mysqli_fetch_array($result_set,MYSQLI_ASSOC)) 
    { 
        $liked=$row['liked'];
    }
    
    return $liked;
}
function validate_roll_number($db,$uwakili)
{
    $sql="Select * FROM tbl_wakiliramani where `n_uwakili`='$uwakili'";
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
function Validate_sent_tokentt($db,$msisdn)
{
    $sql="Select * FROM tbl_token where `phone_number`='$msisdn' AND `isverified`=0";
    $result_set=mysqli_query($db,$sql);
    $count = mysqli_num_rows($result_token);

    if ($count == 1) {
        return false;
    }
    else
    {
        return true;
    }
}
function update_pay_status($db,$order_id,$result)
{
    $sql="UPDATE tbl_payments SET status='$result' where order_id='$order_id' ORDER BY req_txn_date DESC LIMIT 1";
    if(mysqli_query($db,$sql))
    {
        return true;
    }
    else
    {
        //return false;
        echo $sql;
        die();
    }
    
}
function checkStatus($order_id) {
    /*
    $data = array('order_id' => $order_id);
    $url = "http://3.130.119.42/api/payment/check/status";
    $reponse = sendToApi($url,1,json_encode($data));
    */
    return $reponse;
}
function createOrder($amount,$phone) {
    $data = array('amount' => $amount,'phone'=> $phone);
    $url = "http://3.130.119.42/api/payment/create/order";
    $reponse = sendToApi($url,1,json_encode($data));
    return $reponse;
}
function pay($amount,$phone,$destination) {
    $data = array('amount' =>$amount,'phone'=>$phone);
    //$url = "https://sheriagateway.goldnet.tz/api/v1/payments/createorder";
    //https://sheriagateway.jsuite.app/api/v1/payments/createorder
    //$url = "https://sheriagateway.tetherverse.net/api/v1/payments/createorder";
    $url = "https://sheriagateway.goldnet.tz/api/v1/payments/createorder";
    $reponse = sendToApi($url,1,json_encode($data));
    error_log("\n".date('Y-m-d H:i:s')." Push Amount :\n $amount ",3, $destination);
    
    return $reponse;
}
function sendToApi($url, $isPost, $json) {
    $headers = array(
        "Content-type: application/json;charset=utf-8", "Accept: application/json", "Cache-Control: no-cache",
       /* "Authorization: SELCOM $authorization",
        "Digest-Method: HS256",
        "Digest: $digest",
        "Timestamp: $timestamp",
        "Signed-Fields: $signed_fields",*/
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($isPost){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch,CURLOPT_TIMEOUT,90);
    $result = curl_exec($ch);
    curl_close($ch);
    $resp = json_decode($result, true);
    return $resp;
}
function send_sms($semail)
{
    require_once 'mail_master/PHPMailerAutoload.php';
    
    $body="Greatings,\r\nKindly follow the below Link to change your password\r\nhttp://kilimosms.net/linkchangepass.php?username=".$semail."\r\nThanks & Regards.";

     global $error;
 $mail = new PHPMailer();  // create a new object
 $mail->IsSMTP(); // enable SMTP
 $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
 $mail->SMTPAuth = true;  // authentication enabled
 $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
 $mail->Host = 'mail.bluefinsolutions.co.tz';
 $mail->Port = 465; 
 $mail->Username = "dietrich.mchami@bluefinsolutions.co.tz";  
 $mail->Password = "1928mkali@199D";           
 $mail->SetFrom("dietrich.mchami@bluefinsolutions.co.tz", "FAO");
 $mail->Subject = "Reset Password";
 $mail->Body = $body;
 $mail->AddAddress("ronilickd@yahoo.com");
 if(!$mail->Send()) {
 $error = 'Mail error: '.$mail->ErrorInfo; 
 echo $error;
 die();
 
 } else {
 $error = 'Message sent!';

 return true;
 }
}

?>
