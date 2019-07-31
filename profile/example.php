<?php
require_once '../config/jwt.php'; 

$JWTEx = new JWT(12345, "Wheeler");
$JWTEx = $hello->getJWS();  

if ($JWTEx->verify($token)) {
    echo $JWTEx->getId(); 
}

?>