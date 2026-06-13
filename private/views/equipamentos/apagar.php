<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Equipamentos';
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
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <p class="mb-2 fs-5">
                        Deseja remover este equipamento da listagem?
                    </p>

                    <h4 class="mb-4">
                        <strong>Monitor Multiparamétrico</strong>
                    </h4>

                    <div class="mb-4">

                        <span class="d-block mb-2">
                            <i class="fas fa-barcode me-2"></i>
                            <strong>Código interno:</strong> 004.002.00
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-hashtag me-2"></i>
                            <strong>N.º série:</strong> MP5-2022-45873
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-circle-check me-2"></i>
                            <strong>Estado:</strong> Ativo
                        </span>

                        <span class="d-block mb-2">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            <strong>Criticidade:</strong> Alta
                        </span>

                        <span class="d-block">
                            <i class="fas fa-location-dot me-2"></i>
                            <strong>Localização:</strong> Hospital Central - Piso 2 - UCI - Sala 1
                        </span>

                    </div>

                    <div class="d-flex justify-content-center gap-3">

                        <a href="lista.php" class="btn btn-outline-secondary px-4">
                            <i class="fa-solid fa-xmark me-2"></i> Não
                        </a>

                        <a href="lista.php" class="btn btn-danger px-4">
                            <i class="fa-solid fa-check me-2"></i> Sim
                        </a>

                    </div>

                </div>

            </div>

        </section>
    </main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>