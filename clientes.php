<?php
session_start();
include 'conexion.php';

// Agregar cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'agregar') {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    $stmt = $conn->prepare("INSERT INTO clientes (nombre, direccion, telefono) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $direccion, $telefono);
    $stmt->execute();
    $stmt->close();
}

// Editar cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'editar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, direccion = ?, telefono = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre, $direccion, $telefono, $id);
    $stmt->execute();
    $stmt->close();
}

// Eliminar cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'eliminar') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Obtener todos los clientes
$result = $conn->query("SELECT * FROM clientes");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="clientes-container">
        <h1>Gestión de Clientes</h1>

        <!-- Formulario para agregar cliente -->
        <form method="post" action="clientes.php">
            <input type="hidden" name="action" value="agregar">
            Nombre: <input type="text" name="nombre" required>
            Dirección: <textarea name="direccion" required></textarea>
            Teléfono: <input type="text" name="telefono" required>
            <button type="submit" class="btn">Agregar</button>
        </form>

        <!-- Tabla de clientes -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['direccion']; ?></td>
                        <td><?php echo $row['telefono']; ?></td>
                        <td>
                            <!-- Formulario de edición -->
                            <a href="#" onclick="document.getElementById('edit-<?php echo $row['id']; ?>').style.display='block';" class="btn">Editar</a>
                            <form method="post" action="clientes.php" style="display:inline;">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Ventana de edición para el cliente -->
                    <div id="edit-<?php echo $row['id']; ?>" style="display:none;">
                        <form method="post" action="clientes.php">
                            <input type="hidden" name="action" value="editar">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            Nombre: <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>
                            Dirección: <textarea name="direccion" required><?php echo $row['direccion']; ?></textarea>
                            Teléfono: <input type="text" name="telefono" value="<?php echo $row['telefono']; ?>" required>
                            <button type="submit" class="btn">Actualizar</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn">Volver</a>
    </div>
</body>
</html>
