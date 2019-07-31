<?php 

class User {
    // database connection and table name
    private $conn;
    private $table_name = "basic";
 
    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $seminar; 
    public $password;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . " (firstname, lastname, email, seminar, password) " .
            "VALUES (:firstname, :lastname, :email, :seminar, :password)"; 
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->seminar=htmlspecialchars(strip_tags($this->seminar));
        $this->password=htmlspecialchars(strip_tags($this->password));
    
        // bind the values
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':seminar', $this->seminar);
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    
    // check if given email exist in the database
    function emailExists(){
    
        // check if email exists
        $query = "SELECT (user_id, firstname, lastname, seminar, password)
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['user_id'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->firstname = $row['seminar'];
            $this->password = $row['password'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }
    
    // update() method will be here
}

?>