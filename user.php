<?php

require_once('conexion.php');

class User {

    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $contrasenia;
    public $fechaNacimiento;
    public $pais;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=?, apellido=?, email=?, contrasenia=?, nacimiento=?, pais=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssss", $this->nombre, $this->apellido, $this->email, $this->contrasenia, $this->fechaNacimiento, $this->pais);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>