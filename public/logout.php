<?php

require_once __DIR__ . '/../private/includes/funcoes.php';

start_session();

registar_log('LOGOUT', 'Utilizador terminou sessão.');

logout_and_redirect('/public/login.php');
