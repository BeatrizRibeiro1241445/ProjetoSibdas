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

$nomeFicheiro = 'equipamentos_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nomeFicheiro . '"');
header('Pragma: no-cache');
header('Expires: 0');

$ficheiro = fopen('php://output', 'w');
fwrite($ficheiro, "\xEF\xBB\xBF");

fputcsv($ficheiro, [
    'Código interno',
    'Designação',
    'N.º Série',
    'Marca / Modelo',
    'Estado',
    'Criticidade',
    'Localização'
], ';');

foreach ($equipamentos as $equipamento) {
    $marcaModelo = trim($equipamento->marca . (!empty($equipamento->modelo) ? ' / ' . $equipamento->modelo : ''));

    fputcsv($ficheiro, [
        $equipamento->codigoInterno,
        $equipamento->designacao,
        $equipamento->numeroSerie,
        $marcaModelo,
        $equipamento->estado,
        $equipamento->criticidade,
        $equipamento->localizacao
    ], ';');
}

fclose($ficheiro);
exit;
