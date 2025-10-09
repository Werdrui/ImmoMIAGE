<!-- <?php
class Database
{
    private static $pdo = null;
    public static $dbName = 'immo_data';
    public static $dbPath = 'data/';
    public static $dbHost = 'localhost';
    public static $dbUser = 'root';
    public static $dbPass = '';

    /**
     * Establishes a static PDO connection to a database.
     *
     * @return PDO|string A PDO instance on successful connection, or an error message string.
     */
    public static function connect(): PDO|string
    {
        // Check if a connection already exists.
        if (self::$pdo !== null) {
            return self::$pdo;
        }


        try {
            // Create a new PDO instance for MySQL.
            self::$pdo = new PDO(
                "mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName . ";charset=utf8mb4",
                self::$dbUser,
                self::$dbPass);
            return self::$pdo;

        } catch (PDOException $e) {
            // Return an error message if the connection fails.
            return "Error: MySQL database connection failed: " . $e->getMessage();
        }
    }
}
?> -->