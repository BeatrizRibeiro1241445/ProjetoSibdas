<?php
start_session();

$nomeUtilizador = $_SESSION['nome'] ?? $_SESSION['utilizador'] ?? 'Utilizador';
?>

<header class="bng-navbar-menu">

    <div>
        <a href="<?= BASE_URL ?>/private/area_pessoal.php">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo da MedInventário">
        </a>
        <h3>MedInventário</h3>
    </div>

    <div>
        <a href="<?= BASE_URL ?>/public/logout.php" class="btn btn-outline-secondary">
            <i class="fas fa-user-circle"></i>
            <?= e($nomeUtilizador) ?> | Sair
        </a>
    </div>

</header>

<aside class="sidebar">
    <h4>Menu</h4>

    <nav>
        <a href="<?= BASE_URL ?>/private/views/equipamentos/lista.php">
            <i class="fas fa-laptop-medical"></i> Equipamentos
        </a>

        <a href="<?= BASE_URL ?>/private/views/fornecedores/lista.php">
            <i class="fas fa-truck-medical"></i> Fornecedores
        </a>

        <a href="<?= BASE_URL ?>/private/views/localizacoes/lista.php">
            <i class="fas fa-location-dot"></i> Localizações
        </a>

        <a href="<?= BASE_URL ?>/private/views/gestao_conteudos/gestao_conteudos.php">
            <i class="fas fa-pen-to-square"></i> Conteúdos do site
        </a>

        <a href="<?= BASE_URL ?>/private/views/dashboard/dashboard.php">
            <i class="fas fa-chart-bar"></i> Dashboard
        </a>
    </nav>
</aside>