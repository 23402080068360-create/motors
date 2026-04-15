<?php
$conn = new mysqli("localhost", "root", "", "vintage_motors");
$id = $_POST['id'];
$resp = $_POST['resp'];
$conn->query("UPDATE comentarios SET respuesta_admin = '$resp' WHERE id = $id");
?>
