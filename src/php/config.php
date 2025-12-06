<?php
// config.php - Configuración de conexión a la base de datos

class conexion {
    private $host;
    private $bd;
    private $user;
    private $password;
    private $puerto;
    private $conexionBD;

    function __construct($host, $bd, $user, $password, $puerto){ 
        $this->host = $host;
        $this->bd = $bd;
        $this->user = $user;
        $this->password = $password;
        $this->puerto = $puerto;
        $this->conexionBD = null;
    }
    
    public function conectar(){
        $this->conexionBD = pg_connect("host=$this->host port=$this->puerto dbname=$this->bd user=$this->user password=$this->password");
        return $this->conexionBD;
    }
    
    public function desconectar(){
        pg_close($this->conexionBD);
    }
    
    public function ejecutar($sql){
        return pg_query($this->conexionBD, $sql);
    }
    
    public function getConexion(){
        return $this->conexionBD;
    }
}

// Instancia global de la conexión
function getConexion() {
    static $conexion = null;
    if ($conexion === null) {
        $conexion = new conexion(
            'escuela.c5w04k0gqqf2.us-east-2.rds.amazonaws.com',
            'Escuela',
            'Administrador',
            'codigo26',
            '5432'
        );
        $conexion->conectar();
    }
    return $conexion;
}
?>