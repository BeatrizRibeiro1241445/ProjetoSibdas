<?php
require_once __DIR__ . '/includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Área Pessoal';
$body_class = 'area-pessoal-page';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Conteúdo Principal -->
<main class="content area-pessoal-content">

    <!-- Cabeçalho inicial -->
    <section class="area-pessoal-intro">

        <h2>Bem-vindo ao MedInventário</h2>

        <p class="area-pessoal-label">Área reservada</p>

        <p>
            Utilize o painel abaixo para aceder rapidamente à gestão do inventário hospitalar,
            consultar informação e atualizar os conteúdos do site público.
        </p>

    </section>

    <!-- Cartões principais -->
    <section class="area-pessoal-grid">

        <a href="views/equipamentos/lista.php" class="area-card area-card-blue">
            <div class="area-card-icon">
                <i class="fas fa-laptop-medical"></i>
            </div>

            <div>
                <h3>Equipamentos</h3>
                <p>Consultar, registar e organizar os equipamentos médicos do inventário.</p>
            </div>
        </a>

        <a href="views/fornecedores/lista.php" class="area-card area-card-yellow">
            <div class="area-card-icon">
                <i class="fas fa-truck-medical"></i>
            </div>

            <div>
                <h3>Fornecedores</h3>
                <p>Gerir empresas, contactos e associações aos equipamentos.</p>
            </div>
        </a>

        <a href="views/localizacoes/lista.php" class="area-card area-card-pink">
            <div class="area-card-icon">
                <i class="fas fa-location-dot"></i>
            </div>

            <div>
                <h3>Localizações</h3>
                <p>Consultar edifícios, pisos, serviços e salas onde existem equipamentos.</p>
            </div>
        </a>

        <a href="views/gestao_conteudos/gestao_conteudos.php" class="area-card area-card-gray">
            <div class="area-card-icon">
                <i class="fas fa-pen-to-square"></i>
            </div>

            <div>
                <h3>Conteúdos do site</h3>
                <p>Simular a edição dos textos, contactos e informação do site público.</p>
            </div>
        </a>

    </section>

    <!-- Dashboard em destaque -->
    <section class="area-dashboard-section">

        <a href="views/dashboard/dashboard.php" class="area-dashboard-card">

            <div class="area-dashboard-icon">
                <i class="fas fa-chart-bar"></i>
            </div>

            <div class="area-dashboard-text">
                <h3>Dashboard</h3>

                <p class="area-dashboard-subtitle">Resumo e indicadores</p>

                <p>
                    Consultar métricas principais, alertas de gestão, garantias, documentação
                    e equipamentos críticos.
                </p>
            </div>

            <div class="area-dashboard-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>

        </a>

    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>