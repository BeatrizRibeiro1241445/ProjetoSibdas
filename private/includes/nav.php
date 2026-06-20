<?php
start_session();

$nomeUtilizador = $_SESSION['nome'] ?? $_SESSION['utilizador'] ?? 'Utilizador';
$perfilUtilizador = $_SESSION['perfil'] ?? 'utilizador';

switch ($perfilUtilizador) {
    case 'administrador':
        $perfilTexto = 'Administrador';
        break;

    case 'tecnico':
        $perfilTexto = 'Técnico';
        break;

    case 'gestor_hospitalar':
        $perfilTexto = 'Gestor Hospitalar';
        break;

    case 'profissional_saude':
        $perfilTexto = 'Profissional de Saúde';
        break;

    default:
        $perfilTexto = 'Utilizador';
        break;
}
?>

<header class="bng-navbar-menu">

    <div class="navbar-logo-area">
        <a href="<?= BASE_URL ?>/private/area_pessoal.php">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo da MedInventário">
        </a>
        <h3>MedInventário</h3>
    </div>

    <div class="utilizador-menu-container">

        <button class="utilizador-menu-botao" type="button">
            <i class="fas fa-user-circle"></i>

            <span class="utilizador-menu-botao-texto">
                <strong><?= e($nomeUtilizador) ?></strong>
            </span>

            <i class="fas fa-chevron-down"></i>
        </button>

        <div class="utilizador-menu-dropdown">

            <div class="utilizador-menu-info">
                <i class="fas fa-user-circle"></i>

                <div class="utilizador-menu-texto">
                    <strong><?= e($nomeUtilizador) ?></strong>
                    <span><?= e($perfilTexto) ?></span>
                </div>
            </div>

            <a href="<?= BASE_URL ?>/public/logout.php" class="utilizador-menu-sair">
                <i class="fas fa-right-from-bracket"></i>
                Sair
            </a>

        </div>

    </div>

</header>