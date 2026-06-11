<?php

// =====================================================
// Configuração geral da aplicação
// =====================================================

define('APP_NAME', 'MedInventário');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', '1241445');

// IMPORTANTE:
// troca "medinventario" pelo nome real da pasta do teu projeto dentro de C:\laragon\www
define('BASE_URL', '/MedInventario');

// =====================================================
// Configuração da base de dados
// =====================================================

// Como estás a usar a BD do servidor SIBDAS:
define('MYSQL_HOST', 'vsgate-s1.dei.isep.ipp.pt');
define('MYSQL_PORT', '10464');
define('MYSQL_DATABASE', 'db1241445');
define('MYSQL_USERNAME', '1241445');

// Coloca aqui a password indicada na aula/HeidiSQL.
// Não me envies a password no chat.
define('MYSQL_PASSWORD', 'COLOCA_AQUI_A_TUA_PASSWORD');

// Chave usada para encriptar IDs em links GET, como nas fichas 13/14.
define('MYSQL_AES_KEY', 'medinventario_1241445_chave_segura');