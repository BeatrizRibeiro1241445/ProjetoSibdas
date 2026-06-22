<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$utilizador = trim($_POST['utilizador'] ?? '');
$password = $_POST['password'] ?? '';

$validation_errors = [];

if ($utilizador === '') {
    $validation_errors[] = 'Preencha o campo utilizador.';
}

if ($password === '') {
    $validation_errors[] = 'Preencha o campo palavra-passe.';
}

if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;

    registar_log('LOGIN_FALHADO', 'Tentativa de login com campos vazios.');

    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

try {
    $ligacao = db_connect();

    $comando = $ligacao->prepare("
        SELECT
            idUtilizador,
            username,
            email,
            nome,
            passwordHash,
            perfil,
            ativo
        FROM Utilizador
        WHERE ativo = true
          AND (username = :utilizador OR email = :utilizador)
        LIMIT 1
    ");

    $comando->bindValue(':utilizador', $utilizador, PDO::PARAM_STR);
    $comando->execute();

    $agente = $comando->fetch();

    if (!$agente || !password_verify($password, $agente->passwordHash)) {
        $_SESSION['server_error'] = 'Utilizador ou palavra-passe incorretos.';

        registar_log('LOGIN_FALHADO', 'Tentativa com utilizador/email: ' . $utilizador);

        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }

    $stmt = $ligacao->prepare("
        UPDATE Utilizador
        SET lastLogin = NOW()
        WHERE idUtilizador = :idUtilizador
    ");

    $stmt->bindValue(':idUtilizador', $agente->idUtilizador, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['idUtilizador'] = $agente->idUtilizador;
    $_SESSION['utilizador'] = $agente->username;
    $_SESSION['nome'] = $agente->nome;
    $_SESSION['perfil'] = $agente->perfil;
    $_SESSION['profile'] = $agente->perfil;

    registar_log('LOGIN_SUCESSO', 'Utilizador: ' . $agente->username . ' | Perfil: ' . $agente->perfil);

    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';

    registar_log('ERRO_BD', 'Erro no processamento do login.');

    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}
