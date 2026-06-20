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
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

try {
    $ligacao = db_connect();

    $parametros = [
        ':u' => $utilizador,
        ':p' => $password
    ];

    $comando = $ligacao->prepare("
        SELECT *
        FROM Utilizador
        WHERE ativo = 1
          AND (username = :u OR email = :u)
          AND passwordHash = :p
        LIMIT 1
    ");

    $comando->execute($parametros);

    $resultados = $comando->fetchAll(PDO::FETCH_OBJ);

    if (count($resultados) === 0) {
        $_SESSION['server_error'] = 'Login inválido';
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }

    $agente = $resultados[0];

    $stmt = $ligacao->prepare("
        UPDATE Utilizador
        SET lastLogin = NOW()
        WHERE idUtilizador = ?
    ");

    $stmt->execute([$agente->idUtilizador]);

    $_SESSION['idUtilizador'] = $agente->idUtilizador;
    $_SESSION['utilizador'] = $agente->username;
    $_SESSION['nome'] = $agente->nome;
    $_SESSION['perfil'] = $agente->perfil;
    $_SESSION['profile'] = $agente->perfil;

    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}
