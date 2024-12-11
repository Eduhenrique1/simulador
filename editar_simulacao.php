<?php
require 'db.php';



// Atualizar taxa no banco
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mes'], $_POST['parcelas'], $_POST['nova_taxa'])) {
    $mes = intval($_POST['mes']);
    $parcelas = intval($_POST['parcelas']);
    $nova_taxa = floatval($_POST['nova_taxa']);

    try {
        $stmt = $pdo->prepare("UPDATE taxas_credito SET taxa = :nova_taxa WHERE mes = :mes AND parcelas = :parcelas");
        $stmt->execute([':nova_taxa' => $nova_taxa, ':mes' => $mes, ':parcelas' => $parcelas]);
        echo "<p style='color: green;'>Taxa atualizada com sucesso!</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Erro ao atualizar a taxa: " . $e->getMessage() . "</p>";
    }
}

// Buscar meses disponíveis no banco
$meses = $pdo->query("SELECT DISTINCT mes FROM taxas_credito ORDER BY mes")->fetchAll(PDO::FETCH_ASSOC);

// Função para buscar prazos
function getPrazos($pdo, $mesSelecionado) {
    $stmt = $pdo->prepare("SELECT parcelas FROM taxas_credito WHERE mes = :mes ORDER BY parcelas");
    $stmt->execute([':mes' => $mesSelecionado]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para buscar taxa
function getTaxa($pdo, $mesSelecionado, $parcelasSelecionadas) {
    $stmt = $pdo->prepare("SELECT taxa FROM taxas_credito WHERE mes = :mes AND parcelas = :parcelas");
    $stmt->execute([':mes' => $mesSelecionado, ':parcelas' => $parcelasSelecionadas]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Taxas de Crédito</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            font-family: "Montserrat", serif;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 20px auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        select, input {
            width: 100%;
            height: 35px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            color: #000000 !important;
            background-color: #f6d620 !important;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 700;
        }

        button:hover {
            background-color: #d1b417;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<header>
      <img src="images/VFBANK.png" alt="Logo" class="logo" />
      <nav>
        <a href="/orcamento/index.html">Simulador</a>
        <!-- <a href="#" class="highlight">Crédito Pessoal</a> -->
      </nav>
    </header>
    <h2>Editar Taxas de Crédito</h2>

    <div class="container">
         <form id="formTaxa" method="POST">
        <label for="mes">Selecione o mês de antecipação:</label>
        <select name="mes" id="mes" required>
            <option value="">Selecione o mês...</option>
            <!-- Exemplo de opções geradas dinamicamente -->
            <option value="3">3º Mês</option>
            <option value="5">5º Mês</option>
            <option value="6">6º Mês</option>
            <option value="12">12º Mês</option>
        </select>

        <label for="parcelas">Selecione o prazo (número de parcelas):</label>
        <select name="parcelas" id="parcelas" required>
            <option value="">Selecione um mês primeiro...</option>
        </select>

        <label for="nova_taxa">Taxa Atual (%):</label>
        <input type="number" step="0.0001" name="nova_taxa" id="nova_taxa" required placeholder="Selecione o prazo para carregar a taxa">

        <button type="submit">Atualizar Taxa</button>
    </form>
    </div>
    <!-- Formulário principal -->
   

    <script>
        $(document).ready(function() {
            // Atualizar os prazos ao selecionar o mês
            $('#mes').on('change', function() {
                const mesSelecionado = $(this).val();
                if (mesSelecionado) {
                    // Exemplo de chamada AJAX para atualizar prazos
                    $.ajax({
                        url: 'get_prazos.php',
                        type: 'GET',
                        data: { mes: mesSelecionado },
                        success: function(data) {
                            $('#parcelas').html(data);
                            $('#nova_taxa').val('');
                        }
                    });
                } else {
                    $('#parcelas').html('<option value="">Selecione um mês primeiro...</option>');
                    $('#nova_taxa').val('');
                }
            });

            // Atualizar a taxa ao selecionar o prazo
            $('#parcelas').on('change', function() {
                const mesSelecionado = $('#mes').val();
                const parcelasSelecionadas = $(this).val();
                if (mesSelecionado && parcelasSelecionadas) {
                    // Exemplo de chamada AJAX para buscar taxa
                    $.ajax({
                        url: 'get_taxa.php',
                        type: 'GET',
                        data: { mes: mesSelecionado, parcelas: parcelasSelecionadas },
                        success: function(data) {
                            $('#nova_taxa').val(data);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
