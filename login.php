<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM usuarios WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Farmacia</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <form method="post" action="login.php">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Ingresar</button>
        </form>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
