<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$utilizador = trim($_POST['utilizador'] ?? '');
$password = $_POST['password'] ?? '';

if ($utilizador === '' || $password === '') {
    $_SESSION['server_error'] = 'Preencha o utilizador e a palavra-passe.';

    if (function_exists('registar_log')) {
        registar_log('LOGIN_FALHADO', 'Campos de login vazios.');
    }

    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

try {
    $ligacao = db_connect();

    $comando = $ligacao->prepare("
        SELECT *
        FROM Utilizador
        WHERE ativo = 1
          AND (username = :utilizador OR email = :utilizador)
        LIMIT 1
    ");

    $comando->bindValue(':utilizador', $utilizador, PDO::PARAM_STR);
    $comando->execute();

    $agente = $comando->fetch();

    if (!$agente || !password_verify($password, $agente->passwordHash)) {
        $_SESSION['server_error'] = 'Login inválido';

        if (function_exists('registar_log')) {
            registar_log('LOGIN_FALHADO', 'Tentativa com utilizador/email: ' . $utilizador);
        }

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

    if (function_exists('registar_log')) {
        registar_log('LOGIN_SUCESSO', 'Utilizador: ' . $agente->username . ' | Perfil: ' . $agente->perfil);
    }

    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';

    if (function_exists('registar_log')) {
        registar_log('ERRO_BD', 'Erro no processamento do login.');
    }

    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}
