<?php
require_once __DIR__ . '/../private/includes/funcoes.php';

$page_title = APP_NAME;
$body_class = '';

$conteudos = [];

try {
    $ligacao = db_connect();

    $stmt = $ligacao->query("
        SELECT chave, seccao, titulo, texto, imagem
        FROM ConteudoSite
        WHERE ativo = true
    ");

    foreach ($stmt->fetchAll() as $conteudo) {
        $conteudos[$conteudo->chave] = $conteudo;
    }
} catch (PDOException $e) {
    $conteudos = [];
}

function public_conteudo_titulo($conteudos, $chave, $fallback = '')
{
    return $conteudos[$chave]->titulo ?? $fallback;
}

function public_conteudo_texto($conteudos, $chave, $fallback = '')
{
    return $conteudos[$chave]->texto ?? $fallback;
}

function public_conteudo_imagem($conteudos, $chave, $fallback = '')
{
    return $conteudos[$chave]->imagem ?? $fallback;
}

function public_paragrafos($texto)
{
    $partes = preg_split('/\R{2,}/', trim($texto));

    if (!$partes) {
        return [];
    }

    return $partes;
}

include __DIR__ . '/../private/includes/header.php';
include __DIR__ . '/../private/includes/public_nav.php';
?>

<!-- Secção Início -->
<section class="container-texto-generico public-hero" id="inicio">

    <div class="texto-seccao">
        <h1><?= e(public_conteudo_titulo($conteudos, 'inicio', 'Sistema Web de Apoio ao Inventário Hospitalar')) ?></h1>

        <?php foreach (public_paragrafos(public_conteudo_texto($conteudos, 'inicio', 'A MedInventário ajuda instituições de saúde a organizar, consultar e controlar equipamentos médicos de forma simples, centralizada e segura.')) as $paragrafo): ?>
            <p><?= e($paragrafo) ?></p>
        <?php endforeach; ?>

        <a class="botao-principal" href="#solucao">
            Conhecer a solução
        </a>
    </div>

    <div class="imagem-seccao imagem-hero-public">
        <img src="<?= BASE_URL ?>/<?= e(public_conteudo_imagem($conteudos, 'inicio', 'assets/img/hospital-digital.png')) ?>" alt="Hospital digital ligado por rede">
    </div>

</section>

<!-- Secção Quem Somos -->
<section class="container-texto-generico public-section-light" id="quem-somos">

    <div class="imagem-seccao">
        <img src="<?= BASE_URL ?>/<?= e(public_conteudo_imagem($conteudos, 'quem_somos', 'assets/img/equipa-biomedica.png')) ?>" alt="Profissional a usar sistema hospitalar">
    </div>

    <div class="texto-seccao">
        <h2><?= e(public_conteudo_titulo($conteudos, 'quem_somos', 'Quem Somos')) ?></h2>

        <?php foreach (public_paragrafos(public_conteudo_texto($conteudos, 'quem_somos', 'A MedInventário é uma solução digital pensada para apoiar hospitais e serviços de saúde na gestão organizada do seu parque tecnológico.\n\nA plataforma centraliza informação essencial sobre equipamentos, fornecedores, localizações, documentação, garantias e contratos, facilitando o acesso rápido aos dados.')) as $paragrafo): ?>
            <p><?= e($paragrafo) ?></p>
        <?php endforeach; ?>

        <a class="botao-principal" href="#funcionalidades">
            Ver funcionalidades
        </a>
    </div>

</section>

<!-- Secção Solução -->
<section class="container-texto-generico" id="solucao">

    <div class="texto-seccao">
        <h2><?= e(public_conteudo_titulo($conteudos, 'solucao', 'A Nossa Solução')) ?></h2>

        <?php foreach (public_paragrafos(public_conteudo_texto($conteudos, 'solucao', 'O objetivo da MedInventário é disponibilizar uma plataforma organizada para apoiar o ciclo de vida dos equipamentos médicos, desde o registo inicial até à sua consulta, atualização ou desativação.\n\nA aplicação permite melhorar a rastreabilidade, facilitar a pesquisa de informação e apoiar decisões relacionadas com manutenção, garantias e documentação.')) as $paragrafo): ?>
            <p><?= e($paragrafo) ?></p>
        <?php endforeach; ?>
    </div>

    <div class="imagem-seccao">
        <img src="<?= BASE_URL ?>/<?= e(public_conteudo_imagem($conteudos, 'solucao', 'assets/img/solução.png')) ?>" alt="Plataforma de gestão de equipamentos médicos">
    </div>

</section>

<!-- Secção Funcionalidades -->
<section class="container-texto-generico public-section-light" id="funcionalidades">

    <div>
        <h2><?= e(public_conteudo_titulo($conteudos, 'funcionalidades_intro', 'Funcionalidades')) ?></h2>

        <?php foreach (public_paragrafos(public_conteudo_texto($conteudos, 'funcionalidades_intro', 'A MedInventário organiza os principais módulos necessários para uma gestão clara, simples e centralizada do inventário hospitalar.')) as $paragrafo): ?>
            <p><?= e($paragrafo) ?></p>
        <?php endforeach; ?>

        <div class="public-cards">

            <?php for ($i = 1; $i <= 8; $i++): ?>
                <?php
                $chave = 'funcionalidade_' . $i;

                $iconesFallback = [
                    1 => 'fas fa-laptop-medical',
                    2 => 'fas fa-truck-medical',
                    3 => 'fas fa-location-dot',
                    4 => 'fas fa-file-pdf',
                    5 => 'fas fa-file-contract',
                    6 => 'fas fa-chart-simple',
                    7 => 'fas fa-file-export',
                    8 => 'fas fa-clock-rotate-left'
                ];

                $titulosFallback = [
                    1 => 'Equipamentos',
                    2 => 'Fornecedores',
                    3 => 'Localizações',
                    4 => 'Documentação PDF',
                    5 => 'Garantias e contratos',
                    6 => 'Dashboard',
                    7 => 'Exportação de equipamentos',
                    8 => 'Registo de eventos'
                ];

                $textosFallback = [
                    1 => 'Registo, consulta, edição, remoção e restauro de equipamentos médicos.',
                    2 => 'Gestão de fornecedores, contactos e relações com os equipamentos.',
                    3 => 'Associação dos equipamentos a edifícios, pisos, serviços e salas.',
                    4 => 'Upload e consulta de manuais, certificados e documentos técnicos em PDF.',
                    5 => 'Acompanhamento de garantias, contratos, datas de fim e entidades responsáveis.',
                    6 => 'Indicadores, alertas e gráficos estatísticos sobre o inventário.',
                    7 => 'Exportação da lista de equipamentos para os formatos CSV, JSON e PDF, para consulta e análise externa.',
                    8 => 'Registo de ações importantes do sistema para consulta pelo administrador.'
                ];
                ?>

                <div class="public-card">
                    <i class="<?= e(public_conteudo_imagem($conteudos, $chave, $iconesFallback[$i])) ?>"></i>
                    <h3><?= e(public_conteudo_titulo($conteudos, $chave, $titulosFallback[$i])) ?></h3>
                    <p><?= e(public_conteudo_texto($conteudos, $chave, $textosFallback[$i])) ?></p>
                </div>
            <?php endfor; ?>

        </div>
    </div>

</section>

<!-- Secção Dashboard -->
<section class="container-texto-generico">

    <div class="texto-seccao">
        <h2><?= e(public_conteudo_titulo($conteudos, 'dashboard_publico', 'Informação centralizada para melhor decisão')) ?></h2>

        <?php foreach (public_paragrafos(public_conteudo_texto($conteudos, 'dashboard_publico', 'Através de indicadores e alertas, a solução permite identificar equipamentos críticos, garantias próximas do fim, documentação em falta e estados de funcionamento.\n\nEsta informação ajuda os serviços técnicos e administrativos a acompanhar o inventário de forma mais rápida e estruturada.')) as $paragrafo): ?>
            <p><?= e($paragrafo) ?></p>
        <?php endforeach; ?>
    </div>

    <div class="imagem-seccao">
        <img src="<?= BASE_URL ?>/<?= e(public_conteudo_imagem($conteudos, 'dashboard_publico', 'assets/img/dashboard.png')) ?>" alt="Dashboard com alertas de inventário hospitalar">
    </div>

</section>

<?php
include __DIR__ . '/../private/includes/public_footer.php';
include __DIR__ . '/../private/includes/footer.php';
?>
