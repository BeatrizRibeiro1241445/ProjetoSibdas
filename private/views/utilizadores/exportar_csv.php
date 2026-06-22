<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (($_SESSION['perfil'] ?? '') !== 'administrador') {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$filtroNome = trim($_GET['filtro_nome'] ?? '');
$filtroUsername = trim($_GET['filtro_username'] ?? '');
$filtroEmail = trim($_GET['filtro_email'] ?? '');
$filtroPerfil = trim($_GET['filtro_perfil'] ?? '');
$ordenarPor = trim($_GET['ordenar_por'] ?? '');

function texto_perfil_exportar_utilizador($perfil)
{
    switch ($perfil) {
        case 'administrador':
            return 'Administrador';

        case 'tecnico':
            return 'Técnico';

        case 'gestor_hospitalar':
            return 'Gestor Hospitalar';

        case 'profissional_saude':
            return 'Profissional de Saúde';

        default:
            return 'Utilizador';
    }
}

function formatar_data_exportar_utilizador($data)
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y', strtotime($data));
}

function formatar_data_hora_exportar_utilizador($data)
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y H:i', strtotime($data));
}

try {
    $ligacao = db_connect();

    $sql = "
        SELECT DISTINCT
            u.username,
            u.email,
            u.nome,
            u.perfil,
            u.lastLogin,
            u.dataFimContrato
        FROM Utilizador u
        WHERE u.ativo = true
    ";

    $parametros = [];

    if ($filtroNome !== '') {
        $sql .= " AND u.nome LIKE :nome";
        $parametros[':nome'] = '%' . $filtroNome . '%';
    }

    if ($filtroUsername !== '') {
        $sql .= " AND u.username LIKE :username";
        $parametros[':username'] = '%' . $filtroUsername . '%';
    }

    if ($filtroEmail !== '') {
        $sql .= " AND u.email LIKE :email";
        $parametros[':email'] = '%' . $filtroEmail . '%';
    }

    if ($filtroPerfil !== '') {
        $sql .= " AND u.perfil = :perfil";
        $parametros[':perfil'] = $filtroPerfil;
    }

    switch ($ordenarPor) {
        case 'nome_za':
            $sql .= " ORDER BY u.nome DESC, u.username DESC";
            break;

        case 'contrato_crescente':
            $sql .= " ORDER BY u.dataFimContrato IS NULL, u.dataFimContrato ASC, u.nome ASC";
            break;

        case 'contrato_decrescente':
            $sql .= " ORDER BY u.dataFimContrato IS NULL, u.dataFimContrato DESC, u.nome ASC";
            break;

        case 'nome_az':
        default:
            $sql .= " ORDER BY u.nome ASC, u.username ASC";
            break;
    }

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->execute();
    $utilizadores = $stmt->fetchAll();
} catch (PDOException $e) {
    exit('Erro ao exportar utilizadores.');
}

$nomeFicheiro = 'utilizadores_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nomeFicheiro . '"');
header('Pragma: no-cache');
header('Expires: 0');

$ficheiro = fopen('php://output', 'w');
fwrite($ficheiro, "\xEF\xBB\xBF");

fputcsv($ficheiro, [
    'Utilizador',
    'Nome',
    'Email',
    'Perfil',
    'Último login',
    'Fim contrato'
], ';');

foreach ($utilizadores as $utilizador) {
    fputcsv($ficheiro, [
        $utilizador->username,
        $utilizador->nome,
        $utilizador->email,
        texto_perfil_exportar_utilizador($utilizador->perfil),
        formatar_data_hora_exportar_utilizador($utilizador->lastLogin),
        formatar_data_exportar_utilizador($utilizador->dataFimContrato)
    ], ';');
}

fclose($ficheiro);
exit;
