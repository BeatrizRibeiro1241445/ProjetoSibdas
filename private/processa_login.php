<?php

require_once __DIR__ . '/includes/funcoes.php';

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
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// Login temporário da Ficha 10.
// Na Ficha 14 vamos trocar isto por login real com base de dados.
if ($utilizador !== 'admin' || $password !== '123456') {
    $_SESSION['server_error'] = 'Utilizador ou palavra-passe incorretos.';
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$_SESSION['utilizador'] = 'admin';
$_SESSION['nome'] = 'Administrador MedInventário';
$_SESSION['perfil'] = 'administrador';

header('Location: ' . BASE_URL . '/private/area_pessoal.php');
exit;
