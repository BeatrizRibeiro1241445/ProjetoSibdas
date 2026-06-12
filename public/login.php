<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

start_session();

$validation_errors = $_SESSION['validation_errors'] ?? [];
$server_error = $_SESSION['server_error'] ?? '';

unset($_SESSION['validation_errors']);
unset($_SESSION['server_error']);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedInventário - Login</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="../assets/img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">

    <!-- folha de estilos CSS -->
    <link rel="stylesheet" href="../assets/css/1241445.css">
</head>

<body class="login-page">

    <main class="container">
        <section class="row justify-content-center">
            <div class="col-12 col-md-7 col-lg-5">

                <div class="card login-box">

                    <a href="index.php" class="btn btn-outline-secondary login-voltar" title="Voltar ao site">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <div class="card-body">

                        <!-- Cabeçalho do login -->
                        <div class="login-brand">
                            <img src="../assets/img/logo.png" alt="Logo MedInventário" class="login-logo">

                            <div>
                                <h2>MedInventário</h2>
                                <p>Acesso à área reservada</p>
                            </div>
                        </div>

                        <?php if (!empty($validation_errors)): ?>
                            <div class="alert alert-danger text-center">
                                <?php foreach ($validation_errors as $erro): ?>
                                    <div><?= e($erro) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($server_error)): ?>
                            <div class="alert alert-danger text-center">
                                <?= e($server_error) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulário de login -->
                        <form action="../private/processa_login.php" method="post" novalidate>

                            <div class="mb-3">
                                <label for="utilizador" class="form-label">
                                    <i class="fas fa-user"></i> Utilizador
                                </label>

                                <input type="text" class="form-control" id="utilizador" name="utilizador"
                                    placeholder="Introduza o utilizador" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Palavra-passe
                                </label>

                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Introduza a palavra-passe" required>
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

    <!-- Bootstrap JS -->
    <script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- JavaScript -->
    <script src="../assets/js/1241445.js"></script>

</body>

</html>