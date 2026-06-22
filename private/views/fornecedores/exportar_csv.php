<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico', 'gestor_hospitalar'])) {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$filtroNif = trim($_GET['filtro_nif'] ?? '');
$filtroDesignacao = trim($_GET['filtro_designacao'] ?? '');
$filtroEmail = trim($_GET['filtro_email'] ?? '');
$filtroTelefone = trim($_GET['filtro_telefone'] ?? '');

try {
    $ligacao = db_connect();

    $sql = "
        SELECT DISTINCT
            f.designacao,
            f.nif,
            f.telefone,
            f.email
        FROM Fornecedor f
        WHERE f.ativo = true
    ";

    $parametros = [];

    if ($filtroDesignacao !== '') {
        $sql .= " AND f.designacao LIKE :designacao";
        $parametros[':designacao'] = '%' . $filtroDesignacao . '%';
    }

    if ($filtroNif !== '') {
        $sql .= " AND f.nif LIKE :nif";
        $parametros[':nif'] = '%' . $filtroNif . '%';
    }

    if ($filtroEmail !== '') {
        $sql .= " AND f.email LIKE :email";
        $parametros[':email'] = '%' . $filtroEmail . '%';
    }

    if ($filtroTelefone !== '') {
        $sql .= " AND f.telefone LIKE :telefone";
        $parametros[':telefone'] = '%' . $filtroTelefone . '%';
    }

    $sql .= " ORDER BY f.designacao";

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->execute();
    $fornecedores = $stmt->fetchAll();
} catch (PDOException $e) {
    exit('Erro ao exportar fornecedores.');
}

$nomeFicheiro = 'fornecedores_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nomeFicheiro . '"');
header('Pragma: no-cache');
header('Expires: 0');

$ficheiro = fopen('php://output', 'w');
fwrite($ficheiro, "\xEF\xBB\xBF");

fputcsv($ficheiro, [
    'Empresa',
    'NIF',
    'Telefone',
    'Email'
], ';');

foreach ($fornecedores as $fornecedor) {
    fputcsv($ficheiro, [
        $fornecedor->designacao,
        $fornecedor->nif,
        $fornecedor->telefone,
        $fornecedor->email
    ], ';');
}

fclose($ficheiro);
exit;
