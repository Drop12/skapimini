<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

$req = file_get_contents("php://input");
$data = json_decode($req, true);

$destination = "logs/checkForPaymentStatus" . date("Y-m-d") . ".log";
error_log(
    "\n" . date("Y-m-d H:i:s") . " Incomming Request :\n $req ",
    3,
    $destination
);

require_once "functions.php";
$db = login();
date_default_timezone_set("Africa/Dar_es_Salaam");
//$phone = "255".substr($data['phone_number'],1);
$phone = $data["phone_number"];
$registration_ticket = $data["registration_ticket"];

$api_key = $data["api_key"];

$auth_key =
    "YTc4YWY3ZWVlZWViODU3YTY5ODUzNTA4ZGU3YzVhYzM1NTdjNjM1MWEyYzA1ODU1ZDhlqA12AjkP0qNTc4ZjU3N2QwMDVmMw==";

if ($api_key == $auth_key) {
    $sql = "SELECT * FROM tbl_payments where phone_number='$phone' AND websessiontxnid='$registration_ticket' ORDER BY id DESC LIMIT 1";
    $result_set = mysqli_query($db, $sql);
    if (!$result_set) {
        $response = [
            "status" => "200",
            "message" => mysqli_error($db),
            "result" => true,
        ];
        $riko = json_encode($response);
        error_log(
            "\n" . date("Y-m-d H:i:s") . " Response three :\n $riko ",
            3,
            $destination
        );
        header("Content-Type: application/json; charset=utf-8");
        print_r(json_encode($response));
        die();
    }

    if (mysqli_num_rows($result_set) > 0) {
        $record = [];
        while ($row = mysqli_fetch_array($result_set, MYSQLI_ASSOC)) {
            $record["phone"] = $row["phone_number"];
            $record["service"] = $row["service"];
            $record["amount"] = $row["amount"];
            $record["expires_at"] = $row["expires_at"];
            $record["status"] = $row["status"];
            $record["order_id"] = $row["order_id"];
        }
        if ($record["status"] == "SUCCESS") {
            $date = date("Y-m-d h:i");
            $expires_at = date("Y-m-d h:i", strtotime($record["expires_at"]));
            $is_expired = $date > $expires_at ? true : false;
            $response = [
                "status" => "200",
                "message" => "success",
                "result" => $is_expired, // check if expired
            ];
        } else {
    $order_id = $record["order_id"];

    $amount = $record["amount"];
    $phone = $record["phone"];

    $code = null;
    $result_code = null;
    $payment_status = null;

    $sql10 = "SELECT * FROM tbl_newpayment where order_id='$order_id' ORDER BY reg_date DESC LIMIT 1";
    $result_set10 = mysqli_query($db, $sql10);
    if(mysqli_num_rows($result_set10) > 0) {
        while ($row10 = mysqli_fetch_array($result_set10, MYSQLI_ASSOC)) {
            $resultcode = $row10["resultcode"];
            $payment_status = $row10["payment_status"];
        }

        error_log(
            "\n" . date("Y-m-d H:i:s") . " sql10 :\n $sql10 ",
            3,
            $destination
        );
        error_log(
            "\n" . date("Y-m-d H:i:s") . " code :\n $code ",
            3,
            $destination
        );
        error_log(
            "\n" . date("Y-m-d H:i:s") . " result_code :\n $result_code ",
            3,
            $destination
        );
        error_log(
            "\n" .
                date("Y-m-d H:i:s") .
                " payment_status :\n $payment_status ",
            3,
            $destination
        );
        error_log(
            "\n" . date("Y-m-d H:i:s") . " phone_number :\n $phone ",
            3,
            $destination
        );

        if ($resultcode == "000" && $payment_status == "COMPLETED") {
            $sql12 = "UPDATE `tbl_payments` SET `status`='SUCCESS' WHERE `phone_number`='$phone' AND `order_id`='$order_id'";

            error_log(
                "\n" . date("Y-m-d H:i:s") . " sql12 :\n $sql12 ",
                3,
                $destination
            );

            if (mysqli_query($db, $sql12)) {
                // updated successful hence false
                $response = [
                    "status" => "200",
                    "message" => "success",
                    "result" => false,
                ];
            } else {
                // error in updateing  hence true expired
                $response = [
                    "status" => "200",
                    "message" => mysqli_error($db),
                    "result" => true,
                ];
            }
        } else {
            $response = [
                "status" => "200",
                "message" => "success",
                "result" => true, /// transaction was not completed from the user phone
            ];
        }
    }
    else
    {
        die('Nipo');
        $resp_pay = pay($amount,$phone);
        
        die($resp_pay);
if($resp_pay['success']) {
    $response=array(
        "status"=>"200",
        "message"=>"success",
        "result"=>true
    );
}
else
{
    $response=array(
        "status"=>"422",
        "message"=>"Failure",
        "result"=>false,
    );
}
    }
}
        $riko = json_encode($response);
        error_log(
            "\n" . date("Y-m-d H:i:s") . " Response one :\n $riko ",
            3,
            $destination
        );
        header("Content-Type: application/json; charset=utf-8");
        print_r(json_encode($response));
        die();
    } else {
        // this phone does not have any transaction
        $response = [
            "status" => "200",
            "message" => "success",
            "result" => true,
        ];
        $riko = json_encode($response);
        error_log(
            "\n" . date("Y-m-d H:i:s") . " Response two :\n $riko ",
            3,
            $destination
        );
        header("Content-Type: application/json; charset=utf-8");
        print_r(json_encode($response));
        die();
    }
} else {
    $response = [
        "status" => "403",
        "statusDesc" => "Invalid API KEY"
    ];
    error_log(
        "\n" . date("Y-m-d H:i:s") . " Response two :\n $response ",
        3,
        $destination
    );
    header("Content-Type: application/json; charset=utf-8");
    print_r(json_encode($response));
    die();
}
