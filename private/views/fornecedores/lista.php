<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Fornecedores';
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
                        <i class="fas fa-truck-medical"></i> Gestão de Fornecedores
                    </strong>
                </h2>

                <a href="novo.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo fornecedor
                </a>
            </div>

            <hr>

            <!-- Pesquisa e filtros -->
            <div class="accordion mb-5" id="accordionPesquisaFornecedores">

                <div class="accordion-item border-0 shadow-sm">

                    <h2 class="accordion-header" id="headingPesquisaFornecedores">
                        <button class="accordion-button collapsed justify-content-center text-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapsePesquisaFornecedores"
                            aria-expanded="false" aria-controls="collapsePesquisaFornecedores">

                            <strong>
                                <i class="fas fa-magnifying-glass"></i> Pesquisa e filtros
                            </strong>

                        </button>
                    </h2>

                    <div id="collapsePesquisaFornecedores" class="accordion-collapse collapse"
                        aria-labelledby="headingPesquisaFornecedores" data-bs-parent="#accordionPesquisaFornecedores">

                        <div class="accordion-body bg-light">

                            <form action="#" method="get" class="filtros-equipamentos">

                                <div>
                                    <label for="filtro_nome" class="form-label fw-semibold">Nome da empresa</label>
                                    <input type="text" class="form-control text-center" id="filtro_nome"
                                        name="filtro_nome" placeholder="Ex.: MedTech Portugal">
                                </div>

                                <div>
                                    <label for="filtro_nif" class="form-label fw-semibold">NIF</label>
                                    <input type="text" class="form-control text-center" id="filtro_nif"
                                        name="filtro_nif" placeholder="Ex.: 509000000">
                                </div>

                                <div>
                                    <label for="filtro_tipo" class="form-label fw-semibold">Tipo de fornecedor</label>

                                    <select class="form-select text-center" id="filtro_tipo" name="filtro_tipo">
                                        <option value="">Todos</option>
                                        <option value="fabricante">Fabricante</option>
                                        <option value="fornecedor_comercial">Fornecedor comercial</option>
                                        <option value="assistencia_tecnica">Empresa de assistência técnica</option>
                                        <option value="consumiveis_acessorios">Fornecedor de consumíveis/acessórios
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="filtro_equipamento" class="form-label fw-semibold">
                                        Equipamento associado
                                    </label>
                                    <input type="text" class="form-control text-center" id="filtro_equipamento"
                                        name="filtro_equipamento" placeholder="Ex.: Monitor Multiparamétrico">
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
                            <th>Empresa</th>
                            <th>NIF</th>
                            <th>Tipo</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <strong>MedTech Portugal</strong>
                            </td>

                            <td>509000000</td>

                            <td>Empresa de assistência técnica</td>

                            <td>+351 220 000 000</td>

                            <td>geral@medtech.pt</td>

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
                            <td>
                                <strong>Hospital Devices S.A.</strong>
                            </td>

                            <td>508111222</td>

                            <td>Fornecedor comercial</td>

                            <td>+351 210 000 000</td>

                            <td>info@hospitaldevices.pt</td>

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
                            <td>
                                <strong>Philips Medical Systems</strong>
                            </td>

                            <td>501222333</td>

                            <td>Fabricante</td>

                            <td>+351 211 222 333</td>

                            <td>support@philipsmedical.pt</td>

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
                            <td>
                                <strong>Consumíveis Clínicos Norte</strong>
                            </td>

                            <td>506333444</td>

                            <td>Fornecedor de consumíveis/acessórios</td>

                            <td>+351 225 333 444</td>

                            <td>geral@consumiveisnorte.pt</td>

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