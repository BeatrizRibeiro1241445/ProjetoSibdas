<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$filtroCodigo = trim($_GET['filtro_codigo'] ?? '');
$filtroDesignacao = trim($_GET['filtro_designacao'] ?? '');
$filtroSerie = trim($_GET['filtro_serie'] ?? '');
$filtroMarca = trim($_GET['filtro_marca'] ?? '');
$filtroEstado = trim($_GET['filtro_estado'] ?? '');
$filtroCriticidade = trim($_GET['filtro_criticidade'] ?? '');
$filtroCategoria = trim($_GET['filtro_categoria'] ?? '');
$ordenarPor = trim($_GET['ordenar_por'] ?? '');

try {
    $ligacao = db_connect();

    $sql = "
        SELECT DISTINCT
            e.codigoInterno,
            e.designacao,
            e.numeroSerie,
            e.marca,
            e.modelo,
            ce.descricao AS categoria,
            ee.descricao AS estado,
            cr.descricao AS criticidade,
            CONCAT(l.edificio, ' - ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao
        FROM Equipamento e
        INNER JOIN CategoriaEquipamento ce
            ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
        INNER JOIN EstadoEquipamento ee
            ON e.idEstadoEquipamento = ee.idEstadoEquipamento
        INNER JOIN CriticidadeEquipamento cr
            ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
        INNER JOIN Localizacao l
            ON e.idLocalizacao = l.idLocalizacao
        WHERE e.ativo = true
    ";

    $parametros = [];

    if ($filtroCodigo !== '') {
        $sql .= " AND e.codigoInterno LIKE :codigo";
        $parametros[':codigo'] = '%' . $filtroCodigo . '%';
    }

    if ($filtroDesignacao !== '') {
        $sql .= " AND e.designacao LIKE :designacao";
        $parametros[':designacao'] = '%' . $filtroDesignacao . '%';
    }

    if ($filtroSerie !== '') {
        $sql .= " AND e.numeroSerie LIKE :serie";
        $parametros[':serie'] = '%' . $filtroSerie . '%';
    }

    if ($filtroMarca !== '') {
        $sql .= " AND e.marca LIKE :marca";
        $parametros[':marca'] = '%' . $filtroMarca . '%';
    }

    if ($filtroEstado !== '') {
        $sql .= " AND ee.descricao = :estado";
        $parametros[':estado'] = $filtroEstado;
    }

    if ($filtroCriticidade !== '') {
        $sql .= " AND cr.descricao = :criticidade";
        $parametros[':criticidade'] = $filtroCriticidade;
    }

    if ($filtroCategoria !== '') {
        $sql .= " AND ce.descricao LIKE :categoria";
        $parametros[':categoria'] = '%' . $filtroCategoria . '%';
    }

    switch ($ordenarPor) {
        case 'codigo_decrescente':
            $sql .= " ORDER BY e.codigoInterno DESC";
            break;

        case 'designacao_az':
            $sql .= " ORDER BY e.designacao ASC";
            break;

        case 'designacao_za':
            $sql .= " ORDER BY e.designacao DESC";
            break;

        case 'codigo_crescente':
        default:
            $sql .= " ORDER BY e.codigoInterno ASC";
            break;
    }

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->execute();
    $equipamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    exit('Erro ao exportar equipamentos.');
}

$dataExportacao = date('d-m-Y H:i');
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Equipamentos - Exportar PDF</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
            margin: 30px;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .subtitulo {
            font-size: 12px;
            color: #555;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #2e5a88;
            color: #fff;
        }

        tr:nth-child(even) td {
            background-color: #f2f6fa;
        }

        .total {
            margin-top: 16px;
            font-size: 12px;
        }

        .botao-imprimir {
            margin-bottom: 20px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        /* Ao imprimir / guardar como PDF, esconde o botão */
        @media print {
            .botao-imprimir {
                display: none;
            }
        }
    </style>
</head>

<body>

    <button class="botao-imprimir" onclick="window.print();">Imprimir / Guardar como PDF</button>

    <h1>Lista de Equipamentos</h1>
    <p class="subtitulo">MedInventário &mdash; Exportado em <?= e($dataExportacao) ?></p>

    <table>
        <thead>
            <tr>
                <th>Código interno</th>
                <th>Designação</th>
                <th>N.º Série</th>
                <th>Marca / Modelo</th>
                <th>Estado</th>
                <th>Criticidade</th>
                <th>Localização</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($equipamentos as $equipamento): ?>
                <?php $marcaModelo = trim($equipamento->marca . (!empty($equipamento->modelo) ? ' / ' . $equipamento->modelo : '')); ?>
                <tr>
                    <td><?= e($equipamento->codigoInterno) ?></td>
                    <td><?= e($equipamento->designacao) ?></td>
                    <td><?= e($equipamento->numeroSerie) ?></td>
                    <td><?= e($marcaModelo) ?></td>
                    <td><?= e($equipamento->estado) ?></td>
                    <td><?= e($equipamento->criticidade) ?></td>
                    <td><?= e($equipamento->localizacao) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="total">Total de equipamentos: <?= count($equipamentos) ?></p>

    <script>
        // Abre automaticamente a caixa de impressão (permite Guardar como PDF)
        window.onload = function () {
            window.print();
        };
    </script>

</body>

</html>
