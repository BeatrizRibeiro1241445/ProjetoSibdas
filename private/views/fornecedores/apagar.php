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

$page_title = APP_NAME . ' - Remover Fornecedor';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$fornecedor = null;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT
            designacao,
            nif,
            email,
            telefone,
            morada
        FROM Fornecedor
        WHERE idFornecedor = :idFornecedor
          AND ativo = true
    ");

    $stmt->bindParam(':idFornecedor', $idFornecedor, PDO::PARAM_INT);
    $stmt->execute();

    $fornecedor = $stmt->fetch();

    if (!$fornecedor) {
        header('Location: lista.php');
        exit;
    }
} catch (PDOException $e) {
    $erro = 'Erro ao obter os dados do fornecedor.';
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

                <?php if ($fornecedor): ?>

                    <p class="mb-2 fs-5">
                        Deseja remover este fornecedor da listagem?
                    </p>

                    <h4 class="mb-4">
                        <strong><?= e($fornecedor->designacao) ?></strong>
                    </h4>

                    <div class="mb-4">

                        <span class="d-block mb-2">
                            <i class="fas fa-id-card me-2"></i>
                            <strong>NIF:</strong> <?= e($fornecedor->nif) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>Email:</strong> <?= e($fornecedor->email) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <strong>Telefone:</strong> <?= e($fornecedor->telefone) ?>
                        </span>

                        <span class="d-block">
                            <i class="fas fa-location-dot me-2"></i>
                            <strong>Morada:</strong> <?= e($fornecedor->morada ?: '-') ?>
                        </span>

                    </div>

                    <div class="d-flex justify-content-center gap-3">

                        <a href="lista.php" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-xmark me-2"></i> Não
                        </a>

                        <a href="confirmar_apagar.php?id_fornecedor=<?= urlencode($idEncrypted) ?>" class="btn btn-danger px-4">
                            <i class="fas fa-check me-2"></i> Sim
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>