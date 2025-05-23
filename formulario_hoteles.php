<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    $habitaciones = $_POST['habitaciones'];
    $tarifa = $_POST['tarifa'];

    // Validación en el servidor
    if (empty($nombre) || empty($ubicacion) || empty($habitaciones) || empty($tarifa)) {
        $error = "Todos los campos son obligatorios";
    } else {
        $sql = "INSERT INTO HOTEL (nombre, ubicacion, habitaciones_disponibles, tarifa_noche) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $nombre, $ubicacion, $habitaciones, $tarifa);
        
        if ($stmt->execute()) {
            $mensaje = "Hotel registrado exitosamente";
        } else {
            $error = "Error al registrar el hotel: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Hoteles</title>
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
                        <h2 class="text-center">Registro de Hoteles</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($mensaje)): ?>
                            <div class="alert alert-success"><?php echo $mensaje; ?></div>
                        <?php endif; ?>

                        <form id="formHotel" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validarFormulario()">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Hotel:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label for="ubicacion" class="form-label">Ubicación:</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                            </div>

                            <div class="mb-3">
                                <label for="habitaciones" class="form-label">Habitaciones Disponibles:</label>
                                <input type="number" class="form-control" id="habitaciones" name="habitaciones" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="tarifa" class="form-label">Tarifa por Noche (CLP):</label>
                                <input type="number" class="form-control" id="tarifa" name="tarifa" min="0" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Registrar Hotel</button>
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
        const nombre = document.getElementById('nombre').value.trim();
        const ubicacion = document.getElementById('ubicacion').value.trim();
        const habitaciones = document.getElementById('habitaciones').value;
        const tarifa = document.getElementById('tarifa').value;

        // Validar campos vacíos
        if (!nombre || !ubicacion || !habitaciones || !tarifa) {
            alert('Todos los campos son obligatorios');
            return false;
        }

        // Validar longitud del nombre
        if (nombre.length < 3) {
            alert('El nombre del hotel debe tener al menos 3 caracteres');
            return false;
        }

        // Validar habitaciones
        if (habitaciones < 1) {
            alert('Debe haber al menos 1 habitación disponible');
            return false;
        }

        // Validar tarifa
        if (tarifa <= 0) {
            alert('La tarifa no puede ser 0 o negativa');
            return false;
        }


        return true;
    }
    </script>
</body>
</html> 