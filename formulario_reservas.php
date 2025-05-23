<?php
require_once 'conexion.php';

// Obtener lista de vuelos disponibles
$sql_vuelos = "SELECT id_vuelo, origen, destino, fecha, precio FROM VUELO WHERE plazas_disponibles > 0";
$result_vuelos = $conn->query($sql_vuelos);

// Obtener lista de hoteles disponibles
$sql_hoteles = "SELECT id_hotel, nombre, ubicacion, tarifa_noche FROM HOTEL WHERE habitaciones_disponibles > 0";
$result_hoteles = $conn->query($sql_hoteles);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente = $_POST['id_cliente'];
    $id_vuelo = $_POST['id_vuelo'];
    $id_hotel = $_POST['id_hotel'];
    $fecha_reserva = date('Y-m-d H:i:s');

    // Validación en el servidor
    if (empty($id_cliente) || empty($id_vuelo) || empty($id_hotel)) {
        $error = "Todos los campos son obligatorios";
    } else {
        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Insertar la reserva
            $sql = "INSERT INTO RESERVA (id_cliente, fecha_reserva, id_vuelo, id_hotel) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isii", $id_cliente, $fecha_reserva, $id_vuelo, $id_hotel);
            $stmt->execute();

            // Actualizar plazas disponibles en el vuelo
            $sql_update_vuelo = "UPDATE VUELO SET plazas_disponibles = plazas_disponibles - 1 
                               WHERE id_vuelo = ?";
            $stmt = $conn->prepare($sql_update_vuelo);
            $stmt->bind_param("i", $id_vuelo);
            $stmt->execute();

            // Actualizar habitaciones disponibles en el hotel
            $sql_update_hotel = "UPDATE HOTEL SET habitaciones_disponibles = habitaciones_disponibles - 1 
                               WHERE id_hotel = ?";
            $stmt = $conn->prepare($sql_update_hotel);
            $stmt->bind_param("i", $id_hotel);
            $stmt->execute();

            // Confirmar transacción
            $conn->commit();
            $mensaje = "Reserva registrada exitosamente";
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conn->rollback();
            $error = "Error al registrar la reserva: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Reservas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Registro de Reservas</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($mensaje)): ?>
                            <div class="alert alert-success"><?php echo $mensaje; ?></div>
                        <?php endif; ?>

                        <form id="formReserva" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validarFormulario()">
                            <div class="mb-3">
                                <label for="id_cliente" class="form-label">ID del Cliente:</label>
                                <input type="number" class="form-control" id="id_cliente" name="id_cliente" required>
                            </div>

                            <div class="mb-3">
                                <label for="id_vuelo" class="form-label">Vuelo:</label>
                                <select class="form-select" id="id_vuelo" name="id_vuelo" required>
                                    <option value="">Seleccione un vuelo</option>
                                    <?php
                                    if ($result_vuelos->num_rows > 0) {
                                        while($row = $result_vuelos->fetch_assoc()) {
                                            echo "<option value='" . $row['id_vuelo'] . "'>" . 
                                                 $row['origen'] . " a " . $row['destino'] . 
                                                 " - " . date('d/m/Y H:i', strtotime($row['fecha'])) . 
                                                 " - $" . number_format($row['precio'], 0, ',', '.') . 
                                                 "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="id_hotel" class="form-label">Hotel:</label>
                                <select class="form-select" id="id_hotel" name="id_hotel" required>
                                    <option value="">Seleccione un hotel</option>
                                    <?php
                                    if ($result_hoteles->num_rows > 0) {
                                        while($row = $result_hoteles->fetch_assoc()) {
                                            echo "<option value='" . $row['id_hotel'] . "'>" . 
                                                 $row['nombre'] . " - " . $row['ubicacion'] . 
                                                 " - $" . number_format($row['tarifa_noche'], 0, ',', '.') . 
                                                 " por noche</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Registrar Reserva</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function validarFormulario() {
        const idCliente = document.getElementById('id_cliente').value;
        const idVuelo = document.getElementById('id_vuelo').value;
        const idHotel = document.getElementById('id_hotel').value;

        // Validar campos vacíos
        if (!idCliente || !idVuelo || !idHotel) {
            alert('Todos los campos son obligatorios');
            return false;
        }

        // Validar ID del cliente
        if (idCliente < 1) {
            alert('El ID del cliente debe ser un número positivo');
            return false;
        }

        return true;
    }
    </script>
</body>
</html> 