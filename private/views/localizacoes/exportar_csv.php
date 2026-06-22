<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$filtroCategoria = trim($_GET['filtro_categoria'] ?? '');
$filtroEdificio = trim($_GET['filtro_edificio'] ?? '');
$filtroPiso = trim($_GET['filtro_piso'] ?? '');
$filtroServico = trim($_GET['filtro_servico'] ?? '');
$filtroSala = trim($_GET['filtro_sala'] ?? '');

try {
    $ligacao = db_connect();

    $sql = "
        SELECT DISTINCT
            l.categoria,
            l.edificio,
            l.piso,
            l.servico,
            l.sala
        FROM Localizacao l
        WHERE l.ativo = true
    ";

    $parametros = [];

    if ($filtroCategoria !== '') {
        $sql .= " AND l.categoria LIKE :categoria";
        $parametros[':categoria'] = '%' . $filtroCategoria . '%';
    }

    if ($filtroEdificio !== '') {
        $sql .= " AND l.edificio LIKE :edificio";
        $parametros[':edificio'] = '%' . $filtroEdificio . '%';
    }

    if ($filtroPiso !== '') {
        $sql .= " AND l.piso LIKE :piso";
        $parametros[':piso'] = '%' . $filtroPiso . '%';
    }

    if ($filtroServico !== '') {
        $sql .= " AND l.servico LIKE :servico";
        $parametros[':servico'] = '%' . $filtroServico . '%';
    }

    if ($filtroSala !== '') {
        $sql .= " AND l.sala LIKE :sala";
        $parametros[':sala'] = '%' . $filtroSala . '%';
    }

    $sql .= "
        ORDER BY
            l.edificio,
            l.piso,
            l.servico,
            l.sala
    ";

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->execute();
    $localizacoes = $stmt->fetchAll();
} catch (PDOException $e) {
    exit('Erro ao exportar localizações.');
}

$nomeFicheiro = 'localizacoes_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nomeFicheiro . '"');
header('Pragma: no-cache');
header('Expires: 0');

$ficheiro = fopen('php://output', 'w');
fwrite($ficheiro, "\xEF\xBB\xBF");

fputcsv($ficheiro, [
    'Categoria',
    'Edifício',
    'Piso',
    'Serviço / Departamento',
    'Sala / Gabinete'
], ';');

foreach ($localizacoes as $localizacao) {
    fputcsv($ficheiro, [
        $localizacao->categoria,
        $localizacao->edificio,
        $localizacao->piso,
        $localizacao->servico,
        $localizacao->sala
    ], ';');
}

fclose($ficheiro);
exit;
