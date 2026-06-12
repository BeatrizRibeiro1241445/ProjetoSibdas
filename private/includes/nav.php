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