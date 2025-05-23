<?php

// Conexión a la base de datos
$servername = "my-mariadb-container"; 
$username = "root";    
$password = "123";     
$dbname = "AGENCIA";   

try {
    // Crear la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    // echo "Conexión exitosa a la base de datos de AGENCIA";
    
} catch (Exception $e) {
    die("Error en la conexión: " . $e->getMessage());
}

?> 