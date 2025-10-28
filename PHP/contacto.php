<?php

// 1. CONFIGURACIÓN DE LA BASE DE DATOS
$host = "localhost";
$usuario = "root";
$clave = "";
$nombre_bd = "inmobiliaria_db";
$puerto = 3307;

// 2. CONEXIÓN A LA BASE DE DATOS
$conn = mysqli_connect($host, $usuario, $clave, $nombre_bd, $puerto);

if (!$conn) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}


// 3. PROCESAMIENTO DEL FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    $sql = "INSERT INTO contacto (nombre, telefono, email, mensaje) 
            VALUES ('$nombre', '$telefono', '$email', '$mensaje')";

    if (mysqli_query($conn, $sql)) {
        
// 4. Redirigir al usuario a la página de agradecimiento
        header("Location: ../HTML's/gracias.html");
        exit(); 

    } else {

        echo "<h1>Error al guardar el mensaje:</h1>";
        echo "<p>Consulta fallida: " . mysqli_error($conn) . "</p>";
    }
} else {

    header("Location: ../index.html");
    exit(); 
}

// 5. Cerrar la conexión
mysqli_close($conn);

?>