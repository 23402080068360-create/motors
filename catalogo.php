<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

// 1. CARGAR AUTOS DE LA DB
$res_autos = $conn->query("SELECT * FROM inventario ORDER BY id DESC");
$autos_dict = []; $lista_render = [];
while($row = $res_autos->fetch_assoc()){
    $id_real = $row['id'];
    $datos = [
        "id" => $id_real, "n" => $row['nombre'], "p" => $row['precio'],
        "i" => $row['imagen'], "m" => $row['motor'],
        "d" => "Unidad de alto rendimiento disponible en el stock de Vintage Motors."
    ];
    $autos_dict[$id_real] = $datos;
    $lista_render[] = $datos;
}

// 2. FAVORITOS ACTUALES (Cargamos los IDs guardados)
$favs_db = [];
$res_favs = $conn->query("SELECT carro_id FROM favoritos");
while($f = $res_favs->fetch_assoc()){ $favs_db[] = $f['carro_id']; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo | Vintage Motors</title>
    <style>
        body { background-color: #632626; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: Arial, sans-serif; }
        .phone-border { background-color: #1a1a1a; width: 360px; height: 740px; border-radius: 45px; padding: 12px; position: relative; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.7); overflow: hidden; }
        .notch { width: 150px; height: 25px; background-color: #1a1a1a; position: absolute; top: 0; left: 50%; transform: translateX(-50%); border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; z-index: 10; }
        .screen { background-color: #d1d1d1; flex: 1; border-radius: 35px; overflow: hidden; display: flex; flex-direction: column; position: relative; }
        .content { flex: 1; padding: 50px 15px 85px; overflow-y: auto; scrollbar-width: none; }
        .content::-webkit-scrollbar { display: none; }

        .search-box { margin-bottom: 20px; }
        #carSelector { width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #bcbcbc; font-family: Arial; }

        .car-card { background: #f5f5f5; border-radius: 15px; margin-bottom: 20px; overflow: hidden; border: 1px solid #bcbcbc; position: relative; text-align: center; }
        .car-image { width: 100%; height: 160px; object-fit: cover; border-bottom: 2px solid #632626; }
        .fav-star { position: absolute; top: 10px; right: 10px; font-size: 30px; color: rgba(255,255,255,0.8); cursor: pointer; text-shadow: 0 0 5px rgba(0,0,0,0.5); z-index: 5; }
        .fav-star.active { color: #ffcc00; }
        .btn-detalles { background: #4a3333; color: white; border: none; padding: 12px; width: 100%; cursor: pointer; font-weight: bold; font-family: Arial; text-transform: uppercase; font-size: 11px; }

        #vista-detalles { display: none; text-align: center; }
        .tab-bar { position: absolute; bottom: 0; width: 100%; height: 75px; background: #f8f8f8; display: flex; justify-content: space-around; align-items: center; border-radius: 0 0 35px 35px; border-top: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="phone-border">
    <div class="notch"></div>
    <div class="screen">
        <div class="content" id="scrollContainer">
            <div id="main-list">
                <h1 style="text-align:center; color:#632626; font-size:18px;">VINTAGE MOTORS</h1>
                <div class="search-box">
                    <select id="carSelector" onchange="filterCars()">
                        <option value="all">-- BUSCAR AUTO --</option>
                        <?php foreach($lista_render as $a): ?>
                            <option value="card-<?= $a['id'] ?>"><?= $a['n'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php foreach($lista_render as $a): ?>
                <div class="car-card" id="card-<?= $a['id'] ?>">
                    <span class="fav-star <?= in_array($a['id'], $favs_db) ? 'active' : '' ?>" onclick="toggleFav(<?= $a['id'] ?>, this)">★</span>
                    <img src="<?= $a['i'] ?>" class="car-image">
                    <div style="padding:12px;">
                        <h2 style="font-size:15px; margin:0;"><?= $a['n'] ?></h2>
                        <p style="color:#632626; font-weight:bold; margin:5px 0;">$<?= number_format($a['p']) ?> USD</p>
                        <button class="btn-detalles" onclick="verDetalles(<?= $a['id'] ?>)">ESPECIFICACIONES</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="vista-detalles">
                <div id="render-info"></div>
                <button class="btn-detalles" style="margin-top:20px; border-radius:8px;" onclick="cerrarDetalles()">← REGRESAR</button>
            </div>
        </div>

        <nav class="tab-bar">
            <a href="inicio.php" style="text-decoration:none; font-size:22px;">🏠</a>
            <a href="catalogo.php" style="text-decoration:none; font-size:22px; color:#632626;">🏎️</a>
            <a href="favoritos.php" style="text-decoration:none; font-size:22px; color:#888;">★</a>
            <a href="registro-iniciodesesioncuenta.php" style="text-decoration:none; font-size:22px;">👤</a>
        </nav>
    </div>
</div>
<script>
    const autos = <?= json_encode($autos_dict) ?>;
    function toggleFav(id, el) {
        el.classList.toggle('active');
        let fd = new FormData(); fd.append('carro_id', id);
        fetch('guardar_favorito.php', { method: 'POST', body: fd });
    }
    function filterCars() {
        let val = document.getElementById('carSelector').value;
        document.querySelectorAll('.car-card').forEach(c => c.style.display = (val === "all" || c.id === val) ? "block" : "none");
    }
    function verDetalles(id) {
        const a = autos[id];
        document.getElementById('main-list').style.display = 'none';
        document.getElementById('vista-detalles').style.display = 'block';
        document.getElementById('render-info').innerHTML = `<img src="${a.i}" style="width:100%; border-radius:15px; border:2px solid #632626; margin-bottom:15px;"><h2 style="color:#632626;">${a.n}</h2><p style="font-size:20px; font-weight:bold;">$${new Intl.NumberFormat().format(a.p)} USD</p><table style="width:100%; background:white; border-radius:10px; padding:10px; font-size:13px; text-align:left;"><tr><td><b>Motor:</b></td><td style="text-align:right;">${a.m}</td></tr></table><div style="background:#eee; padding:15px; border-radius:10px; margin-top:15px; font-size:12px; text-align:justify;">${a.d}</div>`;
    }
    function cerrarDetalles() {
        document.getElementById('main-list').style.display = 'block';
        document.getElementById('vista-detalles').style.display = 'none';
    }
</script>
</body>
</html>
