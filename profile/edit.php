<!DOCTYPE html> 
<html>
    <head>
        <title>Edit Profile | Montage</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h2>Edit Profile</h2>
        <ul>
            <form name="edit" action="edit.php" method="POST" >
                <li>First Name:</li><li><input type="text" name="firstname" /></li>
                <li>Last Name:</li><li><input type="text" name="lastname" /></li>
                <li>Email:</li><li><input type="email" name="email" /></li>
                <li>Bio:</li><li><textarea name="bio"></textarea></li>
                <li>Major(s):</li><li><input type="text" name="major" /></li>
                <li>Minor(s):</li><li><input type="text" name="minor" /></li>
                <li>Instagram:</li><li><input type="text" name="insta" /></li>
                <li>Facebook:</li><li><input type="text" name="fb" /></li>
                <li>Snapchat:</li><li><input type="text" name="snap" /></li>
                <li><input type="submit" name="submit" /></li>
            </form>
        </ul>
    </body>
</html>

<?php 
require_once '../config/database.php'; 

if (isset($_POST['submit'])) {
    $update = $pdo->prepare('UPDATE users SET firstname = ?, lastname = ?, email = ?, bio = ?, major = ?, minor = ?, insta = ?, fb = ?, snap = ? WHERE username IS elmmo');
    $update->execute([$_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['bio'], $_POST['major'], $_POST['minor'], $_POST['insta'], $_POST['fb'], $_POST['snap']]);
    if ($update) {
        echo "Updated successfully."; 
    } else {
        echo "Error."; 
    }
}
?>