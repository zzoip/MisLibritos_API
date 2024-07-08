<?php

include_once 'database.php';
include_once 'user.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Controll-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header("Content-Type: application/json; charset=UTF-8");
    json_encode(array("message" => "Metodo no permitido."));
    exit();
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$user->nombre = $data->nombre;
$user->apellido = $data->apellido;
$user->email = $data->email;
$user->contrasenia = password_hash($data->contrasenia, PASSWORD_BCRYPT);
$user->fechaNacimiento = $data->fechaNacimiento;
$user->pais = $data->pais;

if ($user->create()) {
    http_response_code(201);
    echo json_encode(array("message" => "Usuario registrado."));
} else {
    http_response_code(503);
    echo json_encode(array("message" => "Error al registrar usuario."));
}

?>