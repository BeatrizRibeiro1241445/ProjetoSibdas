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

$page_title = APP_NAME . ' - Remover Utilizador';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$utilizador = null;

function texto_perfil_apagar_utilizador($perfil)
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

function data_formatada_apagar_utilizador($data)
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y', strtotime($data));
}

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT
            idUtilizador,
            username,
            email,
            nome,
            perfil,
            ativo,
            lastLogin,
            dataFimContrato
        FROM Utilizador
        WHERE idUtilizador = :idUtilizador
          AND ativo = true
    ");

    $stmt->bindParam(':idUtilizador', $idUtilizador, PDO::PARAM_INT);
    $stmt->execute();

    $utilizador = $stmt->fetch();

    if (!$utilizador) {
        header('Location: lista.php');
        exit;
    }

    if ($utilizador->username === $usernameSessao) {
        header('Location: lista.php');
        exit;
    }
} catch (PDOException $e) {
    $erro = 'Erro ao obter os dados do utilizador.';
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/nav.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Conteúdo Principal -->
<main class="content">
    <section>

        <div class="d-flex justify-content-center mt-4">

            <div class="card w-100 shadow rounded text-center p-4" style="max-width: 750px;">

                <div class="text-warning display-4 mb-3">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger text-center">
                        <?= e($erro) ?>
                    </div>
                <?php endif; ?>

                <?php if ($utilizador): ?>

                    <p class="mb-2 fs-5">
                        Deseja remover este utilizador da listagem?
                    </p>

                    <h4 class="mb-4">
                        <strong><?= e($utilizador->nome) ?></strong>
                    </h4>

                    <div class="mb-4">

                        <span class="d-block mb-2">
                            <i class="fas fa-id-card me-2"></i>
                            <strong>ID:</strong> <?= e($utilizador->idUtilizador) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-user me-2"></i>
                            <strong>Username:</strong> <?= e($utilizador->username) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>Email:</strong> <?= e($utilizador->email) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-user-shield me-2"></i>
                            <strong>Perfil:</strong> <?= e(texto_perfil_apagar_utilizador($utilizador->perfil)) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Último login:</strong> <?= e(data_formatada_apagar_utilizador($utilizador->lastLogin)) ?>
                        </span>

                        <span class="d-block">
                            <i class="fas fa-calendar-days me-2"></i>
                            <strong>Fim do contrato:</strong> <?= e(data_formatada_apagar_utilizador($utilizador->dataFimContrato)) ?>
                        </span>

                    </div>

                    <div class="d-flex justify-content-center gap-3">

                        <a href="lista.php" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-xmark me-2"></i> Não
                        </a>

                        <a href="confirmar_apagar.php?id_utilizador=<?= urlencode($idEncrypted) ?>" class="btn btn-danger px-4">
                            <i class="fas fa-check me-2"></i> Sim
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>