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

// ============================================================
// Encriptação e desencriptação de valores com OpenSSL
// ============================================================

function aes_encrypt($value)
{
    return bin2hex(openssl_encrypt(
        $value,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    ));
}

function aes_decrypt($value)
{
    if (!is_string($value) || strlen($value) % 2 !== 0) {
        return false;
    }

    return openssl_decrypt(
        hex2bin($value),
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}

// ============================================================
// Registo de eventos do sistema
// ============================================================

function registar_log($tipoEvento, $descricao = '')
{
    try {
        start_session();

        $ligacao = db_connect();

        $idUtilizador = $_SESSION['idUtilizador'] ?? null;
        $username = $_SESSION['utilizador'] ?? null;
        $perfil = $_SESSION['perfil'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $tipoEvento = trim((string) $tipoEvento);
        $descricao = trim((string) $descricao);

        if ($tipoEvento === '') {
            $tipoEvento = 'EVENTO';
        }

        if (mb_strlen($tipoEvento) > 80) {
            $tipoEvento = mb_substr($tipoEvento, 0, 80);
        }

        $stmt = $ligacao->prepare("
            INSERT INTO LogSistema (
                idUtilizador,
                username,
                perfil,
                tipoEvento,
                descricao,
                ip,
                dataHora
            ) VALUES (
                :idUtilizador,
                :username,
                :perfil,
                :tipoEvento,
                :descricao,
                :ip,
                NOW()
            )
        ");

        if ($idUtilizador !== null && is_numeric($idUtilizador)) {
            $stmt->bindValue(':idUtilizador', (int) $idUtilizador, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':idUtilizador', null, PDO::PARAM_NULL);
        }

        $stmt->bindValue(':username', $username, $username !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':perfil', $perfil, $perfil !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':tipoEvento', $tipoEvento, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao !== '' ? $descricao : null, $descricao !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':ip', $ip, $ip !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);

        $stmt->execute();

        return true;
    } catch (Throwable $e) {
        return false;
    }
}
