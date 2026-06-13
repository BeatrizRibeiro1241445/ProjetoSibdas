<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Fornecedores';
$body_class = 'pagina-novo-equipamento';

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

                    <p class="mb-2 fs-5">
                        Deseja remover este fornecedor da listagem?
                    </p>

                    <h4 class="mb-4">
                        <strong>MedTech Portugal</strong>
                    </h4>

                    <div class="mb-4">

                        <span class="d-block mb-2">
                            <i class="fas fa-id-card me-2"></i>
                            <strong>NIF:</strong> 509000000
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>Email:</strong> geral@medtech.pt
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <strong>Telefone:</strong> +351 220 000 000
                        </span>

                        <span class="d-block">
                            <i class="fas fa-location-dot me-2"></i>
                            <strong>Morada:</strong> Rua da Saúde, Porto, Portugal
                        </span>

                    </div>

                    <div class="mb-4 text-start">
                        <label for="motivo_remocao" class="form-label">
                            Motivo da remoção
                        </label>

                        <textarea class="form-control" id="motivo_remocao" name="motivo_remocao" rows="3"
                            placeholder="Ex.: fornecedor deixou de prestar serviço, foi substituído ou está inativo."></textarea>
                    </div>

                    <div class="d-flex justify-content-center gap-3">

                        <a href="lista.php" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-xmark me-2"></i> Não
                        </a>

                        <a href="lista.php" class="btn btn-danger px-4">
                            <i class="fas fa-check me-2"></i> Sim
                        </a>

                    </div>

                </div>

            </div>

        </section>
    </main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>