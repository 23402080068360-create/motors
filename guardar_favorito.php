<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

if (isset($_POST['carro_id'])) {
    $id = $_POST['carro_id'];

    // Verificamos si ya existe el ID en favoritos
    $check = $conn->query("SELECT * FROM favoritos WHERE carro_id = '$id'");
    
    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM favoritos WHERE carro_id = '$id'");
    } else {
        $conn->query("INSERT INTO favoritos (carro_id) VALUES ('$id')");
    }
}
$conn->close();
?>
