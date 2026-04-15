<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vintage Motors - Bienvenido</title>
    <style>
        :root {
            --vintage-cream: #D9D9D9; 
            --wine-red: #632626; 
            --racing-red: #8e0000;
        }

        body {
            background-color: var(--wine-red);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .phone-mockup {
            width: 360px;
            height: 740px;
            background: var(--vintage-cream); 
            border: 12px solid #1a1a1a;
            border-radius: 45px;
            position: relative;
            box-shadow: 0 25px 50px rgba(0,0,0,0.7);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 60px; 
            box-sizing: border-box;
        }

        .notch { 
            width: 150px; 
            height: 25px; 
            background-color: #1a1a1a; 
            position: absolute; 
            top: 0; 
            left: 50%; 
            transform: translateX(-50%); 
            border-bottom-left-radius: 15px; 
            border-bottom-right-radius: 15px; 
            z-index: 10; 
        }

        .header-section {
            text-align: center;
            width: 100%;
        }

        .header-title {
            color: var(--wine-red);
            font-size: 1.8rem;
            text-transform: uppercase;
            margin: 0;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .header-subtitle {
            color: #333;
            font-size: 0.9rem;
            margin: 10px 0;
            font-style: italic;
        }

        .header-line {
            width: 80%;
            height: 1.5px;
            background-color: #4a3333;
            margin: 10px auto;
        }

        .logo-container {
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-top: auto;
            margin-bottom: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            cursor: pointer;
        }

        .logo-container:hover {
            transform: scale(1.1);
        }

        .main-logo {
            width: 75%;
            max-width: 250px;
            height: auto;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
        }

        .welcome-text {
            color: #632626;
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 60px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.4; }
            50% { opacity: 1; }
            100% { opacity: 0.4; }
        }
    </style>
</head>
<body>

    <div class="phone-mockup">
        <div class="notch"></div>

        <div class="header-section">
            <h1 class="header-title">VINTAGE MOTORS</h1>
            <p class="header-subtitle">Estilo Atemporal sobre Ruedas</p>
            <div class="header-line"></div>
        </div>

        <!-- AQUÍ ESTÁ EL CAMBIO CLAVE: .php en lugar de .html -->
        <a href="registro-iniciodesesioncuenta.php" class="logo-container">
            <img src="img/logo.png" alt="Vintage Motors Logo" class="main-logo">
        </a>

        <p class="welcome-text">Presiona para entrar</p>
    </div>

</body>
</html>
