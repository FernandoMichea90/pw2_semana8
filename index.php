<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $origen = $_POST['origen'];
    $destino = $_POST['destino'];
    $fecha = $_POST['fecha'];

    $sql = "SELECT * FROM VUELO WHERE origen = ? AND destino = ? AND DATE(fecha) = ? AND plazas_disponibles > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $origen, $destino, $fecha);
    $stmt->execute();
    $resultado_vuelos = $stmt->get_result();
}

// Consulta para obtener hoteles con más de una reserva
$sql_hoteles_populares = "
    SELECT h.id_hotel, h.nombre, h.ubicacion, h.tarifa_noche, COUNT(r.id_reserva) as total_reservas
    FROM HOTEL h
    INNER JOIN RESERVA r ON h.id_hotel = r.id_hotel
    GROUP BY h.id_hotel
    HAVING COUNT(r.id_reserva) > 1
    ORDER BY total_reservas DESC";

$result_hoteles = $conn->query($sql_hoteles_populares);

// Consulta para obtener todas las reservas con detalles
$sql_reservas = "
    SELECT r.id_reserva, r.id_cliente, r.fecha_reserva,
           v.origen, v.destino, v.fecha as fecha_vuelo,
           h.nombre as nombre_hotel, h.ubicacion
    FROM RESERVA r
    INNER JOIN VUELO v ON r.id_vuelo = v.id_vuelo
    INNER JOIN HOTEL h ON r.id_hotel = h.id_hotel
    ORDER BY r.fecha_reserva DESC";

$result_reservas = $conn->query($sql_reservas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agencia de Viajes - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .search-container {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            padding: 2rem;
            border-radius: 10px;
            color: white;
            margin-bottom: 2rem;
        }
        .search-title {
            color: white;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border: none;
            padding: 0.75rem;
        }
        .btn-search {
            padding: 0.75rem;
            font-weight: 600;
            background-color: #ffc107;
            border: none;
            color: #000;
        }
        .btn-search:hover {
            background-color: #ffca2c;
            color: #000;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <!-- Buscador de Vuelos -->
        <div class="search-container">
            <h2 class="text-center search-title">Encuentra tu próximo vuelo</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" class="form-control" id="origen" name="origen" placeholder="Ciudad de origen" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="text" class="form-control" id="destino" name="destino" placeholder="Ciudad de destino" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-search w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (isset($resultado_vuelos) && $resultado_vuelos->num_rows > 0): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="bi bi-airplane"></i> Vuelos Disponibles</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Fecha</th>
                                    <th>Plazas Disponibles</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($vuelo = $resultado_vuelos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($vuelo['origen']); ?></td>
                                        <td><?php echo htmlspecialchars($vuelo['destino']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($vuelo['fecha'])); ?></td>
                                        <td><?php echo htmlspecialchars($vuelo['plazas_disponibles']); ?></td>
                                        <td>$<?php echo number_format($vuelo['precio'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif (isset($resultado_vuelos)): ?>
            <div class="alert alert-info">
                No se encontraron vuelos disponibles para los criterios de búsqueda especificados.
            </div>
        <?php endif; ?>

        <!-- Sección de Hoteles Populares -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="bi bi-star-fill"></i> Hoteles más Reservados</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            if ($result_hoteles->num_rows > 0) {
                                while($hotel = $result_hoteles->fetch_assoc()) {
                                    ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($hotel['nombre']); ?></h5>
                                                <p class="card-text">
                                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($hotel['ubicacion']); ?><br>
                                                    <i class="bi bi-currency-dollar"></i> <?php echo number_format($hotel['tarifa_noche'], 0, ',', '.'); ?> por noche<br>
                                                    <i class="bi bi-calendar-check"></i> <?php echo $hotel['total_reservas']; ?> reservas
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="col-12"><div class="alert alert-info">No hay hoteles con más de una reserva.</div></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Lista de Reservas -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="bi bi-list-check"></i> Lista de Reservas</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Reserva</th>
                                        <th>ID Cliente</th>
                                        <th>Fecha Reserva</th>
                                        <th>Vuelo</th>
                                        <th>Hotel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_reservas->num_rows > 0) {
                                        while($reserva = $result_reservas->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <td><?php echo $reserva['id_reserva']; ?></td>
                                                <td><?php echo $reserva['id_cliente']; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($reserva['origen'] . ' → ' . $reserva['destino']); ?><br>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i', strtotime($reserva['fecha_vuelo'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($reserva['nombre_hotel']); ?><br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($reserva['ubicacion']); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">No hay reservas registradas.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
