<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico'])) {
    header('Location: lista.php');
    exit;
}

$idEncrypted = $_GET['id_equipamento'] ?? null;
$idEquipamento = aes_decrypt($idEncrypted);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit;
}

$idEquipamento = (int) $idEquipamento;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE Equipamento
        SET ativo = false
        WHERE idEquipamento = :idEquipamento
    ");

    $stmt->bindParam(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: lista.php');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro ao remover o equipamento.</p>";
    exit;
}
