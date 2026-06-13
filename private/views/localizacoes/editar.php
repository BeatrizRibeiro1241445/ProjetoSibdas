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

            <div class="actions-top">
                <h2>
                    <strong>
                        <i class="fas fa-pen"></i> Editar Localização
                    </strong>
                </h2>

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <hr>

            <form action="#" method="post" class="formulario-equipamento">

                <div class="card mb-4">
                    <div class="card-body">

                        <h3>
                            <i class="fas fa-location-dot"></i> Dados da localização
                        </h3>

                        <div class="row mb-3">

                            <div class="col-12 col-md-6">
                                <label for="categoria" class="form-label">Categoria</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="categoria"
                                    name="categoria" value="Área clínica crítica">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edificio" class="form-label">Edifício</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="edificio"
                                    name="edificio" value="Hospital Central">
                            </div>

                        </div>

                        <div class="row mb-3">

                            <div class="col-12 col-md-4">
                                <label for="piso" class="form-label">Piso</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="piso"
                                    name="piso" value="Piso 2">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="servico" class="form-label">Serviço / Departamento</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="servico"
                                    name="servico" value="Unidade de Cuidados Intensivos">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="sala" class="form-label">Sala / Gabinete</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="sala"
                                    name="sala" value="Sala 1">
                            </div>

                        </div>

                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">

                        <h3>
                            <i class="fas fa-circle-info"></i> Observações
                        </h3>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes"
                                rows="4">Localização destinada a equipamentos críticos e de suporte de vida.</textarea>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="lista.php" class="btn btn-outline-secondary botao-anterior">
                        Cancelar
                    </a>

                    <button type="button" class="btn btn-primary" onclick="validarLocalizacao()">
                        Guardar alterações
                    </button>

                </div>

            </form>

            <p id="mensagem-formulario"></p>

        </section>
    </main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>