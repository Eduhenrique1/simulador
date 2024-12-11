<?php
require 'db.php';


if (isset($_GET['mes'], $_GET['parcelas'])) {
    $mes = intval($_GET['mes']);
    $parcelas = intval($_GET['parcelas']);
    $stmt = $pdo->prepare("SELECT taxa FROM taxas_credito WHERE mes = :mes AND parcelas = :parcelas");
    $stmt->execute([':mes' => $mes, ':parcelas' => $parcelas]);
    $taxa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($taxa) {
        echo $taxa['taxa'];
    }
}
