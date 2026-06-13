<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Localizações';
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
                        Deseja remover esta localização da listagem?
                    </p>

                    <h4 class="mb-4">
                        <strong>Hospital Central - UCI - Sala 1</strong>
                    </h4>

                    <div class="mb-4">

                        <span class="d-block mb-2">
                            <i class="fas fa-layer-group me-2"></i>
                            <strong>Categoria:</strong> Área clínica crítica
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-building me-2"></i>
                            <strong>Edifício:</strong> Hospital Central
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-stairs me-2"></i>
                            <strong>Piso:</strong> Piso 2
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-hospital me-2"></i>
                            <strong>Serviço:</strong> Unidade de Cuidados Intensivos
                        </span>

                        <span class="d-block">
                            <i class="fas fa-door-open me-2"></i>
                            <strong>Sala / Gabinete:</strong> Sala 1
                        </span>

                    </div>

                    <div class="mb-4 text-start">
                        <label for="motivo_remocao" class="form-label">
                            Motivo da remoção
                        </label>

                        <textarea class="form-control" id="motivo_remocao" name="motivo_remocao" rows="3"
                            placeholder="Ex.: localização deixou de existir, foi substituída ou reorganizada."></textarea>
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