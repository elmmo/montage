<?php
// required headers
header("Access-Control-Allow-Origin: whit23");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
        // get the user id from the first entry returned
        $profile = $db->getUserById($id[0]['user_id']); 
        http_response_code(200);
        // return first entry
        echo json_encode($profile[0]);
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode(array("message" => "User doesn't exist."));
    }
}

?>

