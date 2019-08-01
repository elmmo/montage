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

// get posted data 
$data = json_decode(file_get_contents("php://input"));

$db = new Database(); 
$user = new User($db->pdo, $data->firstname, $data->lastname, $data->email, $data->seminar, $data->password); 

if($user->createDBEntry()) {
    // case: user created
    http_response_code(200);
    echo json_encode(array("message" => "User was created."));
} else {
    // case: user creation failed
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user."));
}
?>

