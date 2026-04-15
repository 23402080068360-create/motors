<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

if (isset($_SESSION['user_id'])) {
    $nombre = $_SESSION['user_name'];
    $rol = $_SESSION['rol'] ?? 'user'; 
    $pedidos = $conn->query("SELECT * FROM pedidos WHERE usuario_nombre = '$nombre' ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | Vintage Motors</title>
    <style>
        body { background-color: #632626; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .phone-border { background-color: #1a1a1a; width: 360px; height: 740px; border-radius: 45px; padding: 12px; position: relative; display: flex; flex-direction: column; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .notch { width: 150px; height: 25px; background-color: #1a1a1a; position: absolute; top: 0; left: 50%; transform: translateX(-50%); border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; z-index: 10; }
        .screen { background-color: #d1d1d1; flex: 1; border-radius: 35px; overflow: hidden; display: flex; flex-direction: column; position: relative; }
        .content { flex: 1; padding: 50px 20px 80px 20px; overflow-y: auto; }
        .content::-webkit-scrollbar { width: 0px; }

        .avatar { width: 80px; height: 80px; background: #632626; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 35px; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .section-title { border-bottom: 2px solid #632626; color: #632626; font-size: 14px; font-weight: bold; margin: 25px 0 15px; padding-bottom: 5px; text-transform: uppercase; }
        .order-card { background: white; padding: 12px; border-radius: 12px; margin-bottom: 10px; border-left: 6px solid #28a745; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .btn-admin { background: #d4af37; color: black; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; margin-bottom: 15px; }
        .btn-logout { background: #4a3333; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 15px; }

        .tab-bar { position: absolute; bottom: 0; width: 100%; height: 70px; background: #f8f8f8; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #ccc; border-radius: 0 0 35px 35px; }
        .tab-item { text-align: center; color: #888; text-decoration: none; font-size: 10px; }
        .tab-item.active { color: #632626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="phone-border">
        <div class="notch"></div>
        <div class="screen">
            <div class="content">
                <div style="text-align: center;">
                    <div class="avatar"><?= substr($nombre, 0, 1) ?></div>
                    <h2 style="color: #1a1a1a; margin: 0;"><?= $nombre ?></h2>
                    <span style="color: #632626; font-size: 11px; font-weight: bold;">USUARIO <?= strtoupper($rol) ?></span>
                </div>

                <?php if ($rol == 'admin'): ?>
                    <div class="section-title" style="color:#d4af37; border-color:#d4af37;">Admin Dashboard</div>
                    <button class="btn-admin" onclick="location.href='dashboard.php'">⚙️ CONFIGURACIÓN MAESTRA</button>
                <?php endif; ?>

                <div class="section-title">Mis Compras</div>
                <?php if ($pedidos->num_rows > 0): ?>
                    <?php while($p = $pedidos->fetch_assoc()): ?>
                        <div class="order-card">
                            <h4 style="margin:0; font-size:14px;"><?= str_replace('-', ' ', $p['carro_id']) ?></h4>
                            <span style="color:#632626; font-weight:bold;">$<?= number_format($p['precio']) ?> USD</span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align:center; color:#888; font-size:12px;">No hay pedidos registrados.</p>
                <?php endif; ?>

                <button class="btn-logout" onclick="location.href='logout.php'">CERRAR SESIÓN</button>
            </div>

            <nav class="tab-bar">
                <a href="inicio.php" class="tab-item">🏠<br>Inicio</a>
                <a href="catalogo.php" class="tab-item">🏎️<br>Catálogo</a>
                <a href="favoritos.php" class="tab-item">★<br>Favoritos</a>
                <a href="registro-iniciodesesioncuenta.php" class="tab-item active">👤<br>Perfil</a>
            </nav>
        </div>
    </div>
</body>
</html>
<?php 
} else { 
?>
<!DOCTYPE html> 
<html> 
<head> 
    <meta charset="UTF-8"> 
    <title>Acceso | Vintage Motors</title> 
    <style> 
        body { background-color: #632626; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: Arial; } 
        .phone-border { background-color: #1a1a1a; width: 360px; height: 740px; border-radius: 45px; padding: 12px; position: relative; display: flex; flex-direction: column; } 
        .screen { background-color: #D9D9D9; flex: 1; border-radius: 35px; padding: 50px 20px; box-sizing: border-box; display: flex; flex-direction: column; overflow: hidden; } 
        h1 { color: #8e0000; text-align: center; text-transform: uppercase; font-size: 22px; margin-bottom: 20px; } 
        
        /* CONTENEDOR PARA EL OJO */
        .input-group { position: relative; width: 100%; margin: 8px 0; }
        input { width: 100%; padding: 12px; padding-right: 40px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; } 
        .toggle-pass { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #5d4037; font-size: 18px; user-select: none; }

        button { width: 100%; padding: 12px; background: #5d4037; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; } 
        .hidden { display: none; }
        .links { text-align:center; font-size:12px; margin-top:15px; color:#5d4037; }
        .links a { color:#8e0000; font-weight:bold; cursor:pointer; text-decoration:underline; }
    </style> 
</head> 
<body> 
    <div class="phone-border"> 
        <div class="notch"></div>
        <div class="screen"> 
            <h1>Vintage Motors</h1> 
            
            <div id="login"> 
                <h3 style="text-align: center; color: #5d4037;">INGRESAR</h3> 
                <form id="fL">
                    <input type="email" name="email" placeholder="Correo electrónico" required> 
                    <div class="input-group">
                        <input type="password" name="pass" id="passLogin" placeholder="Contraseña" required> 
                        <span class="toggle-pass" onclick="toggle('passLogin')">𓂀️</span>
                    </div>
                    <button type="submit">ENTRAR</button> 
                </form>
                <div class="links">¿No tienes cuenta? <a onclick="show('register')">Regístrate aquí</a></div> 
            </div> 

            <div id="register" class="hidden"> 
                <h3 style="text-align: center; color: #5d4037;">REGISTRO</h3> 
                <form id="fR">
                    <input type="text" name="nombre" placeholder="Nombre completo" required> 
                    <input type="email" name="email" placeholder="Correo electrónico" required> 
                    <div class="input-group">
                        <input type="password" name="pass1" id="p1" placeholder="Contraseña" required> 
                        <span class="toggle-pass" onclick="toggle('p1')">𓂀️</span>
                    </div>
                    <div class="input-group">
                        <input type="password" name="pass2" id="p2" placeholder="Confirmar contraseña" required> 
                        <span class="toggle-pass" onclick="toggle('p2')">𓂀️</span>
                    </div>
                    <button type="submit">CREAR CUENTA</button> 
                </form>
                <div class="links">¿Ya tienes cuenta? <a onclick="show('login')">Inicia sesión</a></div> 
            </div> 
        </div> 
    </div> 
    <script> 
        function show(v) { 
            document.getElementById('login').classList.toggle('hidden', v !== 'login'); 
            document.getElementById('register').classList.toggle('hidden', v !== 'register'); 
        }

        // FUNCIÓN PARA MOSTRAR/OCULTAR CONTRASEÑA
        function toggle(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }

        document.getElementById('fR').onsubmit = function(e) {
            e.preventDefault();
            if(document.getElementById('p1').value !== document.getElementById('p2').value) {
                alert("Error: Las contraseñas no coinciden."); return;
            }
            fetch('auth.php?action=register', { method: 'POST', body: new FormData(this) })
            .then(res => res.text()).then(res => { alert(res); if(res.includes("exitoso")) show('login'); });
        };

        document.getElementById('fL').onsubmit = function(e) {
            e.preventDefault();
            fetch('auth.php?action=login', { method: 'POST', body: new FormData(this) })
            .then(res => res.text()).then(res => { if(res.includes("OK")) window.location.reload(); else alert(res); });
        };
    </script> 
</body> 
</html>
<?php } ?>
