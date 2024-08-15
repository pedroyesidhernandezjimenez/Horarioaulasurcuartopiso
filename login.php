<?php
session_start();

// Configuración de la conexión a la base de datos
$servername = "localhost";  // Cambia esto si tu servidor de base de datos tiene un nombre diferente
$username= "root"; // Reemplaza con tu nombre de usuario de MySQL
$password= ""; // Reemplaza con tu contraseña de MySQL
$database = "horarios_db"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("La conexión a la base de datos falló: " . $conn->connect_error);
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Si el usuario ya está autenticado, redirigir a la página de administración
    header('Location: admin_horarios.php');
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta SQL para buscar el usuario y contraseña en la base de datos
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        // Redirigir a la página de administración después del inicio de sesión exitoso
        header('Location: admin_horarios.php');
        exit;
    } else {
        $error = "Nombre de usuario o contraseña incorrectos.";
    }
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar sesión</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type='text/css'>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            margin-top: 100px;
        }
        .login-box {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0px 0px 10px #ccc;
        }
        .login-box h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .login-box input[type="text"], 
        .login-box input[type="password"] {
            width: calc(100% - 20px);
            padding: 8px 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .login-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 3px;
        }
        .login-box input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Iniciar sesión</h2>
        <form method="post" action="login.php">
            <input type="text" name="username" placeholder="Nombre de usuario" required><br><br>
            <input type="password" name="password" placeholder="Contraseña" required><br><br>
            <input type="submit" name="login" value="Iniciar sesión">
        </form>
        <?php
        if (isset($error)) {
            echo '<div class="error-message">' . $error . '</div>';
        }
        ?>
    </div>
</body>
</html>
