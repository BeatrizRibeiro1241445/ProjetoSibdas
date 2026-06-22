<?php
require_once __DIR__ . '/includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Alterar Palavra-passe';
$body_class = 'login-page';

$erro = '';
$sucesso = '';
$erros = [];

$passwordAtual = '';
$novaPassword = '';
$confirmarPassword = '';

$idUtilizador = $_SESSION['idUtilizador'] ?? null;

if (!$idUtilizador || !is_numeric($idUtilizador)) {
    header('Location: ' . BASE_URL . '/public/logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $passwordAtual = trim($_POST['password_atual'] ?? '');
    $novaPassword = trim($_POST['nova_password'] ?? '');
    $confirmarPassword = trim($_POST['confirmar_password'] ?? '');

    if ($passwordAtual === '') {
        $erros[] = 'A palavra-passe atual é obrigatória.';
    }

    if ($novaPassword === '') {
        $erros[] = 'A nova palavra-passe é obrigatória.';
    } elseif (mb_strlen($novaPassword) < 6) {
        $erros[] = 'A nova palavra-passe deve ter pelo menos 6 caracteres.';
    } elseif (mb_strlen($novaPassword) > 50) {
        $erros[] = 'A nova palavra-passe não pode ter mais de 50 caracteres.';
    }

    if ($confirmarPassword === '') {
        $erros[] = 'A confirmação da nova palavra-passe é obrigatória.';
    }

    if ($novaPassword !== '' && $confirmarPassword !== '' && $novaPassword !== $confirmarPassword) {
        $erros[] = 'A nova palavra-passe e a confirmação não coincidem.';
    }

    if ($passwordAtual !== '' && $novaPassword !== '' && $passwordAtual === $novaPassword) {
        $erros[] = 'A nova palavra-passe deve ser diferente da palavra-passe atual.';
    }

    if (empty($erros)) {
        try {
            $ligacao = db_connect();

            $stmt = $ligacao->prepare("
                SELECT
                    idUtilizador,
                    username,
                    nome,
                    perfil,
                    passwordHash
                FROM Utilizador
                WHERE idUtilizador = :idUtilizador
                  AND ativo = true
                LIMIT 1
            ");

            $stmt->bindValue(':idUtilizador', $idUtilizador, PDO::PARAM_INT);
            $stmt->execute();

            $utilizador = $stmt->fetch();

            if (!$utilizador) {
                header('Location: ' . BASE_URL . '/public/logout.php');
                exit;
            }

            if (!password_verify($passwordAtual, $utilizador->passwordHash)) {
                $erros[] = 'A palavra-passe atual não está correta.';

                if (function_exists('registar_log')) {
                    registar_log('ALTERAR_PASSWORD_FALHOU', 'Palavra-passe atual incorreta.');
                }
            } else {
                $novaPasswordHash = password_hash($novaPassword, PASSWORD_DEFAULT);

                $stmtUpdate = $ligacao->prepare("
                    UPDATE Utilizador
                    SET passwordHash = :novaPassword
                    WHERE idUtilizador = :idUtilizador
                ");

                $stmtUpdate->bindValue(':novaPassword', $novaPasswordHash, PDO::PARAM_STR);
                $stmtUpdate->bindValue(':idUtilizador', $idUtilizador, PDO::PARAM_INT);
                $stmtUpdate->execute();

                if (function_exists('registar_log')) {
                    registar_log('PASSWORD_ALTERADA', 'Utilizador alterou a própria palavra-passe.');
                }

                $sucesso = 'Palavra-passe alterada com sucesso.';

                $passwordAtual = '';
                $novaPassword = '';
                $confirmarPassword = '';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao alterar a palavra-passe.';

            if (function_exists('registar_log')) {
                registar_log('ERRO_BD', 'Erro ao alterar palavra-passe.');
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="container">
    <section class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5">

            <div class="card login-box">

                <a href="<?= BASE_URL ?>/private/area_pessoal.php" class="btn btn-outline-secondary login-voltar" title="Voltar à área pessoal">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <div class="card-body">

                    <!-- Cabeçalho -->
                    <div class="login-brand">
                        <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo MedInventário" class="login-logo">

                        <div>
                            <h2>Alterar palavra-passe</h2>
                            <p><?= e($_SESSION['nome'] ?? $_SESSION['utilizador'] ?? 'Utilizador') ?></p>
                        </div>
                    </div>

                    <?php if (!empty($erro)): ?>
                        <div class="alert alert-danger text-center">
                            <?= e($erro) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($sucesso)): ?>
                        <div class="alert alert-success text-center">
                            <?= e($sucesso) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger">
                            <strong>Foram encontrados os seguintes erros:</strong>

                            <ul class="mb-0 mt-2">
                                <?php foreach ($erros as $erroValidacao): ?>
                                    <li><?= e($erroValidacao) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Formulário -->
                    <form action="alterar_password.php" method="post" novalidate>

                        <div class="mb-3">
                            <label for="password_atual" class="form-label">
                                <i class="fas fa-lock"></i> Palavra-passe atual
                            </label>

                            <input type="password" class="form-control" id="password_atual" name="password_atual"
                                placeholder="Introduza a palavra-passe atual"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="nova_password" class="form-label">
                                <i class="fas fa-key"></i> Nova palavra-passe
                            </label>

                            <input type="password" class="form-control" id="nova_password" name="nova_password"
                                placeholder="Introduza a nova palavra-passe"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_password" class="form-label">
                                <i class="fas fa-check"></i> Confirmar nova palavra-passe
                            </label>

                            <input type="password" class="form-control" id="confirmar_password" name="confirmar_password"
                                placeholder="Confirme a nova palavra-passe"
                                required>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary login-button">
                                Guardar <i class="fas fa-floppy-disk"></i>
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>