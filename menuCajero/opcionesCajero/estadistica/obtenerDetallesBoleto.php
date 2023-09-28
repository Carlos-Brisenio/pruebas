<?php
// Conexión a la base de datos
$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Obtener el número de boleto enviado desde JavaScript
$numero_boleto = $_POST['numero_boleto'];

// Consulta SQL para obtener los detalles del boleto desde la tabla InfoBoletos
$query = "SELECT nombre, ciudad, colonia, calle, numero, colinda1, colinda2, referencia, telefono1, telefono2, correo_Electronico
          FROM InfoBoletos
          WHERE idBoleto = :numero_boleto";

$stmt = $conn->prepare($query);
$stmt->bindParam(':numero_boleto', $numero_boleto, PDO::PARAM_INT);
$stmt->execute();
$detallesBoleto = $stmt->fetch(PDO::FETCH_ASSOC);

// Devolver los detalles del boleto en formato JSON
echo json_encode($detallesBoleto);
?>
