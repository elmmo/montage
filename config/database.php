<?
// load in packages 
require_once '../vendor/autoload.php';

// initialize DotEnv for environment variables 
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

// parse db connection info from env variable "
$db = parse_url(getenv("DATABASE_URL"));

// create connection 
try {
    $pdo = new PDO("pgsql:" . sprintf(
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
?>