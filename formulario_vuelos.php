<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $origen = $_POST['origen'];
    $destino = $_POST['destino'];
    $fecha = $_POST['fecha'];
    $plazas = $_POST['plazas'];
    $precio = $_POST['precio'];

    // Validación en el servidor
    if (empty($origen) || empty($destino) || empty($fecha) || empty($plazas) || empty($precio)) {
        $error = "Todos los campos son obligatorios";
    } else {
        $sql = "INSERT INTO VUELO (origen, destino, fecha, plazas_disponibles, precio) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $origen, $destino, $fecha, $plazas, $precio);
        
        if ($stmt->execute()) {
            $mensaje = "Vuelo registrado exitosamente";
        } else {
            $error = "Error al registrar el vuelo: " . $conn->error;
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
    <title>Registro de Vuelos</title>
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
                        <h2 class="text-center">Registro de Vuelos</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($mensaje)): ?>
                            <div class="alert alert-success"><?php echo $mensaje; ?></div>
                        <?php endif; ?>

                        <form id="formVuelo" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validarFormulario()">
                            <div class="mb-3">
                                <label for="origen" class="form-label">Origen:</label>
                                <input type="text" class="form-control" id="origen" name="origen" required>
                            </div>

                            <div class="mb-3">
                                <label for="destino" class="form-label">Destino:</label>
                                <input type="text" class="form-control" id="destino" name="destino" required>
                            </div>

                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha y Hora:</label>
                                <input type="datetime-local" class="form-control" id="fecha" name="fecha" required>
                            </div>

                            <div class="mb-3">
                                <label for="plazas" class="form-label">Plazas Disponibles:</label>
                                <input type="number" class="form-control" id="plazas" name="plazas" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio (CLP):</label>
                                <input type="number" class="form-control" id="precio" name="precio" min="0" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Registrar Vuelo</button>
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
        const origen = document.getElementById('origen').value.trim();
        const destino = document.getElementById('destino').value.trim();
        const fecha = document.getElementById('fecha').value;
        const plazas = document.getElementById('plazas').value;
        const precio = document.getElementById('precio').value;

        // Validar campos vacíos
        if (!origen || !destino || !fecha || !plazas || !precio) {
            alert('Todos los campos son obligatorios');
            return false;
        }

        // Validar que origen y destino sean diferentes
        if (origen === destino) {
            alert('El origen y destino no pueden ser iguales');
            return false;
        }

        // Validar que la fecha sea futura
        const fechaVuelo = new Date(fecha);
        const ahora = new Date();
        if (fechaVuelo <= ahora) {
            alert('La fecha del vuelo debe ser futura');
            return false;
        }

        // Validar plazas
        if (plazas < 1) {
            alert('Debe haber al menos 1 plaza disponible');
            return false;
        }

        // Validar precio
        if (precio < 0) {
            alert('El precio no puede ser negativo');
            return false;
        }

        return true;
    }
    </script>
</body>
</html> 