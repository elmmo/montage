<?
// load in packages 
require_once '../vendor/autoload.php';

class Database {
    public $pdo; 

    public function __construct() {
        // initialize DotEnv for environment variables 
        $dotenv = Dotenv\Dotenv::create(__DIR__);
        $dotenv->load();

        // parse db connection info from env variable "
        $db = parse_url(getenv("DATABASE_URL"));

        // create connection 
        try {
            $this->pdo = new PDO("pgsql:" . sprintf(
            "host=%s;port=%s;user=%s;password=%s;dbname=%s",
            $db["host"],
            $db["port"],
            $db["user"],
            $db["pass"],
            ltrim($db["path"], "/")
            ));
        } catch (PDOException $e) {
            echo "Error connecting to database: " . $e->getMessage() . "<br/>";
            die(); 
        }
    }

    // store key's k value for active sessions
    public function storeKey($keyK, $id) {
        try { 
            // insert query
            $query = "INSERT INTO keys (key, user_id) VALUES (:keyK, :user_id)"; 
        
            // prepare the query
            $stmt = $this->pdo->prepare($query);
            $k = $keyK->get('k'); 
            $stmt->bindParam(':keyK', $k);
            $stmt->bindParam(':user_id', $id);
        
            // execute the query, also check if query was successful
            if($stmt->execute()){
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Error storing key in database: " . $e->getMessage() . "<br/>";
            die(); 
        }
    }

    // temporary function to avoid Heroku's scheduler addon
    // removes entries that are older than 3 hours from the keys table
    // for prod use cron or custom scheduler
    public function removeExpiredKeys() {
        try { 
            // insert query
            $query = "DELETE FROM keys WHERE time < now() - interval '3 hours'"; 
        
            // prepare the query
            $stmt = $this->pdo->prepare($query);
        
            // execute the query, also check if query was successful
            if($stmt->execute()){
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Error removing expired keys: " . $e->getMessage() . "<br/>";
            die(); 
        }
    }

    // retrieve info from the db 
    // $rows: string            which rows to retrieve 
    // $table_name: string      the name of the table to retrieve from
    // $condition: string       any additional conditions for retrieving
    public function retrieve($rows, $table_name, $condition = "") {
        try { 
            // insert query
            $query = "SELECT $rows FROM $table_name $condition"; 
        
            // prepare the query
            $stmt = $this->pdo->prepare($query);
            // execute the query
            $stmt->execute();
            // get number of rows
            $num = $stmt->rowCount();

            return $num > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : null; 
        } catch (PDOException $e) {
            echo ("Error retrieving " . $rows . " from table " . $table_name . ": " . $e->getMessage() . "<br/>"); 
            die(); 
        }
    }

    // gets all public user data and prevents exposure of sensitive information
    public function getUserById($id) {
        return $this->retrieve("basic.username, 
                                basic.firstname, 
                                basic.lastname, 
                                profile.bio, 
                                profile.major, 
                                profile.minor, 
                                basic.email, 
                                profile.insta, 
                                profile.snap", 
                                "basic, profile",
                                "WHERE basic.user_id = $id AND profile.user_id = $id");
    }

    // gets the user id of a user by their username
    public function getUserIdByUsername($username) {
        return $this->retrieve("user_id", "basic", "WHERE username = '$username'");
    }

    // returns user info if email or username exists, else null
    public function userExists($input, $type) {
        // check if user exists
        $query = "SELECT * FROM basic WHERE " . $type . " = :input LIMIT 1"; 

        // prepare the query
        $stmt = $this->pdo->prepare($query);
    
        // sanitize
        $input=htmlspecialchars(strip_tags($input));
        $input=rtrim($input); 
    
        // bind given email value
        $stmt->bindParam(':input', $input);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row; 
        }
    
        // return null if email does not exist in the database
        return null; 
    }
}
?>