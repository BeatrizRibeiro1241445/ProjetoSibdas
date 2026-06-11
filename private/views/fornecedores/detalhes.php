<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedInventário - Consultar Fornecedor</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="../../../assets/img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../assets/bootstrap/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../../assets/fontawesome/all.min.css">

    <!-- folha de estilos CSS -->
    <link rel="stylesheet" href="../../../assets/css/1241445.css">
</head>

<body class="pagina-novo-equipamento">
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
                        <i class="fas fa-eye"></i> Consultar Fornecedor
                    </strong>
                </h2>

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <hr>

            <ul class="nav nav-tabs mb-4" id="separadoresDetalhesFornecedor" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="detalhes-geral-tab" data-bs-toggle="tab"
                        data-bs-target="#detalhes-geral" type="button" role="tab">
                        Identificação e contactos
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="detalhes-contactos-tab" data-bs-toggle="tab"
                        data-bs-target="#detalhes-contactos" type="button" role="tab">
                        Pessoas de contacto e observações
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="detalhes-equipamentos-tab" data-bs-toggle="tab"
                        data-bs-target="#detalhes-equipamentos" type="button" role="tab">
                        Equipamentos associados
                    </button>
                </li>

            </ul>

            <div class="tab-content" id="conteudoSeparadoresDetalhesFornecedor">

                <!-- Separador 1 -->
                <div class="tab-pane fade show active" id="detalhes-geral" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-building"></i> Identificação do fornecedor
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Nome da empresa</th>
                                        <td>MedTech Portugal</td>
                                    </tr>

                                    <tr>
                                        <th>NIF</th>
                                        <td>509000000</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-phone"></i> Contactos gerais
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Contacto telefónico</th>
                                        <td>+351 220 000 000</td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td>geral@medtech.pt</td>
                                    </tr>

                                    <tr>
                                        <th>Morada</th>
                                        <td>Rua da Saúde, Porto, Portugal</td>
                                    </tr>

                                    <tr>
                                        <th>Website</th>
                                        <td>https://www.medtech.pt</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador 2 -->
                <div class="tab-pane fade" id="detalhes-contactos" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-users"></i> Pessoas de contacto
                            </h3>

                            <table
                                class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
                                <thead>
                                    <tr>
                                        <th>Pessoa de contacto</th>
                                        <th>Telefone</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>Ana Martins</td>
                                        <td>+351 910 000 000</td>
                                    </tr>

                                    <tr>
                                        <td>Pedro Costa</td>
                                        <td>+351 911 111 111</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-circle-info"></i> Observações
                            </h3>

                            <p class="mb-0">
                                Fornecedor responsável por assistência técnica de equipamentos críticos.
                            </p>

                        </div>
                    </div>

                </div>

                <!-- Separador 3 -->
                <div class="tab-pane fade" id="detalhes-equipamentos" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-laptop-medical"></i> Equipamentos associados
                            </h3>

                            <div class="table-responsive tabela-lista-container">
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista">

                                    <thead>
                                        <tr>
                                            <th>Código interno</th>
                                            <th>Equipamento</th>
                                            <th>Número de série</th>
                                            <th>Tipo de associação</th>
                                            <th>Observações da associação</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>004.002.00</td>

                                            <td>
                                                <strong>Monitor Multiparamétrico</strong>
                                            </td>

                                            <td>MP5-2022-45873</td>

                                            <td>Empresa de assistência técnica</td>

                                            <td>Responsável pela manutenção preventiva deste equipamento.</td>
                                        </tr>

                                        <tr>
                                            <td>003.001.00</td>

                                            <td>
                                                <strong>Ventilador Pulmonar</strong>
                                            </td>

                                            <td>EV500-2021-55210</td>

                                            <td>Manutenção preventiva</td>

                                            <td>Entidade responsável pela manutenção anual.</td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="../../../assets/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- JavaScript -->
    <script src="../../../assets/js/1241445.js"></script>

</body>

</html>