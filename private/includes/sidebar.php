<?php
$perfil = $_SESSION['perfil'] ?? '';
?>

<aside class="sidebar">
    <h4>Menu</h4>

    <nav>
        <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar', 'profissional_saude'])): ?>
            <a href="<?= BASE_URL ?>/private/views/equipamentos/lista.php">
                <i class="fas fa-laptop-medical"></i> Equipamentos
            </a>
        <?php endif; ?>

        <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar'])): ?>
            <a href="<?= BASE_URL ?>/private/views/fornecedores/lista.php">
                <i class="fas fa-truck-medical"></i> Fornecedores
            </a>
        <?php endif; ?>

        <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar', 'profissional_saude'])): ?>
            <a href="<?= BASE_URL ?>/private/views/localizacoes/lista.php">
                <i class="fas fa-location-dot"></i> Localizações
            </a>
        <?php endif; ?>

        <?php if ($perfil === 'administrador'): ?>
            <a href="<?= BASE_URL ?>/private/views/utilizadores/lista.php">
                <i class="fas fa-users"></i> Utilizadores
            </a>
        <?php endif; ?>

        <?php if ($perfil === 'administrador'): ?>
            <a href="<?= BASE_URL ?>/private/views/gestao_conteudos/gestao_conteudos.php">
                <i class="fas fa-pen-to-square"></i> Conteúdos do site
            </a>
        <?php endif; ?>

        <?php if ($perfil === 'administrador'): ?>
            <a href="<?= BASE_URL ?>/private/views/logs/lista.php">
                <i class="fas fa-clock-rotate-left"></i> Registo de eventos
            </a>
        <?php endif; ?>

        <?php if (in_array($perfil, ['administrador', 'tecnico', 'gestor_hospitalar', 'profissional_saude'])): ?>
            <a href="<?= BASE_URL ?>/private/views/dashboard/dashboard.php">
                <i class="fas fa-chart-bar"></i> Dashboard
            </a>
        <?php endif; ?>
    </nav>
</aside>