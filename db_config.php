<?php
// config.php - Configuración de conexión a la base de datos

define('DB_HOST', 'localhost');
define('DB_NAME', 'universidad');
define('DB_USER', 'postgres');
define('DB_PASS', 'tu_password');

class Database {
    private $conn;
    
    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
