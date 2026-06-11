<?php

require_once __DIR__ . '/../../config/config.php';

// =====================================================
// Sessões
// =====================================================

function start_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function check_session()
{
    start_session();
    return isset($_SESSION['utilizador']);
}

function redirect_if_not_logged($redirect_to = '/public/login.php')
{
    start_session();

    if (!check_session()) {
        header('Location: ' . BASE_URL . $redirect_to);
        exit;
    }
}

function logout_and_redirect($redirect_to = '/public/login.php')
{
    start_session();
    session_unset();
    session_destroy();

    header('Location: ' . BASE_URL . $redirect_to);
    exit;
}

// =====================================================
// Segurança / apresentação
// =====================================================

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// =====================================================
// Ligação à base de dados com PDO
// =====================================================

function db_connect()
{
    static $ligacao = null;

    if ($ligacao === null) {
        $dsn = 'mysql:host=' . MYSQL_HOST .
               ';port=' . MYSQL_PORT .
               ';dbname=' . MYSQL_DATABASE .
               ';charset=utf8mb4';

        $ligacao = new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD);
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ligacao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    return $ligacao;
}

// =====================================================
// Encriptação simples de IDs para GET
// Usado mais tarde em detalhes.php, editar.php e apagar.php
// =====================================================

function aes_encrypt($valor)
{
    $key = hash('sha256', MYSQL_AES_KEY, true);
    $iv = random_bytes(16);

    $encrypted = openssl_encrypt(
        (string) $valor,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    return rtrim(strtr(base64_encode($iv . $encrypted), '+/', '-_'), '=');
}

function aes_decrypt($valor)
{
    if (empty($valor)) {
        return null;
    }

    $data = base64_decode(strtr($valor, '-_', '+/'));

    if ($data === false || strlen($data) <= 16) {
        return null;
    }

    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    $key = hash('sha256', MYSQL_AES_KEY, true);

    $decrypted = openssl_decrypt(
        $encrypted,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    return $decrypted === false ? null : $decrypted;
}