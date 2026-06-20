<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

start_session();

if (check_session()) {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$page_title = APP_NAME . ' - Login';
$body_class = 'login-page';

$erro = '';
$utilizadorPreenchido = '';

if (!empty($_SESSION['server_error'])) {
    $erro = $_SESSION['server_error'];
    unset($_SESSION['server_error']);
}

include __DIR__ . '/../private/includes/header.php';
?>

<main class="container">
    <section class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5">

            <div class="card login-box">

                <a href="<?= BASE_URL ?>/public/index.php" class="btn btn-outline-secondary login-voltar" title="Voltar ao site">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <div class="card-body">

                    <!-- Cabeçalho do login -->
                    <div class="login-brand">
                        <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo MedInventário" class="login-logo">

                        <div>
                            <h2>MedInventário</h2>
                            <p>Acesso à área reservada</p>
                        </div>
                    </div>

                    <?php if (!empty($erro)): ?>
                        <div class="alert alert-danger text-center">
                            <?= e($erro) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulário de login -->
                    <form action="processa_login.php" method="post" novalidate>

                        <div class="mb-3">
                            <label for="utilizador" class="form-label">
                                <i class="fas fa-user"></i> Utilizador
                            </label>

                            <input type="text" class="form-control" id="utilizador" name="utilizador"
                                placeholder="Introduza o utilizador"
                                value="<?= e($utilizadorPreenchido) ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Palavra-passe
                            </label>

                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Introduza a palavra-passe"
                                required>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary login-button">
                                Entrar <i class="fas fa-sign-in-alt"></i>
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </section>
</main>

<?php include __DIR__ . '/../private/includes/footer.php'; ?>