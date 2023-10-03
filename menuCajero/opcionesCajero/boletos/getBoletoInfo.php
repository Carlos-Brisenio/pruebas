<?php
    header('Content-Type: application/json');

    $host = "localhost";
    $db_name = "dbMayordomia";
    $username = "root"; 
    $password = "";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);


    if(isset($_GET['numeroBoleto'])) {
        $numeroBoleto = $_GET['numeroBoleto'];
    
        // Consulta en InfoBoletos
        $stmtInfo = $conn->prepare("SELECT * FROM InfoBoletos WHERE idBoleto = ?");
        $stmtInfo->execute([$numeroBoleto]);
        $infoBoleto = $stmtInfo->fetch(PDO::FETCH_ASSOC);
    
        // Consulta en Boletos
        $stmtBoleto = $conn->prepare("SELECT * FROM Boletos WHERE idBoleto = ?");
        $stmtBoleto->execute([$numeroBoleto]);
        $datosBoleto = $stmtBoleto->fetch(PDO::FETCH_ASSOC);
    
        if($infoBoleto && $datosBoleto) {
            $boletoInfo = array_merge($datosBoleto, $infoBoleto);
            echo json_encode($boletoInfo);
        } elseif($infoBoleto) {
            echo json_encode($infoBoleto);
        } elseif($datosBoleto) {
            echo json_encode($datosBoleto);
        } else {
            echo json_encode(['error' => 'No se encontró información para el boleto.']);
        }
    } else {
        echo json_encode(['error' => 'No se especificó número de boleto.']);
    }
?>
    