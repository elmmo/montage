<?php
// required headers
header("Access-Control-Allow-Origin: http://192.168.64.2");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// necessary imports 
require_once 'user.php';
require_once '../config/database.php'; 

$user = new User($pdo); 

// get posted data 
$data = json_decode(file_get_contents("php://input"));

// set product property values
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->seminar = $data->seminar;
$user->password = $data->password;

// attempt to create user
if(!empty($user->firstname) && !empty($user->email) && !empty($user->seminar) && !empty($user->password) && $user->create()) {
    // case: user created
    http_response_code(200);
    echo json_encode(array("message" => "User was created."));
} else {
    // case: user creation failed
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user."));
}
?>

