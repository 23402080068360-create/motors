<?php
$conn = new mysqli("localhost", "root", "", "vintage_motors");

if (isset($_POST['carro_id'])) {
    $id = $_POST['carro_id'];
    $precio = (int)$_POST['precio'];

    // Insertar en la tabla carrito
    $sql = "INSERT INTO carrito (carro_id, precio) VALUES ('$id', '$precio')";
    if($conn->query($sql)) {
        echo "Añadido al carrito";
    } else {
        echo "Error al añadir";
    }
}

// Para vaciar el carrito después de pagar
if (isset($_GET['vaciar'])) {
    $conn->query("TRUNCATE TABLE carrito");
    header("Location: inicio.php");
}
?>
