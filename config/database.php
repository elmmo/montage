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

    // retrieve keys for active sessions and delete inactive sessions
    public function retrieveKeys() {
        try { 
            // insert query
            $query = "SELECT key FROM keys"; 
        
            // prepare the query
            $stmt = $this->pdo->prepare($query);
            // execute the query
            $stmt->execute();
            // get number of rows
            $num = $stmt->rowCount();

            return $num > 0 ? $stmt->fetchAll() : null; 
        } catch (PDOException $e) {
            echo "Error retrieving keys from database: " . $e->getMessage() . "<br/>";
            die(); 
        }
    }

    // returns user info if email exists, else null
    public function emailExists($input) {
        // check if email exists
        $query = "SELECT * FROM basic WHERE email = :email LIMIT 1"; 

        // prepare the query
        $stmt = $this->pdo->prepare($query);
    
        // sanitize
        $input=htmlspecialchars(strip_tags($input));
        $input=rtrim($input); 
    
        // bind given email value
        $stmt->bindParam(':email', $input);
    
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