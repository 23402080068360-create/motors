<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");
$conf = $conn->query("SELECT * FROM configuracion WHERE id=1")->fetch_assoc();
// FILTRO CLAVE: Solo mostrar los que NO están ocultos
$comentarios = $conn->query("SELECT * FROM comentarios WHERE oculto = 0 ORDER BY id DESC");

if(isset($_POST['nuevo_comentario'])) {
    $n = $_POST['nombre']; $t = $_POST['texto']; $e = $_POST['estrellas'];
    $conn->query("INSERT INTO comentarios (nombre, texto, estrellas) VALUES ('$n', '$t', $e)");
    header("Location: inicio.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | <?= $conf['nombre_app'] ?></title>
    <style>
        body { background-color: #632626; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: Arial, Helvetica, sans-serif; }
        .phone-border { background-color: #1a1a1a; width: 360px; height: 740px; border-radius: 45px; padding: 12px; position: relative; display: flex; flex-direction: column; box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden; }
        .notch { width: 150px; height: 25px; background-color: #1a1a1a; position: absolute; top: 0; left: 50%; transform: translateX(-50%); border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; z-index: 10; }
        .screen { background-color: #d1d1d1; flex: 1; border-radius: 35px; overflow: hidden; display: flex; flex-direction: column; position: relative; }
        .content { flex: 1; padding: 50px 20px 85px; overflow-y: auto; text-align: center; scrollbar-width: none; }
        .content::-webkit-scrollbar { display: none; }
        .review-card { background: white; padding: 15px; border-radius: 12px; margin-bottom: 15px; text-align: left; border-left: 5px solid #632626; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 100%; box-sizing: border-box; }
        .stars-view { color: #d4af37; font-size: 14px; letter-spacing: 2px; }
        .admin-reply { background: #f1f1f1; padding: 10px; border-radius: 8px; border-left: 4px solid #d4af37; margin-top: 10px; font-size: 11px; }
        .tab-bar { position: absolute; bottom: 0; width: 100%; height: 70px; background: #f8f8f8; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #ccc; border-radius: 0 0 35px 35px; }
    </style>
</head>
<body>
<div class="phone-border">
    <div class="notch"></div>
    <div class="screen">
        <div class="content">
            <h1 style="color:#632626; font-size: 20px; text-transform: uppercase;"><?= $conf['nombre_app'] ?></h1>
            <img src="img/logo.png" style="width:75%; margin-bottom:15px;">
            
            <div style="background:#e0e0e0; padding:15px; border-radius:12px; margin-bottom:20px; text-align: left;">
                <form method="POST">
                    <input type="hidden" name="nuevo_comentario">
                    <input type="text" name="nombre" placeholder="Nombre o @usuario" required style="width:100%; padding:8px; margin-bottom:8px; border-radius:5px; border:1px solid #ccc; font-family: Arial;">
                    <textarea name="texto" placeholder="Opinión..." required style="width:100%; padding:8px; margin-bottom:8px; border-radius:5px; border:1px solid #ccc; font-family: Arial;"></textarea>
                    <select name="estrellas" style="width:100%; padding:8px; margin-bottom:8px; border-radius:5px; font-family: Arial;">
                        <option value="5">★★★★★ (5 Estrellas)</option>
                        <option value="4">★★★★☆ (4 Estrellas)</option>
                        <option value="3">★★★☆☆ (3 Estrellas)</option>
                        <option value="2">★★☆☆☆ (2 Estrellas)</option>
                        <option value="1">★☆☆☆☆ (1 Estrella)</option>
                    </select>
                    <button style="width:100%; background:#632626; color:white; border:none; padding:10px; border-radius:5px; font-weight:bold; cursor:pointer;">PUBLICAR</button>
                </form>
            </div>

            <h3 style="color:#632626; text-align:left; font-size:14px;">FEEDBACK DEL CLUB:</h3>
            <?php while($c = $comentarios->fetch_assoc()): ?>
                <div class="review-card">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <strong><?= $c['nombre'] ?></strong>
                        <div class="stars-view"><?= str_repeat('★', $c['estrellas']) ?><?= str_repeat('☆', 5-$c['estrellas']) ?></div>
                    </div>
                    <p style="margin:8px 0; font-size:12px; color:#333; line-height:1.4;"><?= $c['texto'] ?></p>
                    <?php if($c['respuesta_admin']): ?>
                        <div class="admin-reply">
                            <b style="color:#632626; display:block; margin-bottom:3px;">Vintage Motors responde:</b>
                            "<?= $c['respuesta_admin'] ?>"
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <nav class="tab-bar">
            <a href="inicio.php" style="text-decoration:none; font-size:20px;">🏠</a>
            <a href="catalogo.php" style="text-decoration:none; font-size:20px;">🏎️</a>
            <a href="registro-iniciodesesioncuenta.php" style="text-decoration:none; font-size:20px;">👤</a>
        </nav>
    </div>
</div>
</body>
</html>
