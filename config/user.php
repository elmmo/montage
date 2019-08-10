<?php 

class User {
    // database connection and table name
    private $conn;
    private $table_name = "basic";
    private $username; 
    private $firstname;
    private $lastname;
    private $email;
    private $seminar; 
    private $password;

    // base constructor for first-time user creation
    public function __construct($db, $username, $first, $last, $email, $sem, $pass){
        $this->conn = $db;
        $this->username = $username; 
        $this->firstname = $first; 
        $this->lastname = $last; 
        $this->email = $email; 
        $this->seminar = $sem; 
        $this->password = $pass; 
    }

    // create new user record
    public function createDBEntry(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . " (firstname, lastname, email, seminar, password, username) " .
            "VALUES (:firstname, :lastname, :email, :seminar, :password, :username)"; 
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->seminar=htmlspecialchars(strip_tags($this->seminar));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->username=htmlspecialchars(strip_tags($this->username));
    
        // bind the values
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':seminar', $this->seminar);
        $stmt->bindParam(':username', $this->username);
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function getFirstName() {
        return $this->firstname; 
    }

    public function getLastName() {
        return $this->lastname; 
    }

    public function getEmail() {
        return $this->email; 
    }

    public function getSeminar() {
        return $this->seminar; 
    }

    public function getPassword() {
        return $this->password; 
    }

    public function getUsername() {
        return $this->username; 
    }
}

?>