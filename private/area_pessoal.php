<?php
require_once __DIR__ . '/includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Área Pessoal';
$body_class = 'area-pessoal-page';

$perfil = $_SESSION['perfil'] ?? '';

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
            Utilize o painel abaixo para aceder rapidamente às funcionalidades disponíveis
            para o seu perfil de utilizador.
        </p>

    </section>

    <!-- Cartões principais -->
    <section class="area-pessoal-grid">

        <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar', 'profissional_saude'])): ?>
            <a href="views/equipamentos/lista.php" class="area-card area-card-blue">
                <div class="area-card-icon">
                    <i class="fas fa-laptop-medical"></i>
                </div>

                <div>
                    <h3>Equipamentos</h3>
                    <p>Consultar, registar e organizar os equipamentos médicos do inventário.</p>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar'])): ?>
            <a href="views/fornecedores/lista.php" class="area-card area-card-yellow">
                <div class="area-card-icon">
                    <i class="fas fa-truck-medical"></i>
                </div>

                <div>
                    <h3>Fornecedores</h3>
                    <p>Gerir empresas, contactos e associações aos equipamentos.</p>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar', 'profissional_saude'])): ?>
            <a href="views/localizacoes/lista.php" class="area-card area-card-pink">
                <div class="area-card-icon">
                    <i class="fas fa-location-dot"></i>
                </div>

                <div>
                    <h3>Localizações</h3>
                    <p>Consultar edifícios, pisos, serviços e salas onde existem equipamentos.</p>
                </div>
            </a>
        <?php endif; ?>

        <?php if ($perfil === 'administrador'): ?>
            <a href="views/utilizadores/lista.php" class="area-card area-card-blue">
                <div class="area-card-icon">
                    <i class="fas fa-users"></i>
                </div>

                <div>
                    <h3>Utilizadores</h3>
                    <p>Gerir contas, perfis, acessos e estado dos utilizadores da aplicação.</p>
                </div>
            </a>
        <?php endif; ?>

        <?php if ($perfil === 'administrador'): ?>
            <a href="views/gestao_conteudos/gestao_conteudos.php" class="area-card area-card-gray">
                <div class="area-card-icon">
                    <i class="fas fa-pen-to-square"></i>
                </div>

                <div>
                    <h3>Conteúdos do site</h3>
                    <p>Editar textos, contactos e informação apresentada na área pública.</p>
                </div>
            </a>
        <?php endif; ?>

        <?php if ($perfil === 'administrador'): ?>
            <a href="views/logs/lista.php" class="area-card area-card-yellow">
                <div class="area-card-icon">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>

                <div>
                    <h3>Registo de eventos</h3>
                    <p>Consultar ações importantes registadas automaticamente pelo sistema.</p>
                </div>
            </a>
        <?php endif; ?>

    </section>

    <!-- Dashboard em destaque -->
    <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar', 'profissional_saude'])): ?>
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
    <?php endif; ?>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>