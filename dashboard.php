<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

// Seguridad: Solo administradores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    die("No autorizado. Debes iniciar sesión como administrador."); 
}

// 1. LÓGICA DE GUARDADO (CREAR O EDITAR)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_car'])) {
    $n = $_POST['n']; $p = $_POST['p']; $m = $_POST['m']; $i = $_POST['img']; $d = $_POST['desc'];

    if (!empty($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $conn->query("UPDATE inventario SET nombre='$n', precio=$p, motor='$m', imagen='$i', descripcion='$d' WHERE id=$id");
    } else {
        $conn->query("INSERT INTO inventario (nombre, precio, motor, imagen, descripcion) VALUES ('$n', $p, '$m', '$i', '$d')");
    }
    header("Location: dashboard.php");
    exit();
}

// 2. CONSULTAS
$comentarios = $conn->query("SELECT * FROM comentarios ORDER BY id DESC");
$listado_com = [];
while($r = $comentarios->fetch_assoc()) { $listado_com[] = $r; }
$autos = $conn->query("SELECT * FROM inventario ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dash Maestro | Vintage Motors</title>
    <style>
        :root { --bg: #d1d1d1; --text: #333; --card: #eee; --input: white; --label: #632626; }
        .dark-mode { --bg: #262626; --text: #f5f5f5; --card: #333; --input: #444; --label: #d4af37; }

        body { background-color: #632626; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: Arial, sans-serif; transition: 0.3s; }
        
        .phone-border { background-color: #1a1a1a; width: 360px; height: 740px; border-radius: 45px; padding: 12px; position: relative; display: flex; flex-direction: column; border: 1px solid #d4af37; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .screen { background-color: var(--bg); flex: 1; border-radius: 35px; overflow: hidden; display: flex; flex-direction: column; color: var(--text); position: relative; }
        
        .mode-toggle { position: absolute; top: 50px; right: 25px; z-index: 100; cursor: pointer; font-size: 22px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5)); }

        /* TABS MEJORADAS CON BOTÓN SALIR */
        .nav-tabs { display: flex; justify-content: space-around; background: #333; padding: 12px 0; border-bottom: 2px solid #d4af37; z-index: 10; }
        .tab-btn { background: none; border: none; color: #888; font-weight: bold; cursor: pointer; font-size: 9px; text-transform: uppercase; }
        .tab-btn.active { color: #d4af37; }
        .tab-btn.exit { color: #ff4444; border: 1px solid #ff4444; border-radius: 4px; padding: 2px 6px; }

        .content { flex: 1; padding: 20px 15px 80px; overflow-y: auto; scrollbar-width: none; }
        .content::-webkit-scrollbar { display: none; }

        label { display: block; font-size: 10px; font-weight: bold; color: var(--label); margin-bottom: 3px; text-transform: uppercase; }
        input, textarea { width: 100%; background: var(--input); border: 1px solid #bcbcbc; border-radius: 8px; padding: 10px; margin-bottom: 12px; color: var(--text); box-sizing: border-box; font-family: Arial; }
        
        .img-container { width: 100%; height: 180px; border: 2px dashed var(--label); border-radius: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; background: var(--card); overflow: hidden; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; display: none; }
        .img-placeholder { color: var(--label); font-weight: bold; font-size: 11px; text-align: center; }

        .btn-save { background: #632626; color: white; width: 100%; padding: 15px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; text-transform: uppercase; }
        .stock-card { background: var(--card); padding: 12px; border-radius: 12px; margin-bottom: 10px; border-left: 5px solid var(--label); }
        .btn-mini { padding: 6px 10px; border-radius: 5px; cursor: pointer; border: none; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="phone-border">
    <div class="mode-toggle" onclick="toggleTheme()">🌓</div>
    <div class="screen">
        <div class="nav-tabs">
            <button class="tab-btn active" id="b1" onclick="tab(1)">Editor</button>
            <button class="tab-btn" id="b2" onclick="tab(2)">Stock</button>
            <button class="tab-btn" id="b3" onclick="tab(3)">Feed</button>
            <button class="tab-btn exit" onclick="location.href='registro-iniciodesesioncuenta.php'">✖ Salir</button>
        </div>

        <div class="content">
            <div id="t1">
                <h2 id="form-title" style="text-align:center; font-size:16px; margin-top:0;">NUEVO REGISTRO</h2>
                <form method="POST" id="mainForm">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <label>URL de Imagen</label>
                    <input type="text" name="img" id="in_img" oninput="updatePreview(this.value)" required>
                    <div class="img-container">
                        <span id="placeholder-text" class="img-placeholder">VISTA PREVIA</span>
                        <img id="preview-img" src="">
                    </div>
                    <label>Nombre del Modelo</label>
                    <input type="text" name="n" id="in_n" required>
                    <label>Precio (USD)</label>
                    <input type="number" name="p" id="in_p" required>
                    <label>Motor</label>
                    <input type="text" name="m" id="in_m" required>
                    <label>Descripción</label>
                    <textarea name="desc" id="in_desc" rows="4" required></textarea>
                    <button type="submit" name="save_car" class="btn-save" id="btn-main">Guardar en Catálogo</button>
                    <button type="button" onclick="resetForm()" style="width:100%; background:none; border:none; color:var(--label); font-size:11px; margin-top:10px; cursor:pointer;">Limpiar</button>
                </form>
            </div>

            <div id="t2" class="hidden">
                <h2 style="text-align:center; font-size:16px;">STOCK</h2>
                <?php while($a = $autos->fetch_assoc()): ?>
                    <div class="stock-card">
                        <div style="font-size:13px; margin-bottom:8px; font-weight:bold;"><?= $a['nombre'] ?></div>
                        <div style="display:flex; gap:8px;">
                            <button class="btn-mini" style="background:#d4af37; color:black;" onclick='cargarParaEditar(<?= json_encode($a) ?>)'>Editar ✎</button>
                            <button class="btn-mini" style="background:#8e0000; color:white;" onclick="if(confirm('¿Borrar?')) location.href='dashboard_logic.php?del=<?= $a['id'] ?>'">Quitar 🗑</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div id="t3" class="hidden">
                <h2 style="text-align:center; font-size:16px;">MODERACIÓN</h2>
                <div id="admin-feed"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const comentarios = <?= json_encode($listado_com) ?>;
    function tab(n) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        if(n <= 3) document.getElementById('b'+n).classList.add('active');
        document.getElementById('t1').classList.toggle('hidden', n !== 1);
        document.getElementById('t2').classList.toggle('hidden', n !== 2);
        document.getElementById('t3').classList.toggle('hidden', n !== 3);
        if(n==3) renderFeed();
    }

    function updatePreview(url) {
        const img = document.getElementById('preview-img');
        const txt = document.getElementById('placeholder-text');
        if(url.trim() !== "") { img.src = url; img.style.display = "block"; txt.style.display = "none"; }
        else { img.style.display = "none"; txt.style.display = "block"; }
    }

    function cargarParaEditar(auto) {
        tab(1);
        document.getElementById('form-title').innerText = "EDITANDO AUTO";
        document.getElementById('edit_id').value = auto.id;
        document.getElementById('in_n').value = auto.nombre;
        document.getElementById('in_p').value = auto.precio;
        document.getElementById('in_m').value = auto.motor;
        document.getElementById('in_img').value = auto.imagen;
        document.getElementById('in_desc').value = auto.descripcion;
        document.getElementById('btn-main').innerText = "Actualizar Auto";
        updatePreview(auto.imagen);
    }

    function resetForm() {
        document.getElementById('mainForm').reset();
        document.getElementById('edit_id').value = "";
        document.getElementById('form-title').innerText = "NUEVO REGISTRO";
        document.getElementById('btn-main').innerText = "Guardar en Catálogo";
        updatePreview("");
    }

    function toggleTheme() { document.body.classList.toggle('dark-mode'); }

    function renderFeed() {
        const container = document.getElementById('admin-feed');
        container.innerHTML = comentarios.map(c => `
            <div class="stock-card" style="opacity:${c.oculto == 1 ? '0.5' : '1'}">
                <div style="display:flex; justify-content:space-between; font-size:11px; font-weight:bold;">
                    <span>${c.nombre}</span>
                    <span style="color:#d4af37;">${"★".repeat(c.estrellas)}</span>
                </div>
                <p style="font-size:11px; margin:5px 0;">${c.texto}</p>
                <div style="display:flex; gap:5px; margin-top:8px;">
                    <button class="btn-mini" style="background:#888; color:white;" onclick="toggleVis(${c.id}, ${c.oculto})">${c.oculto == 1 ? '👁️ Ver' : '🙈 Ocultar'}</button>
                    <button class="btn-mini" style="background:#632626; color:white;" onclick="responder(${c.id}, '${c.nombre}')">💬 Responder</button>
                </div>
            </div>
        `).join('');
    }

    function toggleVis(id, st) {
        let fd = new FormData(); fd.append('id', id); fd.append('nest', (st==0?1:0));
        fetch('toggle_comentario.php', { method: 'POST', body: fd }).then(() => location.reload());
    }

    function responder(id, u) {
        let r = prompt("Respuesta para " + u + ":");
        if(r) {
            let fd = new FormData(); fd.append('id', id); fd.append('resp', r);
            fetch('guardar_respuesta.php', { method: 'POST', body: fd }).then(() => location.reload());
        }
    }
</script>
</body>
</html>
