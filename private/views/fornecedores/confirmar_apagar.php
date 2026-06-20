<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico', 'gestor_hospitalar'])) {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$idEncrypted = $_GET['id_fornecedor'] ?? null;
$idFornecedor = aes_decrypt($idEncrypted);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit;
}

$idFornecedor = (int) $idFornecedor;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE Fornecedor
        SET ativo = false
        WHERE idFornecedor = :idFornecedor
    ");

    $stmt->bindParam(':idFornecedor', $idFornecedor, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: lista.php');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro ao remover o fornecedor.</p>";
    exit;
}
