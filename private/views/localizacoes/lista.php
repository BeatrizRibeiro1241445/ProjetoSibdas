<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Localizações';
$body_class = '';

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
                        <i class="fas fa-location-dot"></i> Gestão de Localizações
                    </strong>
                </h2>

                <a href="novo.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova localização
                </a>
            </div>

            <hr>

            <!-- Pesquisa e filtros -->
            <div class="accordion mb-5" id="accordionPesquisaLocalizacoes">

                <div class="accordion-item border-0 shadow-sm">
                    <h2 class="accordion-header" id="headingPesquisaLocalizacoes">
                        <button class="accordion-button collapsed justify-content-center text-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapsePesquisaLocalizacoes"
                            aria-expanded="false" aria-controls="collapsePesquisaLocalizacoes">

                            <strong>
                                <i class="fas fa-magnifying-glass"></i> Pesquisa e filtros
                            </strong>

                        </button>
                    </h2>

                    <div id="collapsePesquisaLocalizacoes" class="accordion-collapse collapse"
                        aria-labelledby="headingPesquisaLocalizacoes" data-bs-parent="#accordionPesquisaLocalizacoes">

                        <div class="accordion-body bg-light">

                            <form action="#" method="get" class="filtros-equipamentos">

                                <div>
                                    <label for="filtro_codigo_equipamento" class="form-label fw-semibold">
                                        Código do equipamento
                                    </label>
                                    <input type="text" class="form-control text-center" id="filtro_codigo_equipamento"
                                        name="filtro_codigo_equipamento" placeholder="Ex.: 004.002.00">
                                </div>

                                <div>
                                    <label for="filtro_nome_equipamento" class="form-label fw-semibold">
                                        Nome do equipamento
                                    </label>
                                    <input type="text" class="form-control text-center" id="filtro_nome_equipamento"
                                        name="filtro_nome_equipamento" placeholder="Ex.: Monitor Multiparamétrico">
                                </div>

                                <div>
                                    <label for="filtro_categoria" class="form-label fw-semibold">Categoria</label>
                                    <input type="text" class="form-control text-center" id="filtro_categoria"
                                        name="filtro_categoria" placeholder="Ex.: Área clínica crítica">
                                </div>

                                <div>
                                    <label for="filtro_edificio" class="form-label fw-semibold">Edifício</label>
                                    <input type="text" class="form-control text-center" id="filtro_edificio"
                                        name="filtro_edificio" placeholder="Ex.: Hospital Central">
                                </div>

                                <div>
                                    <label for="filtro_piso" class="form-label fw-semibold">Piso</label>
                                    <input type="text" class="form-control text-center" id="filtro_piso"
                                        name="filtro_piso" placeholder="Ex.: Piso 2">
                                </div>

                                <div>
                                    <label for="filtro_servico" class="form-label fw-semibold">
                                        Serviço / Departamento
                                    </label>
                                    <input type="text" class="form-control text-center" id="filtro_servico"
                                        name="filtro_servico" placeholder="Ex.: UCI">
                                </div>

                                <div>
                                    <label for="filtro_sala" class="form-label fw-semibold">Sala / Gabinete</label>
                                    <input type="text" class="form-control text-center" id="filtro_sala"
                                        name="filtro_sala" placeholder="Ex.: Sala 1">
                                </div>

                                <div class="filtros-botoes">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-magnifying-glass"></i> Pesquisar
                                    </button>

                                    <button type="reset" class="btn btn-secondary">
                                        Limpar
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela -->
            <div class="table-responsive tabela-lista-container">
                <table class="table table-hover table-bordered align-middle text-center tabela-lista">

                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Edifício</th>
                            <th>Piso</th>
                            <th>Serviço / Departamento</th>
                            <th>Sala / Gabinete</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Área clínica crítica</td>
                            <td>Hospital Central</td>
                            <td>Piso 2</td>
                            <td>Unidade de Cuidados Intensivos</td>
                            <td>Sala 1</td>

                            <td>
                                <div class="acoes-tabela">
                                    <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Área cirúrgica</td>
                            <td>Hospital Central</td>
                            <td>Piso 1</td>
                            <td>Bloco Operatório</td>
                            <td>Sala 2</td>

                            <td>
                                <div class="acoes-tabela">
                                    <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Área de urgência</td>
                            <td>Hospital Central</td>
                            <td>Piso 0</td>
                            <td>Urgência</td>
                            <td>Sala 3</td>

                            <td>
                                <div class="acoes-tabela">
                                    <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

        </section>
    </main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>