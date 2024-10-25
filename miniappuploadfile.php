<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(0);

$destination="logs/uploadfile".date('Y-m-d').".log";

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ensure there is a file in the request
    if (isset($_FILES['file'])) {

        $uploadedFile = $_FILES['file'];

        // Access file details
        $fileName = time().'.pdf';
        $fileType = $uploadedFile['type'];
        $fileSize = $uploadedFile['size'];
        
        error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $fileName ",3, $destination);
        
        $fileTmpName = $uploadedFile['tmp_name'];

        // Move the uploaded file to a desired directory
        $destinationPath = 'uploads/' . $fileName;
        move_uploaded_file($fileTmpName, $destinationPath);


        //$pdfFilePath =  'uploads/'.$fileName;; 

        //$pdfContent = file_get_contents($pdfFilePath);

        //Convert the PDF content to base64
        //$base64PDF = base64_encode($pdfContent);
        
        
        // You can now process the file or send a response
        $response = array(
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'url'=>'https://sheriakiganjani.co.tz/skapi/uploads/'.$fileName,
        );

        $response=json_encode($response);
        error_log("\n".date('Y-m-d H:i:s')." Response :\n $response ",3, $destination);
        header('Content-Type: application/json; charset=utf-8');
        print_r($response);
        die();
    } else {
        // No file in the request
        $response = array(
            'status' => 'error',
            'message' => 'No file uploaded'
        );

        $response=json_encode($response);
        error_log("\n".date('Y-m-d H:i:s')." Response :\n $response ",3, $destination);
        header('Content-Type: application/json; charset=utf-8');
        print_r($response);
        die();
    }
} else {
    // Handle non-POST requests
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );

    $response=json_encode($response);
    error_log("\n".date('Y-m-d H:i:s')." Response :\n $response ",3, $destination);
    header('Content-Type: application/json; charset=utf-8');
    print_r($response);
    die();
}

//$input=file_get_contents('php://input');
//$data = json_decode($input, true);

//error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $input ",3, $destination);
//error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $fileType ",3, $destination);
//error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $fileName ",3, $destination);
//error_log("\n".date('Y-m-d H:i:s')." Incomming Request :\n $data ",3, $destination);

/*
$response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );
    
$response=json_encode($response);
error_log("\n".date('Y-m-d H:i:s')." Response :\n $response ",3, $destination);
header('Content-Type: application/json; charset=utf-8');
print_r($response);
die();
*/
?>
