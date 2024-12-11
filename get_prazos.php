<?php
require 'db.php';

if (isset($_GET['mes'])) {
    $mes = intval($_GET['mes']);
    $prazos = $pdo->prepare("SELECT parcelas FROM taxas_credito WHERE mes = :mes ORDER BY parcelas");
    $prazos->execute([':mes' => $mes]);
    $prazos = $prazos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($prazos as $prazo) {
        echo "<option value='{$prazo['parcelas']}'>{$prazo['parcelas']} Parcelas</option>";
    }
}
