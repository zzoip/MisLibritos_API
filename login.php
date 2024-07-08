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

if (empty($data->email) || empty($data->contrasenia)) {
    http_response_code(400);
    json_encode(array("message" => "El email y la contrasenia son obligatorios."));
    exit();
}

$user->email = $data->email;

$query = "SELECT * FROM usuarios WHERE email=?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $user->email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($id, $nombre, $apellido, $email, $contrasenia, $nacimiento, $pais, $permisos);
    $stmt->fetch();

    if (password_verify($data->contrasenia, $contrasenia)) {
        http_response_code(200);
        echo json_encode(array(
            "message" => "Successfully logged in.",
            "user" => array(
                "id" => $id,
                "nombre" => $nombre,
                "email" => $email,
                "permisos" => $permisos
            )
            ));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Contrasenia incorrecta."));
    }
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Usuario no encontrado."));
}

?>