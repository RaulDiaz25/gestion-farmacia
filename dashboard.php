<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Farmacia</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <div class="menu">
            <a href="medicamentos.php" class="btn">Gestionar Medicamentos</a>
            <a href="clientes.php" class="btn">Gestionar Clientes</a>
            <a href="ventas.php" class="btn">Registrar Ventas</a>
            <a href="logout.php" class="btn">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>
