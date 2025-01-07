<?php
session_start();
include 'conexion.php';

// Registrar venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'registrar') {
    $medicamento_id = $_POST['medicamento_id'];
    $cliente_id = $_POST['cliente_id'];
    $cantidad = $_POST['cantidad'];

    // Actualizar el stock del medicamento
    $conn->query("UPDATE medicamentos SET stock = stock - $cantidad WHERE id = $medicamento_id");

    // Registrar la venta
    $stmt = $conn->prepare("INSERT INTO ventas (medicamento_id, cliente_id, cantidad) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $medicamento_id, $cliente_id, $cantidad);
    $stmt->execute();
    $stmt->close();
}

// Editar venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'editar') {
    $id = $_POST['id'];
    $medicamento_id = $_POST['medicamento_id'];
    $cliente_id = $_POST['cliente_id'];
    $cantidad = $_POST['cantidad'];

    // Actualizar stock del medicamento afectado
    $conn->query("UPDATE medicamentos SET stock = stock + (SELECT cantidad FROM ventas WHERE id = $id) WHERE id = (SELECT medicamento_id FROM ventas WHERE id = $id)");

    // Actualizar la venta
    $stmt = $conn->prepare("UPDATE ventas SET medicamento_id = ?, cliente_id = ?, cantidad = ? WHERE id = ?");
    $stmt->bind_param("iiii", $medicamento_id, $cliente_id, $cantidad, $id);
    $stmt->execute();
    $stmt->close();

    // Actualizar el stock con el nuevo valor
    $conn->query("UPDATE medicamentos SET stock = stock - $cantidad WHERE id = $medicamento_id");
}

// Eliminar venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'eliminar') {
    $id = $_POST['id'];

    // Obtener el medicamento y la cantidad de la venta
    $result = $conn->query("SELECT medicamento_id, cantidad FROM ventas WHERE id = $id");
    $row = $result->fetch_assoc();

    // Devolver el stock del medicamento
    $conn->query("UPDATE medicamentos SET stock = stock + " . $row['cantidad'] . " WHERE id = " . $row['medicamento_id']);

    // Eliminar la venta
    $stmt = $conn->prepare("DELETE FROM ventas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Obtener todos los medicamentos, clientes y ventas
$medicamentos = $conn->query("SELECT * FROM medicamentos");
$clientes = $conn->query("SELECT * FROM clientes");
$ventas = $conn->query("SELECT ventas.id, medicamentos.nombre AS medicamento, clientes.nombre AS cliente, ventas.cantidad, medicamentos.precio FROM ventas JOIN medicamentos ON ventas.medicamento_id = medicamentos.id JOIN clientes ON ventas.cliente_id = clientes.id");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="ventas-container">
        <h1>Registrar Venta</h1>
        <form method="post" action="ventas.php">
            <input type="hidden" name="action" value="registrar">
            Medicamento:
            <select name="medicamento_id" required>
                <?php while ($row = $medicamentos->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?> - $<?php echo $row['precio']; ?></option>
                <?php endwhile; ?>
            </select>
            Cliente:
            <select name="cliente_id" required>
                <?php while ($row = $clientes->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
            Cantidad: <input type="number" name="cantidad" required>
            <button type="submit" class="btn">Registrar Venta</button>
        </form>

        <h2>Ventas Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Medicamento</th>
                    <th>Cliente</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $ventas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['medicamento']; ?></td>
                        <td><?php echo $row['cliente']; ?></td>
                        <td><?php echo $row['cantidad']; ?></td>
                        <td>$<?php echo $row['precio']; ?></td>
                        <td>$<?php echo $row['cantidad'] * $row['precio']; ?></td>
                        <td>
                            <!-- Editar venta -->
                            <a href="#" onclick="document.getElementById('edit-<?php echo $row['id']; ?>').style.display='block';" class="btn">Editar</a>
                            <form method="post" action="ventas.php" style="display:inline;">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Formulario para editar venta -->
                    <div id="edit-<?php echo $row['id']; ?>" style="display:none;">
                        <form method="post" action="ventas.php">
                            <input type="hidden" name="action" value="editar">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            Medicamento:
                            <select name="medicamento_id" required>
                                <?php 
                                // Re-fetch medicamentos to show in the dropdown
                                $medicamentosEdit = $conn->query("SELECT * FROM medicamentos");
                                while ($med = $medicamentosEdit->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $med['id']; ?>" <?php if($med['id'] == $row['medicamento_id']) echo 'selected'; ?>>
                                        <?php echo $med['nombre']; ?> - $<?php echo $med['precio']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            Cliente:
                            <select name="cliente_id" required>
                                <?php 
                                // Re-fetch clientes to show in the dropdown
                                $clientesEdit = $conn->query("SELECT * FROM clientes");
                                while ($cli = $clientesEdit->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $cli['id']; ?>" <?php if($cli['id'] == $row['cliente_id']) echo 'selected'; ?>>
                                        <?php echo $cli['nombre']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            Cantidad: <input type="number" name="cantidad" value="<?php echo $row['cantidad']; ?>" required>
                            <button type="submit" class="btn">Actualizar Venta</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn">Volver</a>
    </div>
</body>
</html>
