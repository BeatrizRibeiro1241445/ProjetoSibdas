<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedInventário - Dashboard</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="../../../assets/img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../assets/bootstrap/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../../assets/fontawesome/all.min.css">

    <!-- folha de estilos CSS -->
    <link rel="stylesheet" href="../../../assets/css/1241445.css">
</head>

<body class="pagina-dashboard">
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

            <a href="../fornecedores/lista.php">
                <i class="fas fa-truck-medical"></i> Fornecedores
            </a>

            <a href="../localizacoes/lista.php">
                <i class="fas fa-location-dot"></i> Localizações
            </a>

            <a href="../gestao_conteudos/gestao_conteudos.php">
                <i class="fas fa-pen-to-square"></i> Conteúdos do site
            </a>

            <a href="../dashboard/dashboard.php" class="active">
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
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </strong>
                </h2>
            </div>

            <hr>

            <div class="dashboard-intro mb-4">
                <div>
                    <h3>Resumo e indicadores</h3>
                    <p>
                        Consulta rápida dos principais números do inventário hospitalar,
                        alertas de gestão e distribuição dos equipamentos.
                    </p>
                </div>

                <i class="fas fa-chart-line"></i>
            </div>

            <h3 class="dashboard-titulo-seccao">
                <i class="fas fa-chart-simple"></i> Métricas principais
            </h3>

            <div class="row mt-4">

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-total"
                        data-secao="secResumoServico" data-collapse="">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-laptop-medical"></i> Total
                            </h5>
                            <p class="card-text">1500</p>
                            <p>Equipamentos registados</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-ativos"
                        data-secao="secResumoServico" data-collapse="">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-circle-check"></i> Ativos
                            </h5>
                            <p class="card-text">1280</p>
                            <p>Equipamentos em utilização</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-manutencao"
                        data-secao="secResumoServico" data-collapse="">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-screwdriver-wrench"></i> Manutenção
                            </h5>
                            <p class="card-text">145</p>
                            <p>Equipamentos em manutenção</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-inativos"
                        data-secao="secResumoServico" data-collapse="">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-circle-xmark"></i> Inativos
                            </h5>
                            <p class="card-text">75</p>
                            <p>Equipamentos fora de utilização</p>
                        </div>
                    </div>
                </div>

            </div>

            <h3 class="dashboard-titulo-seccao mt-5">
                <i class="fas fa-triangle-exclamation"></i> Alertas de gestão
            </h3>

            <div class="row mt-4">

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                        data-secao="secGarantiasExpiradas" data-collapse="collapseGarantiasExpiradas">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-calendar-xmark"></i> Garantia expirada
                            </h5>
                            <p class="card-text">42</p>
                            <p>Equipamentos com garantia expirada</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-manutencao"
                        data-secao="secGarantiasExpirar" data-collapse="collapseGarantiasExpirar">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-calendar-days"></i> Garantia a expirar
                            </h5>
                            <p class="card-text">18</p>
                            <p>Nos próximos 30 dias</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-inativos"
                        data-secao="secSemDocumentacao" data-collapse="collapseSemDocumentacao">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-file-circle-exclamation"></i> Sem documentação
                            </h5>
                            <p class="card-text">63</p>
                            <p>Equipamentos sem documentação associada</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                        data-secao="secCriticidadeElevada" data-collapse="collapseCriticidadeElevada">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-triangle-exclamation"></i> Criticidade elevada
                            </h5>
                            <p class="card-text">96</p>
                            <p>Equipamentos críticos ou de suporte de vida</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-2">

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-manutencao"
                        data-secao="secDocumentosExpirar" data-collapse="collapseDocumentosExpirar">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-file-circle-exclamation"></i> Documentos a expirar
                            </h5>
                            <p class="card-text">12</p>
                            <p>Documentos com validade próxima</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                        data-secao="secDocumentosExpirados" data-collapse="collapseDocumentosExpirados">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-file-circle-xmark"></i> Documentos expirados
                            </h5>
                            <p class="card-text">9</p>
                            <p>Documentos fora de validade</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                        data-secao="secAvariadosForaServico" data-collapse="collapseAvariadosForaServico">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-screwdriver-wrench"></i> Avariados / fora de serviço
                            </h5>
                            <p class="card-text">34</p>
                            <p>Equipamentos avariados ou fora de serviço</p>
                        </div>
                    </div>
                </div>

            </div>

            <h3 class="dashboard-titulo-seccao mt-5" id="secResumoServico">
                <i class="fas fa-hospital"></i> Resumo por serviço
            </h3>

            <div class="table-responsive tabela-lista-container">
                <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                    <thead>
                        <tr>
                            <th>Serviço / Departamento</th>
                            <th>Total de equipamentos</th>
                            <th>Ativos</th>
                            <th>Em manutenção</th>
                            <th>Inativos</th>
                            <th>Suporte de vida</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Unidade de Cuidados Intensivos</td>
                            <td>320</td>
                            <td>285</td>
                            <td>25</td>
                            <td>10</td>
                            <td>85</td>
                        </tr>

                        <tr>
                            <td>Bloco Operatório</td>
                            <td>410</td>
                            <td>360</td>
                            <td>38</td>
                            <td>12</td>
                            <td>64</td>
                        </tr>

                        <tr>
                            <td>Urgência</td>
                            <td>280</td>
                            <td>240</td>
                            <td>28</td>
                            <td>12</td>
                            <td>42</td>
                        </tr>

                        <tr>
                            <td>Consulta Externa</td>
                            <td>190</td>
                            <td>165</td>
                            <td>15</td>
                            <td>10</td>
                            <td>8</td>
                        </tr>

                        <tr>
                            <td>Imagiologia</td>
                            <td>300</td>
                            <td>230</td>
                            <td>39</td>
                            <td>31</td>
                            <td>16</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="dashboard-titulo-seccao mt-5">
                <i class="fas fa-list-check"></i> Análise detalhada
            </h3>

            <div class="accordion mb-4 dashboard-accordion" id="accordionDashboard">

                <div class="accordion-item" id="secGarantiasExpiradas">
                    <h2 class="accordion-header" id="headingGarantiasExpiradas">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseGarantiasExpiradas" aria-expanded="false"
                            aria-controls="collapseGarantiasExpiradas">
                            <strong>
                                <i class="fas fa-calendar-xmark"></i> Equipamentos com garantia expirada
                            </strong>
                        </button>
                    </h2>

                    <div id="collapseGarantiasExpiradas" class="accordion-collapse collapse"
                        aria-labelledby="headingGarantiasExpiradas" data-bs-parent="#accordionDashboard">

                        <div class="accordion-body">

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                                <thead>
                                    <tr>
                                        <th>Código interno</th>
                                        <th>Equipamento</th>
                                        <th>Serviço</th>
                                        <th>Fim da garantia</th>
                                        <th>Fornecedor / Entidade</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>011.003.00</td>
                                        <td>Aspirador Cirúrgico</td>
                                        <td>Bloco Operatório</td>
                                        <td>2024-12-20</td>
                                        <td>Hospital Devices S.A.</td>
                                    </tr>

                                    <tr>
                                        <td>006.004.00</td>
                                        <td>Eletrocardiógrafo</td>
                                        <td>Urgência</td>
                                        <td>2025-01-15</td>
                                        <td>MedTech Portugal</td>
                                    </tr>

                                    <tr>
                                        <td>012.001.00</td>
                                        <td>Monitor de Transporte</td>
                                        <td>Consulta Externa</td>
                                        <td>2025-02-01</td>
                                        <td>MedTech Portugal</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="accordion-item" id="secGarantiasExpirar">
                    <h2 class="accordion-header" id="headingGarantiasExpirar">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseGarantiasExpirar" aria-expanded="false"
                            aria-controls="collapseGarantiasExpirar">
                            <strong>
                                <i class="fas fa-calendar-days"></i> Equipamentos com garantia a expirar nos próximos 30
                                dias
                            </strong>
                        </button>
                    </h2>

                    <div id="collapseGarantiasExpirar" class="accordion-collapse collapse"
                        aria-labelledby="headingGarantiasExpirar" data-bs-parent="#accordionDashboard">

                        <div class="accordion-body">

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                                <thead>
                                    <tr>
                                        <th>Código interno</th>
                                        <th>Equipamento</th>
                                        <th>Serviço</th>
                                        <th>Fim da garantia</th>
                                        <th>Fornecedor / Entidade</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>007.001.00</td>
                                        <td>Bomba de Infusão</td>
                                        <td>Bloco Operatório</td>
                                        <td>2025-06-10</td>
                                        <td>Hospital Devices S.A.</td>
                                    </tr>

                                    <tr>
                                        <td>006.004.00</td>
                                        <td>Eletrocardiógrafo</td>
                                        <td>Urgência</td>
                                        <td>2025-06-18</td>
                                        <td>MedTech Portugal</td>
                                    </tr>

                                    <tr>
                                        <td>009.002.00</td>
                                        <td>Desfibrilhador</td>
                                        <td>Urgência</td>
                                        <td>2025-06-25</td>
                                        <td>MedTech Portugal</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="accordion-item" id="secSemDocumentacao">
                    <h2 class="accordion-header" id="headingSemDocumentacao">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSemDocumentacao" aria-expanded="false"
                            aria-controls="collapseSemDocumentacao">
                            <strong>
                                <i class="fas fa-file-circle-exclamation"></i> Equipamentos sem documentação associada
                            </strong>
                        </button>
                    </h2>

                    <div id="collapseSemDocumentacao" class="accordion-collapse collapse"
                        aria-labelledby="headingSemDocumentacao" data-bs-parent="#accordionDashboard">

                        <div class="accordion-body">

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                                <thead>
                                    <tr>
                                        <th>Código interno</th>
                                        <th>Equipamento</th>
                                        <th>Serviço</th>
                                        <th>Localização</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>014.002.00</td>
                                        <td>Oxímetro de Pulso</td>
                                        <td>Urgência</td>
                                        <td>Sala 3</td>
                                        <td>Ativo</td>
                                    </tr>

                                    <tr>
                                        <td>015.001.00</td>
                                        <td>Termómetro Clínico Digital</td>
                                        <td>Consulta Externa</td>
                                        <td>Gabinete 4</td>
                                        <td>Ativo</td>
                                    </tr>

                                    <tr>
                                        <td>016.003.00</td>
                                        <td>Carro de Emergência</td>
                                        <td>UCI</td>
                                        <td>Sala 1</td>
                                        <td>Ativo</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="accordion-item" id="secCriticidadeElevada">
                    <h2 class="accordion-header" id="headingCriticidadeElevada">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseCriticidadeElevada" aria-expanded="false"
                            aria-controls="collapseCriticidadeElevada">
                            <strong>
                                <i class="fas fa-triangle-exclamation"></i> Equipamentos de criticidade elevada
                            </strong>
                        </button>
                    </h2>

                    <div id="collapseCriticidadeElevada" class="accordion-collapse collapse"
                        aria-labelledby="headingCriticidadeElevada" data-bs-parent="#accordionDashboard">

                        <div class="accordion-body">

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                                <thead>
                                    <tr>
                                        <th>Código interno</th>
                                        <th>Equipamento</th>
                                        <th>Serviço</th>
                                        <th>Localização</th>
                                        <th>Criticidade</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>003.001.00</td>
                                        <td>Ventilador Pulmonar</td>
                                        <td>Unidade de Cuidados Intensivos</td>
                                        <td>Sala 1</td>
                                        <td>Alta</td>
                                        <td>Ativo</td>
                                    </tr>

                                    <tr>
                                        <td>004.002.00</td>
                                        <td>Monitor Multiparamétrico</td>
                                        <td>Unidade de Cuidados Intensivos</td>
                                        <td>Sala 1</td>
                                        <td>Alta</td>
                                        <td>Ativo</td>
                                    </tr>

                                    <tr>
                                        <td>009.002.00</td>
                                        <td>Desfibrilhador</td>
                                        <td>Urgência</td>
                                        <td>Sala 3</td>
                                        <td>Alta</td>
                                        <td>Ativo</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="accordion-item" id="secDocumentosExpirar">
                    <h2 class="accordion-header" id="headingDocumentosExpirar">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseDocumentosExpirar" aria-expanded="false"
                            aria-controls="collapseDocumentosExpirar">
                            <strong>
                                <i class="fas fa-file-circle-exclamation"></i> Documentos a expirar
                            </strong>
                        </button>
                    </h2>

                    <div id="collapseDocumentosExpirar" class="accordion-collapse collapse"
                        aria-labelledby="headingDocumentosExpirar" data-bs-parent="#accordionDashboard">

                        <div class="accordion-body">

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                                <thead>
                                    <tr>
                                        <th>Código interno</th>
                                        <th>Equipamento</th>
                                        <th>Documento</th>
                                        <th>Tipo de documento</th>
                                        <th>Data de validade</th>
                                        <th>Fornecedor associado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>004.002.00</td>
                                        <td>Monitor Multiparamétrico</td>
                                        <td>Certificado de Calibração</td>
                                        <td>Certificado</td>
                                        <td>2025-06-15</td>
                                        <td>MedTech Portugal</td>
                                    </tr>

                                    <tr>
                                        <td>003.001.00</td>
                                        <td>Ventilador Pulmonar</td>
                                        <td>Relatório Técnico de Verificação</td>
                                        <td>Relatório técnico</td>
                                        <td>2025-06-20</td>
                                        <td>MedTech Portugal</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="accordion-item" id="secDocumentosExpirados">
                    <h2 class="accordion-header" id="headingDocumentosExpirados">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseDocumentosExpirados" aria-expanded="false"
                            aria-controls="collapseDocumentosExpirados">
                            <strong>
                                <i class="fas fa-file-circle-xmark"></i> Documentos expirados
                            </strong>
                        </button>
                    </h2>

                    <div id="collapseDocumentosExpirados" class="accordion-collapse collapse"
                        aria-labelledby="headingDocumentosExpirados" data-bs-parent="#accordionDashboard">

                        <div class="accordion-body">

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                                <thead>
                                    <tr>
                                        <th>Código interno</th>
                                        <th>Equipamento</th>
                                        <th>Documento</th>
                                        <th>Tipo de documento</th>
                                        <th>Data de validade</th>
                                        <th>Fornecedor associado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>007.001.00</td>
                                        <td>Bomba de Infusão</td>
                                        <td>Certificado de Calibração</td>
                                        <td>Certificado</td>
                                        <td>2025-01-10</td>
                                        <td>Hospital Devices S.A.</td>
                                    </tr>

                                    <tr>
                                        <td>006.004.00</td>
                                        <td>Eletrocardiógrafo</td>
                                        <td>Relatório Técnico</td>
                                        <td>Relatório técnico</td>
                                        <td>2025-02-18</td>
                                        <td>MedTech Portugal</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="accordion-item" id="secAvariadosForaServico">
                    <h2 class="accordion-header" id="headingAvariadosForaServico">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseAvariadosForaServico" aria-expanded="false"
                            aria-controls="collapseAvariadosForaServico">
                            <strong>
                                <i class="fas fa-screwdriver-wrench"></i> Equipamentos avariados ou fora de serviço
                            </strong>
                        </button>
                    </h2>

                    <div id="collapseAvariadosForaServico" class="accordion-collapse collapse"
                        aria-labelledby="headingAvariadosForaServico" data-bs-parent="#accordionDashboard">

                        <div class="accordion-body">

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista">
                                <thead>
                                    <tr>
                                        <th>Código interno</th>
                                        <th>Equipamento</th>
                                        <th>Serviço</th>
                                        <th>Localização</th>
                                        <th>Estado</th>
                                        <th>Observação</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>018.002.00</td>
                                        <td>Monitor de Sinais Vitais</td>
                                        <td>Urgência</td>
                                        <td>Sala 3</td>
                                        <td>Avariado</td>
                                        <td>A aguardar avaliação técnica.</td>
                                    </tr>

                                    <tr>
                                        <td>020.001.00</td>
                                        <td>Bomba de Seringa</td>
                                        <td>Unidade de Cuidados Intensivos</td>
                                        <td>Sala 1</td>
                                        <td>Fora de serviço</td>
                                        <td>Equipamento retirado temporariamente da utilização.</td>
                                    </tr>

                                    <tr>
                                        <td>021.004.00</td>
                                        <td>Aspirador Portátil</td>
                                        <td>Bloco Operatório</td>
                                        <td>Sala 2</td>
                                        <td>Avariado</td>
                                        <td>Necessita de reparação.</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>

            <h3 class="dashboard-titulo-seccao mt-5">
                <i class="fas fa-chart-pie"></i> Distribuições gerais
            </h3>

            <div class="row mt-4">

                <div class="col-12 col-lg-6 mb-4">
                    <div class="card shadow-sm h-100 dashboard-grafico-card">
                        <div class="card-body">

                            <h4 id="secDistribuicaoCategoria" class="text-center">
                                <i class="fas fa-chart-pie"></i> Distribuição por categoria
                            </h4>

                            <div class="row align-items-center mt-4">

                                <div class="col-12 col-md-6 text-center">
                                    <div class="grafico-pie grafico-categoria"></div>
                                </div>

                                <div class="col-12 col-md-6 mt-4 mt-md-0">

                                    <p class="legenda-grafico">
                                        <span class="cor-grafico azul"></span>
                                        <strong>Monitorização</strong><br>
                                        420 equipamentos — 28%
                                    </p>

                                    <p class="legenda-grafico">
                                        <span class="cor-grafico verde"></span>
                                        <strong>Suporte de vida</strong><br>
                                        215 equipamentos — 14%
                                    </p>

                                    <p class="legenda-grafico">
                                        <span class="cor-grafico amarelo"></span>
                                        <strong>Infusão</strong><br>
                                        310 equipamentos — 21%
                                    </p>

                                    <p class="legenda-grafico">
                                        <span class="cor-grafico vermelho"></span>
                                        <strong>Diagnóstico</strong><br>
                                        280 equipamentos — 19%
                                    </p>

                                    <p class="legenda-grafico">
                                        <span class="cor-grafico cinzento"></span>
                                        <strong>Outros</strong><br>
                                        275 equipamentos — 18%
                                    </p>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="card shadow-sm h-100 dashboard-grafico-card">
                        <div class="card-body">

                            <h4 id="secDistribuicaoLocalizacao" class="text-center">
                                <i class="fas fa-chart-pie"></i> Distribuição por localização
                            </h4>

                            <div class="row align-items-center mt-4">

                                <div class="col-12 col-md-6 text-center">
                                    <div class="grafico-pie grafico-localizacao"></div>
                                </div>

                                <div class="col-12 col-md-6 mt-4 mt-md-0">

                                    <p class="legenda-grafico">
                                        <span class="cor-grafico azul"></span>
                                        <strong>Hospital Central</strong><br>
                                        1310 equipamentos — 87%
                                    </p>

                                    <p class="legenda-grafico">
                                        <span class="cor-grafico verde"></span>
                                        <strong>Consulta Externa</strong><br>
                                        190 equipamentos — 13%
                                    </p>

                                </div>

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