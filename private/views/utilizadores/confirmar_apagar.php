<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (($_SESSION['perfil'] ?? '') !== 'administrador') {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$idEncrypted = $_GET['id_utilizador'] ?? null;
$idUtilizador = aes_decrypt($idEncrypted);

if (!$idUtilizador || !is_numeric($idUtilizador)) {
    header('Location: lista.php');
    exit;
}

$idUtilizador = (int) $idUtilizador;
$idUtilizadorSessao = (int) ($_SESSION['idUtilizador'] ?? 0);
$usernameSessao = $_SESSION['utilizador'] ?? '';

if ($idUtilizador === $idUtilizadorSessao) {
    header('Location: lista.php');
    exit;
}

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE Utilizador
        SET ativo = false
        WHERE idUtilizador = :idUtilizador
          AND ativo = true
          AND username <> :usernameSessao
    ");

    $stmt->bindParam(':idUtilizador', $idUtilizador, PDO::PARAM_INT);
    $stmt->bindValue(':usernameSessao', $usernameSessao, PDO::PARAM_STR);
    $stmt->execute();

    header('Location: lista.php');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro ao remover o utilizador.</p>";
    exit;
}
