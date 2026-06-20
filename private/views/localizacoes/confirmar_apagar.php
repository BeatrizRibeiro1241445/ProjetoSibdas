<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico'])) {
    header('Location: lista.php');
    exit;
}

$idEncrypted = $_GET['id_localizacao'] ?? null;
$idLocalizacao = aes_decrypt($idEncrypted);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: lista.php');
    exit;
}

$idLocalizacao = (int) $idLocalizacao;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE Localizacao
        SET ativo = false
        WHERE idLocalizacao = :idLocalizacao
    ");

    $stmt->bindParam(':idLocalizacao', $idLocalizacao, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: lista.php');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro ao remover a localização.</p>";
    exit;
}
