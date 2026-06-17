<?php
$nomeSitePublico = APP_NAME;
$logoSitePublico = 'assets/img/logo.png';

$navInicio = 'Início';
$navQuemSomos = 'Quem Somos';
$navSolucao = 'Solução';
$navFuncionalidades = 'Funcionalidades';

try {
    $ligacao = db_connect();

    $stmt = $ligacao->query("
        SELECT chave, titulo, texto, imagem
        FROM ConteudoSite
        WHERE ativo = true
          AND chave IN (
              'site_nome',
              'nav_inicio',
              'nav_quem_somos',
              'nav_solucao',
              'nav_funcionalidades'
          )
    ");

    foreach ($stmt->fetchAll() as $conteudo) {
        if ($conteudo->chave === 'site_nome') {
            $nomeSitePublico = $conteudo->titulo ?: $nomeSitePublico;

            if (!empty($conteudo->imagem)) {
                $logoSitePublico = $conteudo->imagem;
            }
        }

        if ($conteudo->chave === 'nav_inicio') {
            $navInicio = $conteudo->texto ?: $navInicio;
        }

        if ($conteudo->chave === 'nav_quem_somos') {
            $navQuemSomos = $conteudo->texto ?: $navQuemSomos;
        }

        if ($conteudo->chave === 'nav_solucao') {
            $navSolucao = $conteudo->texto ?: $navSolucao;
        }

        if ($conteudo->chave === 'nav_funcionalidades') {
            $navFuncionalidades = $conteudo->texto ?: $navFuncionalidades;
        }
    }
} catch (PDOException $e) {
    $nomeSitePublico = APP_NAME;
}


?>

<nav class="bng-navbar">

    <div>
        <img src="<?= BASE_URL ?>/<?= e($logoSitePublico) ?>" alt="Logo da <?= e($nomeSitePublico) ?>">
        <h3><?= e($nomeSitePublico) ?></h3>
    </div>

    <div class="container-navegacao">
        <a href="<?= BASE_URL ?>/public/index.php#inicio"><?= e($navInicio) ?></a>
        <a href="<?= BASE_URL ?>/public/index.php#quem-somos"><?= e($navQuemSomos) ?></a>
        <a href="<?= BASE_URL ?>/public/index.php#solucao"><?= e($navSolucao) ?></a>
        <a href="<?= BASE_URL ?>/public/index.php#funcionalidades"><?= e($navFuncionalidades) ?></a>
    </div>

    <div class="nav-cliente">
        <a href="<?= BASE_URL ?>/public/login.php">
            <i class="fas fa-user-circle"></i>
            Iniciar Sessão
        </a>
    </div>

</nav>