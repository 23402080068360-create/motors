<?php
session_start();
// 1. CONEXIÓN A LA BASE DE DATOS
$conn = new mysqli("localhost", "root", "", "vintage_motors");
if ($conn->connect_error) { die("Error de conexión: " . $conn->connect_error); }

// 2. PROCESAMIENTO DE LA COMPRA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['datos_compra'])) {
    $nombre_usuario = $_SESSION['user_name'] ?? 'CLIENTE PREMIUM';
    
    // Buscamos lo que hay en favoritos
    $res_favs = $conn->query("SELECT carro_id FROM favoritos");
    
    $items = [];
    $total_final = 0;

    if ($res_favs && $res_favs->num_rows > 0) {
        $conn->begin_transaction();
        try {
            while($f = $res_favs->fetch_assoc()) {
                $id_buscado = $f['carro_id'];

                // BUSCAMOS PRECIO Y NOMBRE REAL EN EL INVENTARIO
                // Esto evita el $0 porque busca directamente en tu tabla de stock
                $stmt = $conn->prepare("SELECT nombre, precio FROM inventario WHERE id = ? OR nombre = ? LIMIT 1");
                $stmt->bind_param("ss", $id_buscado, $id_buscado);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $auto_db = $resultado->fetch_assoc();

                if ($auto_db) {
                    $nombre_real = $auto_db['nombre'];
                    $precio_real = $auto_db['precio'];
                } else {
                    // Si por alguna razón no lo encuentra en inventario (ej. ID '10' no existe)
                    $nombre_real = "UNIDAD ESPECIAL ($id_buscado)";
                    $precio_real = 0; 
                }

                $total_final += $precio_real;
                $items[] = ['nombre' => strtoupper($nombre_real), 'precio' => $precio_real];

                // INSERTAR EN LA TABLA DE PEDIDOS PARA EL HISTORIAL DEL PERFIL
                $ins = $conn->prepare("INSERT INTO pedidos (usuario_nombre, carro_id, precio, estado, fecha) VALUES (?, ?, ?, 'Entregado', NOW())");
                $ins->bind_param("ssd", $nombre_usuario, $nombre_real, $precio_real);
                $ins->execute();
            }

            // GUARDAR DATOS EN SESIÓN PARA EL COMPROBANTE VISUAL
            $_SESSION['datos_compra'] = [
                'folio' => "VM-" . date('Y') . "-" . rand(1000, 9999),
                'fecha' => date('d/m/Y H:i'),
                'cliente' => $nombre_usuario,
                'items' => $items,
                'total' => $total_final
            ];

            // VACIAR CARRITO
            $conn->query("DELETE FROM favoritos");
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            die("Error en la transacción: " . $e->getMessage());
        }
    }
}

// 3. VALIDACIÓN DE VISTA
$d = $_SESSION['datos_compra'] ?? null;
if (!$d) { header("Location: catalogo.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Propiedad | Vintage Motors</title>
    <style>
        * { font-family: Arial, sans-serif !important; color: #000 !important; font-weight: bold !important; }
        body { background-color: #632626 !important; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        
        .phone-border { background-color: #1a1a1a !important; width: 380px; height: 820px; border-radius: 45px; padding: 12px; position: relative; box-shadow: 0 30px 60px rgba(0,0,0,0.8); display: flex; flex-direction: column; }
        .notch { background-color: #1a1a1a !important; width: 150px; height: 25px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; }
        .screen { background-color: #fff !important; flex: 1; border-radius: 35px; overflow-y: auto; padding: 30px 20px; box-sizing: border-box; display: flex; flex-direction: column; }
        
        .doc-header { display: flex; justify-content: space-between; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .company-info h1 { font-size: 18px; margin: 0; }
        .company-info p { font-size: 9px; margin: 2px 0; text-transform: uppercase; }
        .doc-type { text-align: right; }

        .client-section { background: #eee !important; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #000; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { font-size: 11px; text-align: left; border-bottom: 2px solid #000; padding: 8px 0; }
        .items-table td { font-size: 12px; padding: 10px 0; border-bottom: 1px solid #000; }
        
        .total-box { text-align: right; border-top: 2px solid #000; padding-top: 10px; font-size: 20px; }
        .footer-note { margin-top: auto; font-size: 10px; text-align: center; border-top: 2px solid #000; padding-top: 15px; }

        .no-print { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }
        .btn-p { background: #632626 !important; color: #fff !important; border: none; padding: 14px; border-radius: 12px; cursor: pointer; text-transform: uppercase; text-align: center; text-decoration: none; }
        .btn-s { background: #000 !important; color: #fff !important; text-decoration: none; text-align: center; padding: 14px; border-radius: 12px; }

        @media print {
            .no-print, .notch { display: none !important; }
            .phone-border { background: none !important; box-shadow: none !important; width: 100% !important; }
            .screen { border-radius: 0 !important; border: none !important; }
        }
    </style>
</head>
<body>

<div class="phone-border">
    <div class="notch"></div>
    <div class="screen">
        <div class="doc-header">
            <div class="company-info">
                <h1>VINTAGE MOTORS S.A.</h1>
                <p>RFC: VIM-980721-MOT</p>
                <p>Mexicali, B.C. AV. Lazaro Cardenas</p>
                <p>+52 686 244 2834</p>
            </div>
            <div class="doc-type">
                <h2 style="margin:0; font-size:14px;">COMPROBANTE</h2>
                <span>ID: <?= $d['folio'] ?></span><br>
                <small><?= $d['fecha'] ?></small>
            </div>
        </div>

        <div class="client-section">
            <small>TITULAR DE LA UNIDAD:</small>
            <p style="margin:5px 0; font-size:16px;"><?= htmlspecialchars($d['cliente']) ?></p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>DESCRIPCIÓN</th>
                    <th style="text-align: right;">MONTO (USD)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($d['items'] as $item): ?>
                <tr>
                    <td>1x <?= $item['nombre'] ?></td>
                    <td style="text-align: right;">$<?= number_format($item['precio'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-box">
            TOTAL: $<?= number_format($d['total'], 2) ?>
        </div>

        <div class="footer-note">
            Este documento certifica la propiedad legal de las unidades.<br>
            Gracias por confiar en Vintage Motors.
        </div>

        <div class="no-print">
            <button class="btn-p" onclick="window.print()">IMPRIMIR TICKET</button>
            <a href="registro-iniciodesesioncuenta.php" class="btn-s">VOLVER AL PERFIL</a>
        </div>
    </div>
</div>

</body>
</html>
