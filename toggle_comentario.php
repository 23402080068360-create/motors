<?php
$conn = new mysqli("localhost", "root", "", "vintage_motors");
$id = $_POST['id'];
$nest = $_POST['nest'];
$conn->query("UPDATE comentarios SET oculto = $nest WHERE id = $id");
?>
