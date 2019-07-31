<?php
// required headers
header("Access-Control-Allow-Origin: ORIGIN");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// necessary imports 
require_once 'user.php';
require_once '../config/database.php'; 

// instantiate user
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$user->email = $data->email;
$email_exists = $user->emailExists();
 
// generate json web token
// check if email exists and if password is correct
if($email_exists && password_verify($data->password, $user->password)){
    // set response code
    http_response_code(200);

}
 
// login failed will be here

?>
