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

$page_title = APP_NAME . ' - Remover Equipamento';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$equipamento = null;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT
            e.codigoInterno,
            e.numeroSerie,
            e.designacao,
            ee.descricao AS estado,
            cr.descricao AS criticidade,
            CONCAT(l.edificio, ' - Piso ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao
        FROM Equipamento e
        INNER JOIN EstadoEquipamento ee
            ON e.idEstadoEquipamento = ee.idEstadoEquipamento
        INNER JOIN CriticidadeEquipamento cr
            ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
        INNER JOIN Localizacao l
            ON e.idLocalizacao = l.idLocalizacao
        WHERE e.idEquipamento = :idEquipamento
          AND e.ativo = true
    ");

    $stmt->bindParam(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmt->execute();

    $equipamento = $stmt->fetch();

    if (!$equipamento) {
        header('Location: lista.php');
        exit;
    }
} catch (PDOException $e) {
    $erro = 'Erro ao obter os dados do equipamento.';
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
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger text-center">
                        <?= e($erro) ?>
                    </div>
                <?php endif; ?>

                <?php if ($equipamento): ?>

                    <p class="mb-2 fs-5">
                        Deseja remover este equipamento da listagem?
                    </p>

                    <p class="text-muted">
                        O equipamento deixa de aparecer na lista principal e passa para a lista de equipamentos removidos.
                    </p>

                    <h4 class="mb-4">
                        <strong><?= e($equipamento->designacao) ?></strong>
                    </h4>

                    <div class="mb-4">

                        <span class="d-block mb-2">
                            <i class="fas fa-barcode me-2"></i>
                            <strong>Código interno:</strong> <?= e($equipamento->codigoInterno) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-hashtag me-2"></i>
                            <strong>N.º série:</strong> <?= e($equipamento->numeroSerie) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-circle-check me-2"></i>
                            <strong>Estado atual:</strong> <?= e($equipamento->estado) ?>
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            <strong>Criticidade:</strong> <?= e($equipamento->criticidade) ?>
                        </span>

                        <span class="d-block">
                            <i class="fas fa-location-dot me-2"></i>
                            <strong>Localização:</strong> <?= e($equipamento->localizacao) ?>
                        </span>

                    </div>

                    <div class="d-flex justify-content-center gap-3">

                        <a href="lista.php" class="btn btn-outline-secondary px-4">
                            <i class="fa-solid fa-xmark me-2"></i> Não
                        </a>

                        <a href="confirmar_apagar.php?id_equipamento=<?= urlencode($idEncrypted) ?>" class="btn btn-danger px-4">
                            <i class="fa-solid fa-check me-2"></i> Sim
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>