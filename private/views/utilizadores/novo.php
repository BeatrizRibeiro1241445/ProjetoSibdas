<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (($_SESSION['perfil'] ?? '') !== 'administrador') {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$page_title = APP_NAME . ' - Novo Utilizador';
$body_class = 'pagina-novo-equipamento';

$erros = [];
$erroSistema = '';
$sucesso = '';

$username = '';
$email = '';
$nome = '';
$password = '';
$perfil = '';
$dataFimContrato = '';

$perfisPermitidos = [
    'administrador',
    'tecnico',
    'gestor_hospitalar',
    'profissional_saude'
];

function data_valida_novo_utilizador($data)
{
    if ($data === '') {
        return false;
    }

    $objetoData = DateTime::createFromFormat('Y-m-d', $data);

    return $objetoData && $objetoData->format('Y-m-d') === $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $perfil = trim($_POST['perfil'] ?? '');
    $dataFimContrato = trim($_POST['dataFimContrato'] ?? '');

    $username = preg_replace('/\s+/', '', $username);
    $email = preg_replace('/\s+/', '', $email);
    $nome = preg_replace('/\s+/', ' ', $nome);

    $padraoUsername = '/^[A-Za-z0-9._-]+$/';
    $padraoNome = "/^[\p{L}\s.'-]+$/u";

    if ($username === '') {
        $erros[] = 'O username é obrigatório.';
    } elseif (mb_strlen($username) < 3) {
        $erros[] = 'O username deve ter pelo menos 3 caracteres.';
    } elseif (mb_strlen($username) > 80) {
        $erros[] = 'O username não pode ter mais de 80 caracteres.';
    } elseif (!preg_match($padraoUsername, $username)) {
        $erros[] = 'O username só pode conter letras, números, ponto, hífen e underscore.';
    }

    if ($email === '') {
        $erros[] = 'O email é obrigatório.';
    } elseif (mb_strlen($email) > 120) {
        $erros[] = 'O email não pode ter mais de 120 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'O email introduzido não é válido.';
    }

    if ($nome === '') {
        $erros[] = 'O nome é obrigatório.';
    } elseif (mb_strlen($nome) < 3) {
        $erros[] = 'O nome deve ter pelo menos 3 caracteres.';
    } elseif (mb_strlen($nome) > 120) {
        $erros[] = 'O nome não pode ter mais de 120 caracteres.';
    } elseif (!preg_match($padraoNome, $nome)) {
        $erros[] = 'O nome contém caracteres inválidos.';
    }

    if ($password === '') {
        $erros[] = 'A palavra-passe é obrigatória.';
    } elseif (mb_strlen($password) < 6) {
        $erros[] = 'A palavra-passe deve ter pelo menos 6 caracteres.';
    } elseif (mb_strlen($password) > 50) {
        $erros[] = 'A palavra-passe não pode ter mais de 50 caracteres.';
    }

    if ($perfil === '') {
        $erros[] = 'O perfil é obrigatório.';
    } elseif (!in_array($perfil, $perfisPermitidos)) {
        $erros[] = 'O perfil selecionado não é válido.';
    }

    if ($dataFimContrato === '') {
        $erros[] = 'A data de fim do contrato é obrigatória.';
    } elseif (!data_valida_novo_utilizador($dataFimContrato)) {
        $erros[] = 'A data de fim do contrato não é válida.';
    } elseif ($dataFimContrato < date('Y-m-d')) {
        $erros[] = 'A data de fim do contrato não pode ser anterior à data atual.';
    }

    if (empty($erros)) {
        try {
            $ligacao = db_connect();

            $stmtDuplicado = $ligacao->prepare("
                SELECT COUNT(*) AS total
                FROM Utilizador
                WHERE username = :username
                   OR email = :email
            ");

            $stmtDuplicado->bindValue(':username', $username, PDO::PARAM_STR);
            $stmtDuplicado->bindValue(':email', $email, PDO::PARAM_STR);
            $stmtDuplicado->execute();

            $existe = (int) $stmtDuplicado->fetch()->total;

            if ($existe > 0) {
                $erros[] = 'Já existe um utilizador com esse username ou email.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $ligacao->prepare("
                    INSERT INTO Utilizador
                        (username, email, nome, passwordHash, perfil, ativo, lastLogin, dataFimContrato)
                    VALUES
                        (:username, :email, :nome, :passwordHash, :perfil, true, null, :dataFimContrato)
                ");

                $stmt->bindValue(':username', $username, PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
                $stmt->bindValue(':passwordHash', $passwordHash, PDO::PARAM_STR);
                $stmt->bindValue(':perfil', $perfil, PDO::PARAM_STR);
                $stmt->bindValue(':dataFimContrato', $dataFimContrato, PDO::PARAM_STR);
                $stmt->execute();

                if (function_exists('registar_log')) {
                    registar_log('UTILIZADOR_CRIADO', 'Username: ' . $username . ' | Perfil: ' . $perfil);
                }

                $sucesso = 'Utilizador registado com sucesso.';

                $username = '';
                $email = '';
                $nome = '';
                $password = '';
                $perfil = '';
                $dataFimContrato = '';
            }
        } catch (PDOException $e) {
            $erroSistema = 'Erro ao guardar o utilizador.';

            if (function_exists('registar_log')) {
                registar_log('ERRO_BD', 'Erro ao criar utilizador.');
            }
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/nav.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Conteúdo Principal -->
<main class="content">
    <section>

        <div class="actions-top">
            <h2>
                <strong>
                    <i class="fas fa-user-plus"></i> Inserir Novo Utilizador
                </strong>
            </h2>

            <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <hr>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success text-center">
                <?= e($sucesso) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erroSistema)): ?>
            <div class="alert alert-danger text-center">
                <?= e($erroSistema) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <strong>Foram encontrados os seguintes erros:</strong>

                <ul class="mb-0 mt-2">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="novo.php" method="post" class="formulario-equipamento" novalidate>

            <div class="card mb-4">
                <div class="card-body">

                    <h3>
                        <i class="fas fa-user"></i> Dados do utilizador
                    </h3>

                    <div class="row mb-3">

                        <div class="col-12 col-md-4">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Ex.: maria.silva"
                                value="<?= e($username) ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome"
                                placeholder="Ex.: Maria Silva"
                                value="<?= e($nome) ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Ex.: maria.silva@medinventario.pt"
                                value="<?= e($email) ?>">
                        </div>

                    </div>

                    <div class="row mb-3">

                        <div class="col-12 col-md-4">
                            <label for="password" class="form-label">Palavra-passe</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Mínimo 6 caracteres">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="perfil" class="form-label">Perfil</label>
                            <select class="form-select" id="perfil" name="perfil">
                                <option value="">Selecionar perfil</option>
                                <option value="administrador" <?= $perfil === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                <option value="tecnico" <?= $perfil === 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                                <option value="gestor_hospitalar" <?= $perfil === 'gestor_hospitalar' ? 'selected' : '' ?>>Gestor Hospitalar</option>
                                <option value="profissional_saude" <?= $perfil === 'profissional_saude' ? 'selected' : '' ?>>Profissional de Saúde</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="dataFimContrato" class="form-label">Data de fim do contrato</label>
                            <input type="date" class="form-control" id="dataFimContrato" name="dataFimContrato"
                                value="<?= e($dataFimContrato) ?>">
                        </div>

                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    Guardar utilizador
                </button>
            </div>

        </form>

        <p id="mensagem-formulario"></p>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>