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

    // returns user info if email exists, else null
    public function emailExists($input) {
        // check if email exists
        $query = "SELECT * FROM basic WHERE email = :email LIMIT 1"; 

        // prepare the query
        $stmt = $this->pdo->prepare($query);
    
        // sanitize
        $input=htmlspecialchars(strip_tags($input));
    
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