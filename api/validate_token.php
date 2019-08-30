<?php 
// necessary imports
require_once "../config/jwt.php";

$JWT = new JWT(); 

// get jwt
$token = isset($_GET['token']) ? $_GET['token'] : "";

if ($JWT->verify($token)) {
    if ($JWT->getPayloadFromToken($token)) {
        // set response code
        http_response_code(200);
 
        //show user details
        echo json_encode(array(
            "message" => "Access granted.",
            "data" => $JWT->getPayloadFromToken($token)
        ));
    } else {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Unable to generate payload."
        ));
    }
} else {
    http_response_code(401);
    echo json_encode(array(
        "message" => "Access denied: unable to validate token."
    ));
}

?>