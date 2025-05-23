-- Crear tabla VUELO
CREATE TABLE  VUELO (
    id_vuelo INT AUTO_INCREMENT PRIMARY KEY,
    origen VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    fecha DATETIME NOT NULL,
    plazas_disponibles INT NOT NULL,
    precio INT NOT NULL
);

-- Crear tabla HOTEL
CREATE TABLE  HOTEL (
    id_hotel INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(200) NOT NULL,
    habitaciones_disponibles INT NOT NULL,
    tarifa_noche INT NOT NULL
);

-- Crear tabla RESERVA
CREATE TABLE  RESERVA (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha_reserva DATETIME NOT NULL,
    id_vuelo INT,
    id_hotel INT,
    FOREIGN KEY (id_vuelo) REFERENCES VUELO(id_vuelo),
    FOREIGN KEY (id_hotel) REFERENCES HOTEL(id_hotel)
); 