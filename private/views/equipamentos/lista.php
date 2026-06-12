<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Equipamentos';
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
                        <i class="fas fa-laptop-medical"></i> Gestão de Equipamentos
                    </strong>
                </h2>

                <a href="novo.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo equipamento
                </a>
            </div>

            <hr>

            <!-- Pesquisa e filtros -->
            <div class="accordion mb-5" id="accordionPesquisaEquipamentos">

                <div class="accordion-item border-0 shadow-sm">
                    <h2 class="accordion-header" id="headingPesquisaEquipamentos">
                        <button class="accordion-button collapsed justify-content-center text-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapsePesquisaEquipamentos"
                            aria-expanded="false" aria-controls="collapsePesquisaEquipamentos">

                            <strong>
                                <i class="fas fa-magnifying-glass"></i> Pesquisa, filtros e ordenação
                            </strong>

                        </button>
                    </h2>

                    <div id="collapsePesquisaEquipamentos" class="accordion-collapse collapse"
                        aria-labelledby="headingPesquisaEquipamentos" data-bs-parent="#accordionPesquisaEquipamentos">

                        <div class="accordion-body bg-light">

                            <form action="#" method="get" class="filtros-equipamentos">

                                <div>
                                    <label for="filtro_codigo" class="form-label fw-semibold">Código interno</label>
                                    <input type="text" class="form-control text-center" id="filtro_codigo"
                                        name="filtro_codigo" placeholder="Ex.: 004.002.00">
                                </div>

                                <div>
                                    <label for="filtro_designacao" class="form-label fw-semibold">Designação</label>
                                    <input type="text" class="form-control text-center" id="filtro_designacao"
                                        name="filtro_designacao" placeholder="Ex.: Monitor">
                                </div>

                                <div>
                                    <label for="filtro_serie" class="form-label fw-semibold">Número de série</label>
                                    <input type="text" class="form-control text-center" id="filtro_serie"
                                        name="filtro_serie" placeholder="Ex.: MP5-2022">
                                </div>

                                <div>
                                    <label for="filtro_marca" class="form-label fw-semibold">Marca</label>
                                    <input type="text" class="form-control text-center" id="filtro_marca"
                                        name="filtro_marca" placeholder="Ex.: Philips">
                                </div>

                                <div>
                                    <label for="filtro_estado" class="form-label fw-semibold">Estado</label>
                                    <select class="form-select text-center" id="filtro_estado" name="filtro_estado">
                                        <option value="">Todos</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="em_manutencao">Em manutenção</option>
                                        <option value="inativo">Inativo</option>
                                        <option value="avariado">Avariado</option>
                                        <option value="fora_servico">Fora de serviço</option>
                                        <option value="abatido">Abatido</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="filtro_criticidade" class="form-label fw-semibold">Criticidade</label>
                                    <select class="form-select text-center" id="filtro_criticidade"
                                        name="filtro_criticidade">
                                        <option value="">Todas</option>
                                        <option value="baixa">Baixa</option>
                                        <option value="media">Média</option>
                                        <option value="alta">Alta</option>
                                        <option value="suporte_vida">Suporte de vida</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="filtro_categoria" class="form-label fw-semibold">Categoria /
                                        Grupo</label>
                                    <input type="text" class="form-control text-center" id="filtro_categoria"
                                        name="filtro_categoria" placeholder="Ex.: Monitorização">
                                </div>

                                <div>
                                    <label for="filtro_localizacao" class="form-label fw-semibold">Localização</label>
                                    <input type="text" class="form-control text-center" id="filtro_localizacao"
                                        name="filtro_localizacao" placeholder="Ex.: UCI">
                                </div>

                                <div>
                                    <label for="ordenar_por" class="form-label fw-semibold">Ordenar por</label>
                                    <select class="form-select text-center" id="ordenar_por" name="ordenar_por">
                                        <option value="">Sem ordenação</option>
                                        <option value="codigo_crescente">Código interno crescente</option>
                                        <option value="codigo_decrescente">Código interno decrescente</option>
                                        <option value="designacao_az">Designação A-Z</option>
                                        <option value="designacao_za">Designação Z-A</option>
                                        <option value="criticidade">Criticidade</option>
                                        <option value="estado">Estado</option>
                                    </select>
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
                            <th>Código interno</th>
                            <th>Designação</th>
                            <th>N.º Série</th>
                            <th>Marca / Modelo</th>
                            <th>Estado</th>
                            <th>Criticidade</th>
                            <th>Localização</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><strong>004.002.00</strong></td>
                            <td>Monitor Multiparamétrico</td>
                            <td>MP5-2022-45873</td>
                            <td>Philips / IntelliVue MP5</td>
                            <td class="table-success fw-bold">Ativo</td>
                            <td class="table-danger fw-bold">Alta</td>
                            <td>Hospital Central - Piso 2 - UCI - Sala 1</td>

                            <td>
                                <div class="acoes-tabela">
                                    <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Arquivar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>007.001.00</strong></td>
                            <td>Bomba de Infusão</td>
                            <td>INF-2020-88321</td>
                            <td>B. Braun / Infusomat Space</td>
                            <td class="table-warning fw-bold">Em manutenção</td>
                            <td class="table-warning fw-bold">Média</td>
                            <td>Hospital Central - Piso 1 - Bloco Operatório - Sala 2</td>

                            <td>
                                <div class="acoes-tabela">
                                    <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Arquivar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>003.001.00</strong></td>
                            <td>Ventilador Pulmonar</td>
                            <td>EV500-2021-55210</td>
                            <td>Evita Medical / EV500</td>
                            <td class="table-success fw-bold">Ativo</td>
                            <td class="table-danger fw-bold">Alta</td>
                            <td>Hospital Central - Piso 2 - UCI - Sala 1</td>

                            <td>
                                <div class="acoes-tabela">
                                    <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Arquivar">
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