<?php
require_once __DIR__ . '/../../config/config.php';

$page_title = $page_title ?? APP_NAME;
$body_class = $body_class ?? '';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?></title>

    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/img/logo.png" type="image/png">

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/1241445.css">
</head>

<body class="<?= e($body_class) ?>">