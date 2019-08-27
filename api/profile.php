<?php
// required headers
header("Access-Control-Allow-Origin: whit23");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// necessary imports 
require_once '../config/database.php'; 

// get posted data 
$data = json_decode(file_get_contents("php://input"));

$db = new Database(); 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $id = $db->getUserIdByUsername($_GET['user']);
        $profile = $db->getUserById($id); 
        http_response_code(200);
        echo json_encode($profile);
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode(array("message" => "User doesn't exist."));
    }
}

?>

