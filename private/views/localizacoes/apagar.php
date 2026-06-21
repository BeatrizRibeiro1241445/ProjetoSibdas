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

$page_title = APP_NAME . ' - Remover Localização';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$localizacao = null;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT
            categoria,
            edificio,
            piso,
            servico,
            sala
        FROM Localizacao
        WHERE idLocalizacao = :idLocalizacao
          AND ativo = true
    ");

    $stmt->bindParam(':idLocalizacao', $idLocalizacao, PDO::PARAM_INT);
    $stmt->execute();

    $localizacao = $stmt->fetch();

    if (!$localizacao) {
        header('Location: lista.php');
        exit;
    }
} catch (PDOException $e) {
    $erro = 'Erro ao obter os dados da localização.';
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

                <?php if ($localizacao): ?>

                    <p class="mb-2 fs-5">
                        Deseja remover esta localização da listagem?
                    </p>

                    <h4 class="mb-4">
                        <strong>
                            <?= e($localizacao->edificio) ?> -
                            <?= e($localizacao->servico) ?> -
                            <?= e($localizacao->sala) ?>
                        </strong>
                    </h4>

                    <div class="mb-4">

                        <span class="d-block mb-2">
                            <i class="fas fa-layer-group me-2"></i>
                            <strong>Categoria:</strong> <?= e($localizacao->categoria) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-building me-2"></i>
                            <strong>Edifício:</strong> <?= e($localizacao->edificio) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-stairs me-2"></i>
                            <strong>Piso:</strong> <?= e($localizacao->piso) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-hospital me-2"></i>
                            <strong>Serviço:</strong> <?= e($localizacao->servico) ?>
                        </span>

                        <span class="d-block">
                            <i class="fas fa-door-open me-2"></i>
                            <strong>Sala / Gabinete:</strong> <?= e($localizacao->sala) ?>
                        </span>

                    </div>

                    <div class="d-flex justify-content-center gap-3">

                        <a href="lista.php" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-xmark me-2"></i> Não
                        </a>

                        <a href="confirmar_apagar.php?id_localizacao=<?= urlencode($idEncrypted) ?>" class="btn btn-danger px-4">
                            <i class="fas fa-check me-2"></i> Sim
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>