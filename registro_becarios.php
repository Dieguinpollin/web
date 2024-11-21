<?php
// Configuración de la conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "becarios";

// Conectar a la base de datos
$conn = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = ""; // Inicializar para evitar errores

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'] ?? '';
    $nombre = $_POST['nombre'] ?? '';

    // Establecer por defecto que el usuario registrado es becario
    $es_becario = 1;

    // Insertar en la base de datos
    $sql = "INSERT INTO alumnos (matricula, nombre, es_becario) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssi", $matricula, $nombre, $es_becario);
        if ($stmt->execute()) {
            $mensaje = "Registro exitoso: $nombre ahora es becario.";
        } else {
            $mensaje = "Error al registrar: " . $stmt->error;
        }
    } else {
        $mensaje = "Error al preparar la consulta: " . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Registro de Becarios</title>
</head>
<body>
    <h1>Registro de Becarios</h1>
    <form method="POST" action="">
        <label for="matricula">Matrícula:</label>
        <input type="text" id="matricula" name="matricula" required>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <input type="submit" value="Registrar">
    </form>
    <?php if ($mensaje): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
</body>
</html>
