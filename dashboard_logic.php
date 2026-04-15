<?php
$conn = new mysqli("localhost", "root", "", "vintage_motors");
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $conn->query("DELETE FROM inventario WHERE id = $id");
}
header("Location: dashboard.php");
?>
