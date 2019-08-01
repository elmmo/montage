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
require_once '../config/jwt.php'; 

// get posted data
$data = json_decode(file_get_contents("php://input"));

// instantiate database and user 
$db = new Database(); 

// check db for posted data input
$entry = $db->emailExists($data->email); 

// ensure inputted email record exists 
if ($entry != null) {
    $user = new User($db, $entry['firstname'], $entry['lastname'], $entry['email'], $entry['seminar'], $entry['password']); 
    if (password_verify($data->password, $user->getPassword())) {
        http_response_code(200); 
        $JWT = new JWT($entry['user_id'], $user->getSeminar(), $user->getFirstName()); 
        $token = $JWT->generateJWS(); 

        echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $token
            )
        ); 
    } else {
        http_response_code(200);
        echo json_encode(array("message" => "Incorrect password."));
    }
} else {
    http_response_code(200);
    echo json_encode(array("message" => "User does not exist."));
}
?>
