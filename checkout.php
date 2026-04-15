<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vintage_motors");

// 1. CONSULTA UNIFICADA: Traemos el carro de favoritos Y su precio real del inventario
// Esto evita que el precio salga en $0 si no estaba en la lista manual
$res = $conn->query("
    SELECT f.carro_id, i.precio, i.nombre 
    FROM favoritos f 
    JOIN inventario i ON (f.carro_id = i.id OR f.carro_id = i.nombre)
");

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Caja | Vintage Motors</title>
    <style>
        body { background-color: #632626; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: Arial, sans-serif; }
        .phone-border { background-color: #1a1a1a; width: 360px; height: 740px; border-radius: 45px; padding: 12px; position: relative; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.7); }
        .notch { width: 150px; height: 25px; background-color: #1a1a1a; position: absolute; top: 0; left: 50%; transform: translateX(-50%); border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; z-index: 10; }
        .screen { background-color: #d1d1d1; flex: 1; border-radius: 35px; overflow: hidden; display: flex; flex-direction: column; position: relative; }
        .scrollable-content { flex: 1; overflow-y: auto; padding: 45px 15px 85px; box-sizing: border-box; }
        .scrollable-content::-webkit-scrollbar { width: 0; }

        h1 { color: #632626; font-size: 20px; text-align: center; text-transform: uppercase; margin-bottom: 20px; }

        /* TARJETA VISUAL */
        .credit-card { 
            background: linear-gradient(135deg, #1a1a1a 0%, #4a3333 100%); 
            color: white; padding: 22px; border-radius: 18px; margin-bottom: 20px; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.4); font-family: 'Courier New', monospace; 
            border: 1px solid rgba(255,255,255,0.2); transition: 0.5s ease;
        }
        .card-logo { width: 60px; height: auto; opacity: 0.8; }
        .card-number { font-size: 17px; letter-spacing: 2px; margin: 20px 0; display: block; }

        /* RESUMEN */
        .resumen-box { background: white; border-radius: 15px; padding: 15px; border: 1px solid #bcbcbc; margin-bottom: 20px; }
        .item-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #eee; font-size: 12px; }
        .total-row { display: flex; justify-content: space-between; padding-top: 10px; font-weight: bold; color: #632626; font-size: 17px; }

        label { font-size: 11px; font-weight: bold; color: #555; display: block; margin: 10px 0 5px; text-transform: uppercase; }
        input, select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; margin-bottom: 10px; }

        .btn-pagar { background-color: #28a745; color: white; border: none; padding: 16px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; text-transform: uppercase; margin-top: 10px; }
        
        .tab-bar { position: absolute; bottom: 0; width: 100%; height: 75px; background: #f8f8f8; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #ccc; border-radius: 0 0 35px 35px; }
        .tab-item { text-align: center; color: #888; text-decoration: none; font-size: 11px; }
        .tab-item.active { color: #632626; font-weight: bold; }
    </style>
</head>
<body>

<div class="phone-border">
    <div class="notch"></div>
    <div class="screen">
        <div class="scrollable-content">
            <h1>Pago Seguro</h1>

            <!-- TARJETA VISUAL SIMULADA -->
            <div class="credit-card" id="card-visual">
                <div style="display:flex; justify-content: space-between; align-items: center;">
                    <small id="card-type-text" style="font-weight: bold;">VINTAGE MEMBER</small>
                    <img src="img/logo.png" class="card-logo">
                </div>
                <span class="card-number" id="v-num">XXXX XXXX XXXX XXXX</span>
                <div style="display:flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <div style="font-size: 9px; opacity: 0.7;">TITULAR</div>
                        <div style="font-size: 13px;" id="v-name"><?= strtoupper($_SESSION['user_name'] ?? 'PILOTO') ?></div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 9px; opacity: 0.7;">VENCE</div>
                        <div style="font-size: 12px;">12/29</div>
                    </div>
                </div>
            </div>

            <!-- RESUMEN DE COMPRA CORREGIDO -->
            <div class="resumen-box">
                <?php if ($res->num_rows > 0): ?>
                    <?php while($row = $res->fetch_assoc()): 
                        $p = $row['precio'];
                        $total += $p;
                    ?>
                        <div class="item-row">
                            <span><?= strtoupper($row['nombre']) ?></span>
                            <strong>$<?= number_format($p) ?></strong>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align:center; font-size:12px; color:#888;">Tu garage de favoritos está vacío.</p>
                <?php endif; ?>

                <div class="total-row">
                    <span>TOTAL:</span>
                    <span>$<?= number_format($total) ?> USD</span>
                </div>
            </div>

            <form action="finalizar_compra.php" method="POST" id="form-pago">
                <label>Método de Pago:</label>
                <select id="metodoPago" name="metodo_pago" onchange="cambiarEstiloTarjeta()">
                    <option value="credito">Tarjeta de Crédito</option>
                    <option value="debito">Tarjeta de Débito</option>
                    <option value="amex">American Express</option>
                    <option value="paypal">PayPal</option>
                    <option value="coppel">Coppel (Abonos)</option>
                </select>

                <label>Número de Tarjeta:</label>
                <input type="text" name="numero_tarjeta" id="c-number" placeholder="4000 1234 5678 9010" maxlength="19" oninput="actCard()" required>

                <button type="button" class="btn-pagar" onclick="confirmarSimulacion()">Finalizar Compra</button>
            </form>

            <button onclick="location.href='favoritos.php'" style="width:100%; background:none; border:none; color:#632626; margin-top:15px; cursor:pointer; font-size:12px;">← Volver</button>
        </div>

        <nav class="tab-bar">
            <a href="inicio.php" class="tab-item">🏠<br>Inicio</a>
            <a href="catalogo.php" class="tab-item">🏎️<br>Catálogo</a>
            <a href="favoritos.php" class="tab-item active">★<br>Caja</a>
            <a href="registro-iniciodesesioncuenta.php" class="tab-item">👤<br>Perfil</a>
        </nav>
    </div>
</div>

<script>
    function actCard() {
        let n = document.getElementById('c-number').value;
        document.getElementById('v-num').innerText = n || "XXXX XXXX XXXX XXXX";
    }

    function cambiarEstiloTarjeta() {
        let m = document.getElementById('metodoPago').value;
        let card = document.getElementById('card-visual');
        let text = document.getElementById('card-type-text');
        card.style.filter = "none";
        
        if (m === 'paypal') { card.style.filter = "hue-rotate(200deg) brightness(1.2)"; text.innerText = "PAYPAL"; }
        else if (m === 'coppel') { card.style.filter = "hue-rotate(40deg) saturate(2) brightness(1.5)"; text.innerText = "CLIENTE COPPEL"; }
        else if (m === 'amex') { card.style.filter = "grayscale(1) brightness(1.5)"; text.innerText = "AMEX"; }
        else { text.innerText = "VINTAGE CARD"; }
    }

    function confirmarSimulacion() {
        let tarjeta = document.getElementById('c-number').value;
        if(tarjeta.length < 10) { alert("Número de tarjeta inválido."); return; }
        
        if (confirm("¿Confirmar compra por $<?= number_format($total) ?> USD?")) {
            document.getElementById('form-pago').submit();
        }
    }
</script>
</body>
</html>
