<?php
error_reporting(0);
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

$action = $_GET['action'] ?? '';

// Obtener configuración actual
$conf = $conn->query("SELECT * FROM configuracion WHERE id=1")->fetch_assoc();

// --- LÓGICA DE REGISTRO ---
if ($action == 'register') {
    if ($conf['permitir_registros'] == 0) {
        echo "Lo sentimos, los registros están desactivados por el administrador.";
        exit;
    }

    $n = $_POST['nombre'];
    $e = $_POST['email'];
    $p = $_POST['pass1'];
    
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES ('$n', '$e', '$p', 'user')";
    
    if($conn->query($sql)) {
        echo "¡Registro exitoso! Ya puedes iniciar sesión.";
    } else {
        echo "Error: El correo ya está registrado.";
    }
}

// --- LÓGICA DE LOGIN ---
if ($action == 'login') {
    $e = $_POST['email'];
    $p = $_POST['pass'];
    
    $res = $conn->query("SELECT * FROM usuarios WHERE email='$e' AND password='$p'");
    
    if($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol']; // <--- Aquí detectamos si es admin o user
        echo "OK";
    } else {
        echo "Correo o contraseña incorrectos.";
    }
}
$conn->close();
?>
