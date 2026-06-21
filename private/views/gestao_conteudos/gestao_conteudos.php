<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (($_SESSION['perfil'] ?? '') !== 'administrador') {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$page_title = APP_NAME . ' - Gestão de Conteúdos';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$sucesso = '';
$erros = [];
$conteudos = [];

function conteudo_titulo($conteudos, $chave, $fallback = '')
{
    return $conteudos[$chave]->titulo ?? $fallback;
}

function conteudo_texto($conteudos, $chave, $fallback = '')
{
    return $conteudos[$chave]->texto ?? $fallback;
}

function conteudo_linha($conteudos, $chave, $posicao, $fallback = '')
{
    $texto = $conteudos[$chave]->texto ?? '';
    $partes = explode('|', $texto);

    return $partes[$posicao] ?? $fallback;
}

function atualizar_conteudo_site($ligacao, $chave, $seccao, $titulo, $texto)
{
    $stmt = $ligacao->prepare("
        UPDATE ConteudoSite
        SET
            seccao = :seccao,
            titulo = :titulo,
            texto = :texto,
            ativo = true
        WHERE chave = :chave
    ");

    $stmt->bindValue(':chave', $chave, PDO::PARAM_STR);
    $stmt->bindValue(':seccao', $seccao, PDO::PARAM_STR);
    $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
    $stmt->bindValue(':texto', $texto, PDO::PARAM_STR);

    $stmt->execute();
}

try {
    $ligacao = db_connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nomeSite = trim($_POST['nome_site'] ?? '');
        $navInicio = trim($_POST['nav_inicio'] ?? '');
        $navSeccao1 = trim($_POST['nav_seccao1'] ?? '');
        $navSeccao2 = trim($_POST['nav_seccao2'] ?? '');
        $navSeccao3 = trim($_POST['nav_seccao3'] ?? '');

        $tituloInicio = trim($_POST['titulo_inicio'] ?? '');
        $textoInicio = trim($_POST['texto_inicio'] ?? '');

        $tituloSeccao1 = trim($_POST['titulo_seccao1'] ?? '');
        $textoSeccao1 = trim($_POST['texto_seccao1'] ?? '');

        $tituloSeccao2 = trim($_POST['titulo_seccao2'] ?? '');
        $textoSeccao2 = trim($_POST['texto_seccao2'] ?? '');

        $tituloSeccao4 = trim($_POST['titulo_seccao4'] ?? '');
        $textoSeccao4 = trim($_POST['texto_seccao4'] ?? '');

        $cidadePaisFooter = trim($_POST['cidade_pais_footer'] ?? '');
        $codigoPostalFooter = trim($_POST['codigo_postal_footer'] ?? '');
        $moradaFooter = trim($_POST['morada_footer'] ?? '');

        $horarioSemanaFooter = trim($_POST['horario_semana_footer'] ?? '');
        $horarioSabadoFooter = trim($_POST['horario_sabado_footer'] ?? '');
        $horarioDomingoFooter = trim($_POST['horario_domingo_footer'] ?? '');

        $emailFooter = trim($_POST['email_footer'] ?? '');
        $telefone1Footer = trim($_POST['telefone_1_footer'] ?? '');
        $telefone2Footer = trim($_POST['telefone_2_footer'] ?? '');

        $nomeSite = preg_replace('/\s+/', ' ', $nomeSite);
        $navInicio = preg_replace('/\s+/', ' ', $navInicio);
        $navSeccao1 = preg_replace('/\s+/', ' ', $navSeccao1);
        $navSeccao2 = preg_replace('/\s+/', ' ', $navSeccao2);
        $navSeccao3 = preg_replace('/\s+/', ' ', $navSeccao3);

        $tituloInicio = preg_replace('/\s+/', ' ', $tituloInicio);
        $tituloSeccao1 = preg_replace('/\s+/', ' ', $tituloSeccao1);
        $tituloSeccao2 = preg_replace('/\s+/', ' ', $tituloSeccao2);
        $tituloSeccao4 = preg_replace('/\s+/', ' ', $tituloSeccao4);

        $cidadePaisFooter = preg_replace('/\s+/', ' ', $cidadePaisFooter);
        $codigoPostalFooter = preg_replace('/\s+/', ' ', $codigoPostalFooter);
        $moradaFooter = preg_replace('/\s+/', ' ', $moradaFooter);
        $horarioSemanaFooter = preg_replace('/\s+/', ' ', $horarioSemanaFooter);
        $horarioSabadoFooter = preg_replace('/\s+/', ' ', $horarioSabadoFooter);
        $horarioDomingoFooter = preg_replace('/\s+/', ' ', $horarioDomingoFooter);
        $emailFooter = preg_replace('/\s+/', '', $emailFooter);
        $telefone1Footer = preg_replace('/\s+/', '', $telefone1Footer);
        $telefone2Footer = preg_replace('/\s+/', '', $telefone2Footer);

        $camposObrigatorios = [
            'nome do site' => $nomeSite,
            'link inicial' => $navInicio,
            'link da secção 1' => $navSeccao1,
            'link da secção 2' => $navSeccao2,
            'link da secção 3' => $navSeccao3,
            'título da secção início' => $tituloInicio,
            'texto da secção início' => $textoInicio,
            'título da secção 1' => $tituloSeccao1,
            'texto da secção 1' => $textoSeccao1,
            'título da secção 2' => $tituloSeccao2,
            'texto da secção 2' => $textoSeccao2,
            'título da secção 4' => $tituloSeccao4,
            'texto da secção 4' => $textoSeccao4,
            'cidade e país do rodapé' => $cidadePaisFooter,
            'código postal do rodapé' => $codigoPostalFooter,
            'morada do rodapé' => $moradaFooter,
            'horário semanal' => $horarioSemanaFooter,
            'horário de sábado' => $horarioSabadoFooter,
            'horário de domingo' => $horarioDomingoFooter,
            'email do rodapé' => $emailFooter,
            'telefone 1 do rodapé' => $telefone1Footer,
            'telefone 2 do rodapé' => $telefone2Footer
        ];

        foreach ($camposObrigatorios as $nomeCampo => $valorCampo) {
            if ($valorCampo === '') {
                $erros[] = 'O campo ' . $nomeCampo . ' é obrigatório.';
            }
        }

        $titulos = [
            $nomeSite,
            $tituloInicio,
            $tituloSeccao1,
            $tituloSeccao2,
            $tituloSeccao4
        ];

        foreach ($titulos as $titulo) {
            if (mb_strlen($titulo) > 150) {
                $erros[] = 'Os títulos não podem ter mais de 150 caracteres.';
                break;
            }
        }

        if (!filter_var($emailFooter, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'O email do rodapé deve estar num formato válido.';
        }

        if (!preg_match('/^\+[0-9]{8,15}$/', $telefone1Footer)) {
            $erros[] = 'O telefone 1 do rodapé deve começar por +, incluir o indicativo do país e conter apenas números.';
        }

        if (!preg_match('/^\+[0-9]{8,15}$/', $telefone2Footer)) {
            $erros[] = 'O telefone 2 do rodapé deve começar por +, incluir o indicativo do país e conter apenas números.';
        }

        if (empty($erros)) {
            atualizar_conteudo_site($ligacao, 'site_nome', 'navbar', $nomeSite, $nomeSite);
            atualizar_conteudo_site($ligacao, 'nav_inicio', 'navbar', $navInicio, $navInicio);
            atualizar_conteudo_site($ligacao, 'nav_quem_somos', 'navbar', $navSeccao1, $navSeccao1);
            atualizar_conteudo_site($ligacao, 'nav_solucao', 'navbar', $navSeccao2, $navSeccao2);
            atualizar_conteudo_site($ligacao, 'nav_funcionalidades', 'navbar', $navSeccao3, $navSeccao3);

            atualizar_conteudo_site($ligacao, 'inicio', 'inicio', $tituloInicio, $textoInicio);
            atualizar_conteudo_site($ligacao, 'quem_somos', 'quem_somos', $tituloSeccao1, $textoSeccao1);
            atualizar_conteudo_site($ligacao, 'solucao', 'solucao', $tituloSeccao2, $textoSeccao2);
            atualizar_conteudo_site($ligacao, 'dashboard_publico', 'dashboard_publico', $tituloSeccao4, $textoSeccao4);

            atualizar_conteudo_site($ligacao, 'footer_localizacao', 'rodape', 'Localização', $cidadePaisFooter . '|' . $moradaFooter . '|' . $codigoPostalFooter);
            atualizar_conteudo_site($ligacao, 'footer_horario', 'rodape', 'Horário', $horarioSemanaFooter . '|' . $horarioSabadoFooter . '|' . $horarioDomingoFooter);
            atualizar_conteudo_site($ligacao, 'footer_contactos', 'rodape', 'Contactos', $emailFooter . '|' . $telefone1Footer . '|' . $telefone2Footer);

            $sucesso = 'Conteúdos atualizados com sucesso.';
        }
    }

    $stmt = $ligacao->query("
        SELECT chave, seccao, titulo, texto, imagem
        FROM ConteudoSite
        WHERE ativo = true
    ");

    foreach ($stmt->fetchAll() as $conteudo) {
        $conteudos[$conteudo->chave] = $conteudo;
    }
} catch (PDOException $e) {
    $erro = 'Erro ao guardar ou obter os conteúdos do site.';
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/nav.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Conteúdo Principal -->
<main class="content">
    <section>

        <div class="actions-top">
            <h2>
                <strong>
                    <i class="fas fa-pen-to-square"></i> Gestão de Conteúdos do Site
                </strong>
            </h2>
        </div>

        <hr>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger text-center">
                <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success text-center">
                <?= e($sucesso) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <strong>Foram encontrados os seguintes erros:</strong>

                <ul class="mb-0 mt-2">
                    <?php foreach ($erros as $erroValidacao): ?>
                        <li><?= e($erroValidacao) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="formulario-equipamento" novalidate>

            <!-- Bloco informativo -->
            <div class="card mb-4"
                style="background-color: #fff8d6; border: 1px solid #e0c75a; border-radius: 18px;">
                <div class="card-body">

                    <h4 class="mb-2" style="color: #1f4e79;">
                        <i class="fas fa-circle-info"></i> Conteúdo da página pública
                    </h4>

                    <p class="mb-0">
                        Esta área permite alterar os textos principais apresentados na página pública da MedInventário.
                        Os textos apresentados no formulário são carregados a partir da tabela ConteudoSite, a mesma
                        fonte usada pela página pública.
                    </p>

                </div>
            </div>

            <!-- Separadores -->
            <ul class="nav nav-tabs mb-4" id="separadoresGestaoConteudos" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="navbar-site-tab" data-bs-toggle="tab"
                        data-bs-target="#navbar-site" type="button" role="tab">
                        Navbar
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inicio-tab" data-bs-toggle="tab" data-bs-target="#inicio"
                        type="button" role="tab">
                        Início
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seccao1-tab" data-bs-toggle="tab" data-bs-target="#seccao1"
                        type="button" role="tab">
                        Quem Somos
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seccao2-tab" data-bs-toggle="tab" data-bs-target="#seccao2"
                        type="button" role="tab">
                        Solução
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seccao4-tab" data-bs-toggle="tab" data-bs-target="#seccao4"
                        type="button" role="tab">
                        Dashboard público
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rodape-tab" data-bs-toggle="tab" data-bs-target="#rodape"
                        type="button" role="tab">
                        Rodapé
                    </button>
                </li>

            </ul>

            <div class="tab-content" id="conteudoSeparadoresGestaoConteudos">

                <!-- Separador: Navbar -->
                <div class="tab-pane fade show active" id="navbar-site" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-bars"></i> Navbar da página pública
                            </h3>

                            <div class="mb-3">
                                <label for="nome_site" class="form-label">Nome do site</label>
                                <input type="text" class="form-control" id="nome_site" name="nome_site"
                                    value="<?= e(conteudo_titulo($conteudos, 'site_nome', 'MedInventário')) ?>">
                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-3">
                                    <label for="nav_inicio" class="form-label">Nome do link inicial</label>
                                    <input type="text" class="form-control" id="nav_inicio" name="nav_inicio"
                                        value="<?= e(conteudo_texto($conteudos, 'nav_inicio', 'Início')) ?>">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="nav_seccao1" class="form-label">Nome do link Quem Somos</label>
                                    <input type="text" class="form-control" id="nav_seccao1" name="nav_seccao1"
                                        value="<?= e(conteudo_texto($conteudos, 'nav_quem_somos', 'Quem Somos')) ?>">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="nav_seccao2" class="form-label">Nome do link Solução</label>
                                    <input type="text" class="form-control" id="nav_seccao2" name="nav_seccao2"
                                        value="<?= e(conteudo_texto($conteudos, 'nav_solucao', 'Solução')) ?>">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="nav_seccao3" class="form-label">Nome do link Funcionalidades</label>
                                    <input type="text" class="form-control" id="nav_seccao3" name="nav_seccao3"
                                        value="<?= e(conteudo_texto($conteudos, 'nav_funcionalidades', 'Funcionalidades')) ?>">
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Início -->
                <div class="tab-pane fade" id="inicio" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-house-medical"></i> Secção Início
                            </h3>

                            <div class="mb-3">
                                <label for="titulo_inicio" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo_inicio" name="titulo_inicio"
                                    value="<?= e(conteudo_titulo($conteudos, 'inicio', 'Sistema Web de Apoio ao Inventário Hospitalar')) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="texto_inicio" class="form-label">Texto da secção inicial</label>
                                <textarea class="form-control" id="texto_inicio" name="texto_inicio"
                                    rows="4"><?= e(conteudo_texto($conteudos, 'inicio', 'A MedInventário ajuda instituições de saúde a organizar, consultar e controlar equipamentos médicos de forma simples, centralizada e segura.')) ?></textarea>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Quem Somos -->
                <div class="tab-pane fade" id="seccao1" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-users"></i> Quem Somos
                            </h3>

                            <div class="mb-3">
                                <label for="titulo_seccao1" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo_seccao1" name="titulo_seccao1"
                                    value="<?= e(conteudo_titulo($conteudos, 'quem_somos', 'Quem Somos')) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="texto_seccao1" class="form-label">Texto da secção</label>
                                <textarea class="form-control" id="texto_seccao1" name="texto_seccao1"
                                    rows="5"><?= e(conteudo_texto($conteudos, 'quem_somos', 'A MedInventário é uma solução digital pensada para apoiar hospitais e serviços de saúde na gestão organizada do seu parque tecnológico.

A plataforma centraliza informação essencial sobre equipamentos, fornecedores, localizações, documentação, garantias e contratos, facilitando o acesso rápido aos dados.')) ?></textarea>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Solução -->
                <div class="tab-pane fade" id="seccao2" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-network-wired"></i> Solução
                            </h3>

                            <div class="mb-3">
                                <label for="titulo_seccao2" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo_seccao2" name="titulo_seccao2"
                                    value="<?= e(conteudo_titulo($conteudos, 'solucao', 'A Nossa Solução')) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="texto_seccao2" class="form-label">Texto da secção</label>
                                <textarea class="form-control" id="texto_seccao2" name="texto_seccao2"
                                    rows="5"><?= e(conteudo_texto($conteudos, 'solucao', 'O objetivo da MedInventário é disponibilizar uma plataforma organizada para apoiar o ciclo de vida dos equipamentos médicos, desde o registo inicial até à sua consulta, atualização ou desativação.

A aplicação permite melhorar a rastreabilidade, facilitar a pesquisa de informação e apoiar decisões relacionadas com manutenção, garantias e documentação.')) ?></textarea>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Dashboard público -->
                <div class="tab-pane fade" id="seccao4" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-chart-line"></i> Dashboard público
                            </h3>

                            <div class="mb-3">
                                <label for="titulo_seccao4" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo_seccao4" name="titulo_seccao4"
                                    value="<?= e(conteudo_titulo($conteudos, 'dashboard_publico', 'Informação centralizada para melhor decisão')) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="texto_seccao4" class="form-label">Texto da secção</label>
                                <textarea class="form-control" id="texto_seccao4" name="texto_seccao4"
                                    rows="5"><?= e(conteudo_texto($conteudos, 'dashboard_publico', 'Através de indicadores e alertas, a solução permite identificar equipamentos críticos, garantias próximas do fim, documentação em falta e estados de funcionamento.

Esta informação ajuda os serviços técnicos e administrativos a acompanhar o inventário de forma mais rápida e estruturada.')) ?></textarea>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Rodapé -->
                <div class="tab-pane fade" id="rodape" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-shoe-prints"></i> Rodapé da página pública
                            </h3>

                            <h4 class="mt-3">
                                <i class="fas fa-location-dot"></i> Localização
                            </h4>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="cidade_pais_footer" class="form-label">Cidade e país</label>
                                    <input type="text" class="form-control" id="cidade_pais_footer"
                                        name="cidade_pais_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_localizacao', 0, 'Porto, Portugal')) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="morada_footer" class="form-label">Morada</label>
                                    <input type="text" class="form-control" id="morada_footer"
                                        name="morada_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_localizacao', 1, 'Rua ************ 000')) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="codigo_postal_footer" class="form-label">Código postal</label>
                                    <input type="text" class="form-control" id="codigo_postal_footer"
                                        name="codigo_postal_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_localizacao', 2, '4249-000')) ?>">
                                </div>

                            </div>

                            <h4 class="mt-4">
                                <i class="fas fa-clock"></i> Horário
                            </h4>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="horario_semana_footer" class="form-label">Segunda a sexta</label>
                                    <input type="text" class="form-control" id="horario_semana_footer"
                                        name="horario_semana_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_horario', 0, 'Segunda a sexta: 09:00 - 18:00')) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="horario_sabado_footer" class="form-label">Sábado</label>
                                    <input type="text" class="form-control" id="horario_sabado_footer"
                                        name="horario_sabado_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_horario', 1, 'Sábado: 09:00 - 13:00')) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="horario_domingo_footer" class="form-label">Domingo</label>
                                    <input type="text" class="form-control" id="horario_domingo_footer"
                                        name="horario_domingo_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_horario', 2, 'Domingo: encerrado')) ?>">
                                </div>

                            </div>

                            <h4 class="mt-4">
                                <i class="fas fa-address-book"></i> Contactos
                            </h4>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="email_footer" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_footer"
                                        name="email_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_contactos', 0, 'geral@medinventario.pt')) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="telefone_1_footer" class="form-label">Telefone 1</label>
                                    <input type="text" class="form-control" id="telefone_1_footer"
                                        name="telefone_1_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_contactos', 1, '+351 220 000 000')) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="telefone_2_footer" class="form-label">Telefone 2</label>
                                    <input type="text" class="form-control" id="telefone_2_footer"
                                        name="telefone_2_footer"
                                        value="<?= e(conteudo_linha($conteudos, 'footer_contactos', 2, '+351 914 000 000')) ?>">
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-end gap-2">

                <button type="reset" class="btn btn-outline-secondary botao-anterior">
                    Repor
                </button>

                <button type="submit" class="btn btn-primary">
                    Guardar conteúdos
                </button>

            </div>

        </form>

        <p id="mensagem-formulario"></p>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>