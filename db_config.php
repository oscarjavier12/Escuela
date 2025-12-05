<?php
// config.php - Configuración de conexión a la base de datos

define('DB_HOST', 'escuela.c5w04k0gqqf2.us-east-2.rds.amazonaws.com');
define('DB_NAME', 'Escuela');
define('DB_USER', 'Administrador');
define('DB_PASS', 'codigo26');

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
