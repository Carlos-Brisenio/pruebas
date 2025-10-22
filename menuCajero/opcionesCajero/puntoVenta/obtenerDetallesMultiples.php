<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $ids = $data['boletos'] ?? [];

    if (!is_array($ids) || count($ids) === 0) {
        echo json_encode(['success' => false, 'message' => 'No se recibieron boletos.']);
        exit;
    }

    // DB
    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root";
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // placeholders
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT b.idBoleto, b.numero_boleto,
                   i.nombre, i.calle, i.numero AS num_casa, i.colonia, i.ciudad, i.telefono1, i.telefono2
            FROM Boletos b
            LEFT JOIN InfoBoletos i ON b.idBoleto = i.idBoleto
            WHERE b.idBoleto IN ($placeholders)";

    $stmt = $conn->prepare($sql);
    foreach ($ids as $k => $id) $stmt->bindValue($k+1, intval($id), PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Map por id para ordenar
    $map = [];
    foreach ($rows as $r) {
        $map[intval($r['idBoleto'])] = $r;
    }

    $ordered = [];
    foreach ($ids as $id) {
        $idInt = intval($id);
        if (isset($map[$idInt])) {
            $r = $map[$idInt];
            // Normalizar campos para que coincidan con el JS
            $ordered[] = [
                'idBoleto' => $r['idBoleto'],
                'numero_boleto' => $r['numero_boleto'],
                'nombre' => $r['nombre'],
                'calle' => $r['calle'],
                'numero' => $r['num_casa'],
                'colonia' => $r['colonia'],
                'ciudad' => $r['ciudad'],
                'telefono1' => $r['telefono1'],
                'telefono2' => $r['telefono2']
            ];
        } else {
            $ordered[] = [
                'idBoleto' => $idInt,
                'numero_boleto' => null,
                'nombre' => null,
                'calle' => null,
                'numero' => null,
                'colonia' => null,
                'ciudad' => null,
                'telefono1' => null,
                'telefono2' => null
            ];
        }
    }

    echo json_encode(['success' => true, 'data' => $ordered]);
    exit;
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error DB: '.$e->getMessage()]);
    exit;
} catch (Exception $ex) {
    echo json_encode(['success' => false, 'message' => 'Error: '.$ex->getMessage()]);
    exit;
}
?>