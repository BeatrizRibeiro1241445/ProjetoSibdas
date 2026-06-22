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
    header('Location: eliminados.php');
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

    $stmtEstado->execute([':descricao' => 'Ativo']);
    $estado = $stmtEstado->fetch();

    if ($estado) {
        $stmt = $ligacao->prepare("
            UPDATE Equipamento
            SET
                ativo = true,
                idEstadoEquipamento = :idEstadoEquipamento
            WHERE idEquipamento = :idEquipamento
        ");

        $stmt->bindValue(':idEstadoEquipamento', $estado->idEstadoEquipamento, PDO::PARAM_INT);
        $stmt->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $ligacao->prepare("
            UPDATE Equipamento
            SET ativo = true
            WHERE idEquipamento = :idEquipamento
        ");

        $stmt->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
    }

    registar_log('EQUIPAMENTO_RESTAURADO', 'ID equipamento: ' . $idEquipamento);

    header('Location: lista.php');
    exit;
} catch (PDOException $e) {
    registar_log('ERRO_BD', 'Erro ao restaurar equipamento. ID equipamento: ' . $idEquipamento);

    echo "<p class='text-danger'>Erro ao restaurar o equipamento.</p>";
    exit;
}
