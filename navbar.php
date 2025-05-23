<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Agencia de Viajes</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'formulario_vuelos.php') ? 'active' : ''; ?>" href="formulario_vuelos.php">Vuelos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'formulario_hoteles.php') ? 'active' : ''; ?>" href="formulario_hoteles.php">Hoteles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'formulario_reservas.php') ? 'active' : ''; ?>" href="formulario_reservas.php">Reservas</a>
                </li>
            </ul>
        </div>
    </div>
</nav> 