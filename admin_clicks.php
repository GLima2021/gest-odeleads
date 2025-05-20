<?php
session_start();
if (!isset($_SESSION["logado"])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';

$dataInicio = $_GET['inicio'] ?? '';
$dataFim = $_GET['fim'] ?? '';

$sql = "SELECT * FROM click";
if ($dataInicio && $dataFim) {
    $sql .= " WHERE data BETWEEN '$dataInicio 00:00:00' AND '$dataFim 23:59:59'";
}
$sql .= " ORDER BY data ASC";

$resultado = $conexao->query($sql);
if (!$resultado) {
    die("Erro: " . $conexao->error);
}

$totalLeads = $resultado->num_rows;
$leadsHoje = 0;
$conversao = 0;
$dataHoje = date("Y-m-d");
$leadsPorData = [];
$datas = [];

$vendedoresDetalhado = [];

while ($linha = $resultado->fetch_assoc()) {
    $dataCompleta = $linha['data'];
    $dataApenas = substr($dataCompleta, 0, 10);
    $nomeVendedor = $linha['nome'];

    // Leads hoje
    if ($dataApenas === $dataHoje) {
        $leadsHoje++;
    }

    // Contagem por data para gráfico
    if (!isset($leadsPorData[$dataApenas])) {
        $leadsPorData[$dataApenas] = 0;
    }
    $leadsPorData[$dataApenas]++;

    // Guardar timestamp para tempo médio
    $datas[] = strtotime($dataCompleta);

    // Detalhamento por vendedor
    if (!isset($vendedoresDetalhado[$nomeVendedor])) {
        $vendedoresDetalhado[$nomeVendedor] = [
            'cliques' => 0,
            'ultimo_lead' => $dataCompleta
        ];
    }

    $vendedoresDetalhado[$nomeVendedor]['cliques']++;

    if ($dataCompleta > $vendedoresDetalhado[$nomeVendedor]['ultimo_lead']) {
        $vendedoresDetalhado[$nomeVendedor]['ultimo_lead'] = $dataCompleta;
    }
}

// Tempo desde o último lead
$tempoMedioTexto = "N/A";
if (!empty($datas)) {
    $agora = time();
    $ultimoLead = end($datas);
    $diferenca = $agora - $ultimoLead;

    $dias = floor($diferenca / 86400);
    $horas = floor(($diferenca % 86400) / 3600);
    $minutos = floor(($diferenca % 3600) / 60);
    $segundos = floor($diferenca % 60);

    $partes = [];
    if ($dias > 0) $partes[] = $dias . "d";
    if ($horas > 0) $partes[] = $horas . "h";
    if ($minutos > 0) $partes[] = $minutos . "m";
    if ($segundos > 0 || empty($partes)) $partes[] = $segundos . "s";

    $tempoMedioTexto = "Último lead há: " . implode(" ", $partes);
}

if ($totalLeads > 0) {
    $conversao = round(($leadsHoje / $totalLeads) * 100, 0);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Leads</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --laranja: #f26522;
            --laranja-escuro: #e55300;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f3f9fa;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
            width: 100%;
            max-width: 1000px;
        }
        .header {
            background: var(--laranja);
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 20px;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 15px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .stats div {
            flex: 1 1 100px;
            min-width: 100px;
        }
        .filter-form {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        input[type="date"] {
            background: #fff;
            color: var(--laranja);
            border: 2px solid var(--laranja);
            padding: 6px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        button[type="submit"] {
            background-color: var(--laranja);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: var(--laranja-escuro);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            overflow-x: auto;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        th {
            background: #f3f3f3;
        }
        .btn-refresh {
            display: block;
            margin: 15px auto;
            background-color: var(--laranja);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">GESTÃO DE LEADS - DASHBOARD</div>
    <div class="stats">
        <div>Total de Leads<br><?= $totalLeads ?></div>
        <div>Leads Hoje<br><?= $leadsHoje ?></div>
        <div><?= $tempoMedioTexto ?></div>
    </div>
    <div class="filter-form">
        <form method="get">
            <label>INÍCIO: <input type="date" name="inicio" value="<?= $dataInicio ?>"></label>
            <label>FIM: <input type="date" name="fim" value="<?= $dataFim ?>"></label>
            <button type="submit">Filtrar</button>
        </form>
    </div>
    <div class="chart-container">
        <canvas id="leadsChart"></canvas>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vendedor</th>
                <th>Cliques</th>
                <th>Último Lead</th>
                <th>Porcentagem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendedoresDetalhado as $nome => $info):
                $porcentagem = $totalLeads > 0 ? round(($info['cliques'] / $totalLeads) * 100) : 0;
            ?>
                <tr>
                    <td><?= htmlspecialchars($nome) ?></td>
                    <td><?= $info['cliques'] ?></td>
                    <td><?= $info['ultimo_lead'] ?></td>
                    <td><?= $porcentagem ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form method="get">
        <button class="btn-refresh">Atualizar Tabela</button>
    </form>
</div>

<script>
    const ctx = document.getElementById('leadsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($leadsPorData)) ?>,
            datasets: [{
                label: 'Leads por Data',
                data: <?= json_encode(array_values($leadsPorData)) ?>,
                backgroundColor: 'rgba(242, 101, 34, 0.7)',
                borderColor: 'rgba(242, 101, 34, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    ticks: {
                        autoSkip: true,
                        maxRotation: 45,
                        minRotation: 45,
                        color: '#333'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#333'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#333'
                    }
                }
            }
        }
    });
</script>
</body>
</html>
