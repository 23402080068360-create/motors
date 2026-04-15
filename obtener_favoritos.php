<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]); // Si no hay sesión, mandamos lista vacía
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT carro_id FROM favoritos WHERE usuario_id = '$usuario_id'";
$res = mysqli_query($conexion, $sql);

$favoritos = [];
while ($row = mysqli_fetch_assoc($res)) {
    $favoritos[] = $row['carro_id'];
}

echo json_encode($favoritos);
?>
