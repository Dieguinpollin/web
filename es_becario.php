<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "becarios";


$conn = new mysqli($host, $user, $password, $database);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricula = $_POST["matricula"];
    $hoy = date("Y-m-d");

    // Verificar si la matrícula existe y si es becario
    $sql = "SELECT es_becario FROM alumnos WHERE matricula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $stmt->bind_result($es_becario);
    $stmt->fetch();
    $stmt->close();

    if ($es_becario === null) {
        $mensaje = "Matrícula no encontrada.";
    } elseif (!$es_becario) {
        $mensaje = "Acceso denegado: No eres becario.";
    } else {
        // Verificar si ya hizo check-in hoy
        $sql = "SELECT * FROM registros WHERE matricula = ? AND fecha = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $matricula, $hoy);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();

        if ($resultado->num_rows > 0) {
            $mensaje = "Ya hiciste check-in hoy.";
        } else {
            // Registrar el check-in
            $hora_actual = date("H:i:s");
            $sql = "INSERT INTO registros (matricula, fecha, hora) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $matricula, $hoy, $hora_actual);
            if ($stmt->execute()) {
                $mensaje = "Acceso permitido. ¡Buen provecho!";
            } else {
                $mensaje = "Error al registrar el check-in.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Becarios</title>
    <link rel="stylesheet" href="styles.css"><br>

</head>
<body>
    <h1>Check-in Becarios</h1>
    <form method="POST" action="">
        <label for="matricula">Matrícula:</label>
        <input type="text" id="matricula" name="matricula" required>
        <input type="submit" value="Registrar">
    </form>
    <?php if ($mensaje): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
</body>
</html>
