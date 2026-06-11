<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedInventário - Consultar Equipamento</title>

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
            <a href="../equipamentos/lista.php" class="active">
                <i class="fas fa-laptop-medical"></i> Equipamentos
            </a>

            <a href="../fornecedores/lista.php">
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
                        <i class="fas fa-eye"></i> Consultar Equipamento
                    </strong>
                </h2>

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <hr>

            <ul class="nav nav-tabs mb-4" id="separadoresDetalhesEquipamento" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral"
                        type="button" role="tab">
                        Dados gerais
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fornecedores-tab" data-bs-toggle="tab" data-bs-target="#fornecedores"
                        type="button" role="tab">
                        Fornecedores associados
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao"
                        type="button" role="tab">
                        Localização atual
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documentacao-tab" data-bs-toggle="tab" data-bs-target="#documentacao"
                        type="button" role="tab">
                        Documentação associada
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="garantias-tab" data-bs-toggle="tab" data-bs-target="#garantias"
                        type="button" role="tab">
                        Garantias e contratos
                    </button>
                </li>

            </ul>

            <div class="tab-content" id="conteudoSeparadoresDetalhesEquipamento">

                <!-- Separador: Dados gerais -->
                <div class="tab-pane fade show active" id="geral" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-laptop-medical"></i> Dados gerais do equipamento
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Código interno</th>
                                        <td>004.002.00</td>
                                    </tr>

                                    <tr>
                                        <th>Designação</th>
                                        <td>Monitor Multiparamétrico</td>
                                    </tr>

                                    <tr>
                                        <th>Número de série</th>
                                        <td>MP5-2022-45873</td>
                                    </tr>

                                    <tr>
                                        <th>Categoria / Grupo</th>
                                        <td>Monitorização</td>
                                    </tr>

                                    <tr>
                                        <th>Marca</th>
                                        <td>Philips</td>
                                    </tr>

                                    <tr>
                                        <th>Modelo</th>
                                        <td>IntelliVue MP5</td>
                                    </tr>

                                    <tr>
                                        <th>Estado atual</th>
                                        <td class="table-success fw-bold">Ativo</td>
                                    </tr>

                                    <tr>
                                        <th>Criticidade</th>
                                        <td class="table-danger fw-bold">Alta</td>
                                    </tr>

                                    <tr>
                                        <th>Ano de fabrico</th>
                                        <td>2021</td>
                                    </tr>

                                    <tr>
                                        <th>Data de aquisição</th>
                                        <td>2022-03-15</td>
                                    </tr>

                                    <tr>
                                        <th>Custo de aquisição</th>
                                        <td>3500 €</td>
                                    </tr>

                                    <tr>
                                        <th>Tipo de entrada</th>
                                        <td>Compra</td>
                                    </tr>

                                    <tr>
                                        <th>Observações / utilização</th>
                                        <td>
                                            Equipamento utilizado para monitorização contínua de sinais vitais
                                            em contexto de cuidados intensivos.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador: Fornecedores associados -->
                <div class="tab-pane fade" id="fornecedores" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-truck-medical"></i> Fornecedores associados
                            </h3>

                            <table
                                class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Tipo de associação</th>
                                        <th>Contacto telefónico</th>
                                        <th>Email</th>
                                        <th>Website</th>
                                        <th>Pessoas de contacto</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>MedTech Portugal</strong>
                                        </td>

                                        <td>Empresa de assistência técnica</td>

                                        <td>+351 220 000 000</td>

                                        <td>geral@medtech.pt</td>

                                        <td>www.medtech.pt</td>

                                        <td class="text-start">
                                            Ana Martins - +351 910 000 000<br>
                                            Pedro Costa - +351 911 111 111
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <strong>Philips Medical Systems</strong>
                                        </td>

                                        <td>Fabricante</td>

                                        <td>+351 211 222 333</td>

                                        <td>support@philipsmedical.pt</td>

                                        <td>www.philips.pt</td>

                                        <td class="text-start">
                                            Carla Sousa - +351 912 222 333
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <strong>Hospital Devices S.A.</strong>
                                        </td>

                                        <td>Fornecedor comercial</td>

                                        <td>+351 210 000 000</td>

                                        <td>info@hospitaldevices.pt</td>

                                        <td>www.hospitaldevices.pt</td>

                                        <td class="text-start">
                                            João Pereira - +351 913 333 444<br>
                                            Marta Lopes - +351 914 444 555
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador: Localização atual -->
                <div class="tab-pane fade" id="localizacao" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-location-dot"></i> Localização atual
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Localização</th>
                                        <td>Hospital Central - Piso 2 - UCI - Sala 1</td>
                                    </tr>

                                    <tr>
                                        <th>Data da localização</th>
                                        <td>2025-01-10</td>
                                    </tr>

                                    <tr>
                                        <th>Responsável</th>
                                        <td>Técnico responsável</td>
                                    </tr>

                                    <tr>
                                        <th>Motivo / observação</th>
                                        <td>Instalação inicial no serviço</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-clock-rotate-left"></i> Histórico de localizações
                            </h3>

                            <table
                                class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
                                <thead>
                                    <tr>
                                        <th>Localização</th>
                                        <th>Data</th>
                                        <th>Responsável</th>
                                        <th>Motivo / observação</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>Hospital Central - Piso 2 - UCI - Sala 1</td>
                                        <td>2025-01-10</td>
                                        <td>Técnico responsável</td>
                                        <td>Instalação inicial no serviço</td>
                                    </tr>

                                    <tr>
                                        <td>Hospital Central - Piso 0 - Urgência - Sala 3</td>
                                        <td>2024-09-20</td>
                                        <td>Serviço de Equipamentos</td>
                                        <td>Utilização temporária no serviço de urgência</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador: Documentação associada -->
                <div class="tab-pane fade" id="documentacao" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-file-medical"></i> Documentação associada
                            </h3>

                            <table
                                class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nome do documento</th>
                                        <th>Data</th>
                                        <th>Validade / Expiração</th>
                                        <th>Fornecedor</th>
                                        <th>Ficheiro</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>Manual técnico</td>

                                        <td>Manual Técnico do Equipamento</td>

                                        <td>2025-01-10</td>

                                        <td>Sem validade definida</td>

                                        <td>Philips Medical Systems</td>

                                        <td>
                                            <a href="#" class="text-primary text-decoration-underline fw-semibold">
                                                manual-equipamento.pdf
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Certificado de calibração</td>

                                        <td>Certificado de Calibração</td>

                                        <td>2025-02-15</td>

                                        <td>2026-02-15</td>

                                        <td>MedTech Portugal</td>

                                        <td>
                                            <a href="#" class="text-primary text-decoration-underline fw-semibold">
                                                certificado-equipamento.pdf
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Contrato de manutenção</td>

                                        <td>Contrato de Manutenção Preventiva</td>

                                        <td>2025-03-01</td>

                                        <td>2027-03-01</td>

                                        <td>MedTech Portugal</td>

                                        <td>
                                            <a href="#" class="text-primary text-decoration-underline fw-semibold">
                                                contrato-manutencao.pdf
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador: Garantias e contratos -->
                <div class="tab-pane fade" id="garantias" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-file-contract"></i> Garantias e contratos
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Data de início da garantia</th>
                                        <td>2024-01-15</td>
                                    </tr>

                                    <tr>
                                        <th>Data de fim da garantia</th>
                                        <td>2027-01-15</td>
                                    </tr>

                                    <tr>
                                        <th>Entidade responsável pela garantia</th>
                                        <td>MedTech Portugal</td>
                                    </tr>

                                    <tr>
                                        <th>Existe contrato de manutenção?</th>
                                        <td>Sim</td>
                                    </tr>

                                    <tr>
                                        <th>Tipo de contrato</th>
                                        <td>Manutenção preventiva</td>
                                    </tr>

                                    <tr>
                                        <th>Periodicidade</th>
                                        <td>Anual</td>
                                    </tr>

                                    <tr>
                                        <th>Entidade responsável pelo contrato</th>
                                        <td>MedTech Portugal</td>
                                    </tr>

                                    <tr>
                                        <th>Observações</th>
                                        <td>
                                            Contrato inclui verificação técnica anual e apoio em caso de avaria.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

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