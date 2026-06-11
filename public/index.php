<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

$page_title = APP_NAME;
$body_class = '';

include __DIR__ . '/../private/includes/header.php';
include __DIR__ . '/../private/includes/public_nav.php';
?>

<!-- Secção Início -->
<section class="container-texto-generico public-hero" id="inicio">

    <div class="texto-seccao">
        <h1>Sistema Web de Apoio ao Inventário Hospitalar</h1>

        <p>
            A MedInventário ajuda instituições de saúde a organizar, consultar e controlar
            equipamentos médicos de forma simples, centralizada e segura.
        </p>

        <a class="botao-principal" href="#solucao">
            Conhecer a solução
        </a>
    </div>

    <div class="imagem-seccao imagem-hero-public">

        <img src="<?= BASE_URL ?>/assets/img/hospital-digital.png" alt="Hospital digital ligado por rede">
    </div>

</section>

<!-- Secção Quem Somos -->
<section class="container-texto-generico public-section-light" id="quem-somos">

    <div class="imagem-seccao">
        <img src="<?= BASE_URL ?>/assets/img/equipa-biomedica.png" alt="Profissional a usar sistema hospitalar">
    </div>

    <div class="texto-seccao">
        <h2>Quem Somos</h2>

        <p>
            A MedInventário é uma solução digital pensada para apoiar hospitais e serviços de saúde
            na gestão organizada do seu parque tecnológico.
        </p>

        <p>
            A plataforma centraliza informação essencial sobre equipamentos, fornecedores,
            localizações, documentação, garantias e contratos, facilitando o acesso rápido aos dados.
        </p>

        <a class="botao-principal" href="#funcionalidades">
            Ver funcionalidades
        </a>
    </div>

</section>

<!-- Secção Solução -->
<section class="container-texto-generico" id="solucao">

    <div class="texto-seccao">
        <h2>A Nossa Solução</h2>

        <p>
            O objetivo da MedInventário é disponibilizar uma plataforma organizada para apoiar
            o ciclo de vida dos equipamentos médicos, desde o registo inicial até à sua consulta,
            atualização ou desativação.
        </p>

        <p>
            A aplicação permite melhorar a rastreabilidade, facilitar a pesquisa de informação
            e apoiar decisões relacionadas com manutenção, garantias e documentação.
        </p>
    </div>

    <div class="imagem-seccao">
        <img src="<?= BASE_URL ?>/assets/img/solução.png" alt="Plataforma de gestão de equipamentos médicos">
    </div>

</section>

<!-- Secção Funcionalidades -->
<section class="container-texto-generico public-section-light" id="funcionalidades">

    <div>
        <h2>Funcionalidades</h2>

        <p>
            A MedInventário organiza os principais módulos necessários para uma gestão clara,
            simples e centralizada do inventário hospitalar.
        </p>

        <div class="public-cards">

            <div class="public-card">
                <i class="fas fa-laptop-medical"></i>
                <h3>Equipamentos</h3>
                <p>Registo, consulta e atualização dos equipamentos médicos existentes.</p>
            </div>

            <div class="public-card">
                <i class="fas fa-location-dot"></i>
                <h3>Localizações</h3>
                <p>Associação dos equipamentos a edifícios, pisos, serviços e salas.</p>
            </div>

            <div class="public-card">
                <i class="fas fa-truck-medical"></i>
                <h3>Fornecedores</h3>
                <p>Gestão de empresas, contactos e associações aos equipamentos.</p>
            </div>

            <div class="public-card">
                <i class="fas fa-file-medical"></i>
                <h3>Documentação</h3>
                <p>Organização de manuais, certificados e documentos técnicos.</p>
            </div>

            <div class="public-card">
                <i class="fas fa-file-contract"></i>
                <h3>Garantias</h3>
                <p>Consulta de garantias, contratos e entidades responsáveis.</p>
            </div>

            <div class="public-card">
                <i class="fas fa-chart-simple"></i>
                <h3>Dashboard</h3>
                <p>Indicadores, alertas e resumo do estado do inventário.</p>
            </div>

        </div>
    </div>

</section>

<!-- Secção Dashboard -->
<section class="container-texto-generico">

    <div class="texto-seccao">
        <h2>Informação centralizada para melhor decisão</h2>

        <p>
            Através de indicadores e alertas, a solução permite identificar equipamentos críticos,
            garantias próximas do fim, documentação em falta e estados de funcionamento.
        </p>

        <p>
            Esta informação ajuda os serviços técnicos e administrativos a acompanhar o inventário
            de forma mais rápida e estruturada.
        </p>
    </div>

    <div class="imagem-seccao">
        <img src="<?= BASE_URL ?>/assets/img/dashboard.png" alt="Dashboard com alertas de inventário hospitalar">
    </div>

</section>

<?php
include __DIR__ . '/../private/includes/public_footer.php';
include __DIR__ . '/../private/includes/footer.php';
?>