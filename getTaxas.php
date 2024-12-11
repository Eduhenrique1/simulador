<?php
// Conexão com o banco de dados
require 'db.php';


// Receber os parâmetros via GET
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;
$parcelas = isset($_GET['parcelas']) ? intval($_GET['parcelas']) : 0;

if ($mes > 0 && $parcelas > 0) {
    // Buscar a taxa correspondente
    $stmt = $pdo->prepare("SELECT taxa FROM taxas_credito WHERE mes = :mes AND parcelas = :parcelas LIMIT 1");
    $stmt->execute([':mes' => $mes, ':parcelas' => $parcelas]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['taxa' => $result['taxa']]);
    } else {
        echo json_encode(['error' => 'Taxa não encontrada']);
    }
} else {
    echo json_encode(['error' => 'Parâmetros inválidos']);
}
?>
