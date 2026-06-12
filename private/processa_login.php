<?php

require_once __DIR__ . '/includes/funcoes.php';

start_session();

// ------------------------------------------------------------
// SEGURANÇA: este ficheiro só pode ser chamado por POST
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// ------------------------------------------------------------
// 1. Recolher dados enviados pelo formulário
// ------------------------------------------------------------
$utilizador = trim($_POST['utilizador'] ?? '');
$password = $_POST['password'] ?? '';

$validation_errors = [];

// ------------------------------------------------------------
// 2. Validar dados
// ------------------------------------------------------------
if ($utilizador === '') {
    $validation_errors[] = 'Preencha o campo utilizador.';
}

if ($password === '') {
    $validation_errors[] = 'Preencha o campo palavra-passe.';
}

if ($utilizador !== '' && (strlen($utilizador) < 3 || strlen($utilizador) > 80)) {
    $validation_errors[] = 'O utilizador deve ter entre 3 e 80 caracteres.';
}

if ($password !== '' && (strlen($password) < 6 || strlen($password) > 12)) {
    $validation_errors[] = 'A palavra-passe deve ter entre 6 e 12 caracteres.';
}

// ------------------------------------------------------------
// 3. Se houver erros, guardar na sessão e voltar ao login
// ------------------------------------------------------------
if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// ------------------------------------------------------------
// 4. Simulação temporária do login
// Esta parte será substituída por login real com BD na Ficha 14
// ------------------------------------------------------------
$utilizadorValido = 'admin';
$passwordValida = '123456';

if ($utilizador !== $utilizadorValido || $password !== $passwordValida) {
    $_SESSION['server_error'] = 'Utilizador ou palavra-passe errados.';
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// ------------------------------------------------------------
// 5. Criar sessão do utilizador autenticado
// ------------------------------------------------------------
$_SESSION['utilizador'] = $utilizador;
$_SESSION['nome'] = 'Administrador MedInventário';
$_SESSION['perfil'] = 'administrador';

header('Location: ' . BASE_URL . '/private/area_pessoal.php');
exit;
