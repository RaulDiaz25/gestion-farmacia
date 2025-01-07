<?php
session_start();
include 'conexion.php';

// Agregar medicamento
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'agregar') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $caducidad = $_POST['caducidad'];

    $stmt = $conn->prepare("INSERT INTO medicamentos (nombre, descripcion, precio, stock, caducidad) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $nombre, $descripcion, $precio, $stock, $caducidad);
    $stmt->execute();
    $stmt->close();
}

// Editar medicamento
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'editar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $caducidad = $_POST['caducidad'];

    $stmt = $conn->prepare("UPDATE medicamentos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, caducidad = ? WHERE id = ?");
    $stmt->bind_param("ssdssi", $nombre, $descripcion, $precio, $stock, $caducidad, $id);
    $stmt->execute();
    $stmt->close();
}

// Eliminar medicamento
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'eliminar') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM medicamentos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Obtener todos los medicamentos
$result = $conn->query("SELECT * FROM medicamentos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="medicamentos-container">
        <h1>Gestión de Medicamentos</h1>

        <!-- Formulario para agregar medicamento -->
        <form method="post" action="medicamentos.php">
            <input type="hidden" name="action" value="agregar">
            Nombre: <input type="text" name="nombre" required>
            Descripción: <textarea name="descripcion" required></textarea>
            Precio: <input type="number" step="0.01" name="precio" required>
            Stock: <input type="number" name="stock" required>
            Caducidad: <input type="date" name="caducidad" required>
            <button type="submit" class="btn">Agregar</button>
        </form>

        <!-- Tabla de medicamentos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Caducidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['descripcion']; ?></td>
                        <td><?php echo $row['precio']; ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td><?php echo $row['caducidad']; ?></td>
                        <td>
                            <!-- Formulario de edición -->
                            <a href="#" onclick="document.getElementById('edit-<?php echo $row['id']; ?>').style.display='block';" class="btn">Editar</a>
                            <form method="post" action="medicamentos.php" style="display:inline;">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Ventana de edición para el medicamento -->
                    <div id="edit-<?php echo $row['id']; ?>" style="display:none;">
                        <form method="post" action="medicamentos.php">
                            <input type="hidden" name="action" value="editar">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            Nombre: <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>
                            Descripción: <textarea name="descripcion" required><?php echo $row['descripcion']; ?></textarea>
                            Precio: <input type="number" step="0.01" name="precio" value="<?php echo $row['precio']; ?>" required>
                            Stock: <input type="number" name="stock" value="<?php echo $row['stock']; ?>" required>
                            Caducidad: <input type="date" name="caducidad" value="<?php echo $row['caducidad']; ?>" required>
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
