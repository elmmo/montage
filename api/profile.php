<?php
// required headers
header("Access-Control-Allow-Origin: http://192.168.64.2");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// necessary imports 
require_once '../config/database.php'; 

// keep track of which updatable columns belong to which tables 
$basic = ['username', 'firstname', 'lastname', 'email']; 
$profile = ['bio', 'major', 'minor', 'insta', 'snap']; 

// get posted data 
$data = json_decode(file_get_contents("php://input"));

$db = new Database(); 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if ($_GET['user'] == null) {
            throw new InvalidArgumentExcpetion("User not passed."); 
        } else { 
            $id = $db->getUserIdByUsername($_GET['user']);
            // get the user id from the first entry returned
            $profile = $db->getUserById($id[0]['user_id']); 
            http_response_code(200);
            // return first entry
            echo json_encode($profile[0]);
        }
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode(array("message" => "User doesn't exist."));
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        // process the update request form into their respective tables 
        $processedForm = ["basic" => null, "profile" => null]; 
        $success = array(); 
        foreach($data as $key => $value) {
            if ($value != "") {
                if (in_array($key, $basic)) { 
                    $processedForm["basic"][$key] = $value; 
                    $success[0] = 0; 
                } else { 
                    $processedForm["profile"][$key] = $value; 
                    $success[1] = 0; 
                }
            }
        }
        // submit an update request for each table
        foreach($processedForm as $key => $value) {
            if ($value != null && $db->update($key, $value, $_GET['user'], $_GET['id'])) {
                if ($key == "basic") {
                    $success[0] = 1; 
                } else {
                    $success[1] = 1; 
                }
            }
        }

        // check if the requested changes were successful 
        if (in_array(0, $success, true)) {
            throw new Exception("Error in updating."); 
        } else {
            http_response_code(200);
            echo json_encode(array("message" => "Database successfully updated."));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => $e->getMessage()));
    }
}

?>

