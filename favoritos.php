<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

// OBTENEMOS LOS DATOS CRUZANDO TABLAS POR ID
$res = $conn->query("SELECT f.carro_id, i.nombre, i.precio, i.imagen 
                     FROM favoritos f 
                     JOIN inventario i ON f.carro_id = i.id");
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Favoritos | Vintage Motors</title>
    <style>
        body { background-color: #632626; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: Arial; }
        .phone-border { background-color: #1a1a1a; width: 360px; height: 740px; border-radius: 45px; padding: 12px; position: relative; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.7); overflow: hidden; }
        .notch { width: 150px; height: 25px; background-color: #1a1a1a; position: absolute; top: 0; left: 50%; transform: translateX(-50%); border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; z-index: 10; }
        .screen { background-color: #d1d1d1; flex: 1; border-radius: 35px; overflow: hidden; display: flex; flex-direction: column; position: relative; }
        .content { flex: 1; padding: 45px 15px 85px; overflow-y: auto; scrollbar-width: none; }
        .car-card { background: white; border-radius: 12px; margin-bottom: 12px; display: flex; align-items: center; padding: 10px; position: relative; border-left: 5px solid #632626; }
        .car-card img { width: 80px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 15px; }
        .footer-total { background: white; padding: 15px; border-radius: 15px; text-align: center; border: 2px solid #632626; margin-top: 10px; }
        .btn-pay { background: #632626; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; text-transform: uppercase; margin-top: 10px; }
        .tab-bar { position: absolute; bottom: 0; width: 100%; height: 75px; background: #f8f8f8; display: flex; justify-content: space-around; align-items: center; border-radius: 0 0 35px 35px; border-top: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="phone-border">
    <div class="notch"></div>
    <div class="screen">
        <div class="content">
            <h1 style="text-align:center; color:#632626; font-size:18px;">LISTA DE COMPRA</h1>
            <?php if($res->num_rows > 0): ?>
                <?php while($row = $res->fetch_assoc()): 
                    $total += $row['precio']; ?>
                    <div class="car-card">
                        <img src="<?= $row['imagen'] ?>">
                        <div>
                            <b style="font-size:13px;"><?= $row['nombre'] ?></b><br>
                            <span style="color:#632626; font-weight:bold; font-size:12px;">$<?= number_format($row['precio']) ?> USD</span>
                        </div>
                        <div style="position:absolute; right:10px; cursor:pointer;" onclick="quitar(<?= $row['carro_id'] ?>)">❌</div>
                    </div>
                <?php endwhile; ?>
                <div class="footer-total">
                    <b>TOTAL: $<?= number_format($total) ?> USD</b>
                    <button class="btn-pay" onclick="location.href='checkout.php'">COMPRAR LOTE</button>
                </div>
            <?php else: ?>
                <p style="text-align:center; color:#888; margin-top:50px;">Tu garaje está vacío.</p>
            <?php endif; ?>
        </div>
        <nav class="tab-bar">
            <a href="inicio.php" style="text-decoration:none; font-size:22px;">🏠</a>
            <a href="catalogo.php" style="text-decoration:none; font-size:22px;">🏎️</a>
            <a href="favoritos.php" style="text-decoration:none; font-size:22px; color:#632626;">★</a>
            <a href="registro-iniciodesesioncuenta.php" style="text-decoration:none; font-size:22px;">👤</a>
        </nav>
    </div>
</div>
<script>
    function quitar(id) {
        let fd = new FormData(); fd.append('carro_id', id);
        fetch('guardar_favorito.php', { method: 'POST', body: fd }).then(() => location.reload());
    }
</script>
</body>
</html>
