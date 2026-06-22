<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico', 'gestor_hospitalar'])) {
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

    $stmtEstado = $ligacao->prepare("
        SELECT idEstadoEquipamento
        FROM EstadoEquipamento
        WHERE descricao = :descricao
        LIMIT 1
    ");

    $stmtEstado->execute([':descricao' => 'Abatido']);
    $estado = $stmtEstado->fetch();

    if (!$estado) {
        $stmtEstado->execute([':descricao' => 'Inativo']);
        $estado = $stmtEstado->fetch();
    }

    if ($estado) {
        $stmt = $ligacao->prepare("
            UPDATE Equipamento
            SET
                ativo = false,
                idEstadoEquipamento = :idEstadoEquipamento
            WHERE idEquipamento = :idEquipamento
        ");

        $stmt->bindValue(':idEstadoEquipamento', $estado->idEstadoEquipamento, PDO::PARAM_INT);
        $stmt->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $ligacao->prepare("
            UPDATE Equipamento
            SET ativo = false
            WHERE idEquipamento = :idEquipamento
        ");

        $stmt->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
    }

    header('Location: eliminados.php');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro ao remover o equipamento.</p>";
    exit;
}
