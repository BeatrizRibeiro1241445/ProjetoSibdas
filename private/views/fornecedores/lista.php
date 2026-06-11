<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedInventário - Fornecedores</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="../../../assets/img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../assets/bootstrap/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../../assets/fontawesome/all.min.css">

    <!-- folha de estilos CSS -->
    <link rel="stylesheet" href="../../../assets/css/1241445.css">
</head>

<body>
    <!-- Navbar -->
    <header class="bng-navbar-menu">

        <div>
            <a href="../../area_pessoal.php">
                <img src="../../../assets/img/logo.png" alt="Logo da MedInventário">
            </a>
            <h3>MedInventário</h3>
        </div>

        <div>
            <button id="btn-utilizador" type="button" onclick="mostrarAreaUtilizador()">
                <i class="fas fa-user-circle"></i>
                Utilizador
            </button>
        </div>

    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <h4>Menu</h4>

        <nav>
            <a href="../equipamentos/lista.php">
                <i class="fas fa-laptop-medical"></i> Equipamentos
            </a>

            <a href="../fornecedores/lista.php" class="active">
                <i class="fas fa-truck-medical"></i> Fornecedores
            </a>

            <a href="../localizacoes/lista.php">
                <i class="fas fa-location-dot"></i> Localizações
            </a>

            <a href="../gestao_conteudos/gestao_conteudos.php">
                <i class="fas fa-pen-to-square"></i> Conteúdos do site
            </a>

            <a href="../dashboard/dashboard.php">
                <i class="fas fa-chart-bar"></i> Dashboard
            </a>
        </nav>
    </aside>

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

    <!-- Bootstrap JS -->
    <script src="../../../assets/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- JavaScript -->
    <script src="../../../assets/js/1241445.js"></script>

</body>

</html>