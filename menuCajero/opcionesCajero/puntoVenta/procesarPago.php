<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$db_name = "dbMayordomia";
$username = "root";
$password = "";

// Precio fijo por boleto
define('PRECIO_BOLETO', 180);

try {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'JSON inválido o no se recibieron datos.']);
        exit;
    }

    $boletos = $data['boletos'] ?? [];
    $forma_pago = intval($data['forma_pago'] ?? 0);
    $recibe = floatval($data['recibe'] ?? 0);
    $idUsuario = intval($data['idUsuario'] ?? 1);

    if (!is_array($boletos) || count($boletos) === 0) {
        echo json_encode(['success' => false, 'message' => 'No hay boletos para procesar.']);
        exit;
    }

    $cantidadBoletos = count($boletos);
    $total = PRECIO_BOLETO * $cantidadBoletos;

    if ($recibe < $total) {
        echo json_encode(['success' => false, 'message' => 'La cantidad recibida es insuficiente.', 'recibo' => ['total'=>$total, 'recibe'=>$recibe, 'cambio'=>$recibe-$total]]);
        exit;
    }

    // Conexión
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar transacción
    $conn->beginTransaction();

    $fechaVenta = new DateTime("now", new DateTimeZone('America/Mexico_City'));
    $fechaStr = $fechaVenta->format('Y-m-d H:i:s');

    // Preparar sentencias
    $stmtUpdate = $conn->prepare("UPDATE Boletos SET status = 3 WHERE idBoleto = :idBoleto AND status = 2");
    $stmtInsertVenta = $conn->prepare("INSERT INTO Ventas (idBoletos, idUsuario, fecha_Venta, forma_Pago) VALUES (:idBoletos, :idUsuario, :fecha_Venta, :forma_Pago)");

    $procesados = [];

    foreach ($boletos as $b) {
        // Se espera que el frontend envíe { idBoleto: 123 } o solo el id como entero
        $idBoleto = isset($b['idBoleto']) ? intval($b['idBoleto']) : intval($b);

        if ($idBoleto <= 0) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => "ID de boleto inválido: {$idBoleto}"]);
            exit;
        }

        // Actualizar status sólo si estaba en 2 (disponible)
        $stmtUpdate->execute([':idBoleto' => $idBoleto]);
        $rows = $stmtUpdate->rowCount();

        if ($rows === 0) {
            // No se pudo actualizar -> no estaba disponible o no existe
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => "El boleto ID {$idBoleto} no está disponible para la venta (ya fue vendido o no existe)."]);
            exit;
        }

        // Insertar en Ventas
        $stmtInsertVenta->execute([
            ':idBoletos'   => $idBoleto,
            ':idUsuario'   => $idUsuario,
            ':fecha_Venta' => $fechaStr,
            ':forma_Pago'  => $forma_pago
        ]);

        $procesados[] = $idBoleto;
    }

    // Commit
    $conn->commit();

    $recibo = [
        'total'  => $total,
        'recibe' => $recibe,
        'cambio' => $recibe - $total
    ];

    echo json_encode(['success' => true, 'message' => 'Venta registrada correctamente.', 'procesados' => $procesados, 'recibo' => $recibo]);
    exit;

} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    exit;
} catch (Exception $ex) {
    if (isset($conn) && $conn->inTransaction()) $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $ex->getMessage()]);
    exit;
}
?>
