<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

// 1. OBTENER EL NOMBRE DEL USUARIO
$usuario = $_SESSION['user_name'] ?? 'Usuario Invitado';

// 2. SELECCIONAR FAVORITOS UNIENDO CON INVENTARIO PARA TRAER EL PRECIO REAL
// Así no necesitas la lista manual de $precios
$favoritos = $conn->query("
    SELECT f.carro_id, i.precio 
    FROM favoritos f 
    JOIN inventario i ON (f.carro_id = i.id OR f.carro_id = i.nombre)
");

if ($favoritos && $favoritos->num_rows > 0) {
    while($row = $favoritos->fetch_assoc()) {
        $c_id = $row['carro_id'];
        $precio = $row['precio']; // Este es el precio real que pusiste en el Dash
        
        // 3. INSERTAR EN LA TABLA PEDIDOS
        $conn->query("INSERT INTO pedidos (usuario_nombre, carro_id, precio, fecha) 
                      VALUES ('$usuario', '$c_id', '$precio', NOW())");
    }

    // 4. VACIAR FAVORITOS (Usa DELETE para ser más preciso)
    $conn->query("DELETE FROM favoritos");
}

// 5. REDIRIGIR AL PERFIL
header("Location: registro-iniciodesesioncuenta.php");
exit();
?>
