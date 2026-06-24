<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (($_SESSION['perfil'] ?? '') !== 'administrador') {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$page_title = APP_NAME . ' - Utilizadores';
$body_class = '';

$erro = '';
$utilizadores = [];

$filtroNome = trim($_GET['filtro_nome'] ?? '');
$filtroUsername = trim($_GET['filtro_username'] ?? '');
$filtroEmail = trim($_GET['filtro_email'] ?? '');
$filtroPerfil = trim($_GET['filtro_perfil'] ?? '');
$ordenarPor = trim($_GET['ordenar_por'] ?? '');

$paginaAtual = max(1, (int) ($_GET['pagina'] ?? 1));
$registosPorPagina = 5;
$offset = ($paginaAtual - 1) * $registosPorPagina;

$totalRegistos = 0;
$totalPaginas = 1;

$idUtilizadorSessao = (int) ($_SESSION['idUtilizador'] ?? 0);
$usernameSessao = $_SESSION['utilizador'] ?? '';

function texto_perfil_utilizador($perfil)
{
    switch ($perfil) {
        case 'administrador':
            return 'Administrador';

        case 'tecnico':
            return 'Técnico';

        case 'gestor_hospitalar':
            return 'Gestor Hospitalar';

        case 'profissional_saude':
            return 'Profissional de Saúde';

        default:
            return 'Utilizador';
    }
}

function formatar_data_utilizador($data)
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y', strtotime($data));
}

function formatar_data_hora_utilizador($data)
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y H:i', strtotime($data));
}

try {
    $ligacao = db_connect();

    $sql = "
        SELECT
            u.idUtilizador,
            u.username,
            u.email,
            u.nome,
            u.perfil,
            u.lastLogin,
            u.dataFimContrato
        FROM Utilizador u
        WHERE u.ativo = true
    ";

    $parametros = [];

    if ($filtroNome !== '') {
        $sql .= " AND u.nome LIKE :nome";
        $parametros[':nome'] = '%' . $filtroNome . '%';
    }

    if ($filtroUsername !== '') {
        $sql .= " AND u.username LIKE :username";
        $parametros[':username'] = '%' . $filtroUsername . '%';
    }

    if ($filtroEmail !== '') {
        $sql .= " AND u.email LIKE :email";
        $parametros[':email'] = '%' . $filtroEmail . '%';
    }

    if ($filtroPerfil !== '') {
        $sql .= " AND u.perfil = :perfil";
        $parametros[':perfil'] = $filtroPerfil;
    }

    $sqlSemOrdenacao = $sql;

    $stmtTotal = $ligacao->prepare("
        SELECT COUNT(*) AS total
        FROM ($sqlSemOrdenacao) AS resultado_total
    ");

    foreach ($parametros as $nome => $valor) {
        $stmtTotal->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmtTotal->execute();
    $totalRegistos = (int) $stmtTotal->fetch()->total;

    $totalPaginas = max(1, (int) ceil($totalRegistos / $registosPorPagina));

    if ($paginaAtual > $totalPaginas) {
        $paginaAtual = $totalPaginas;
        $offset = ($paginaAtual - 1) * $registosPorPagina;
    }

    switch ($ordenarPor) {
        case 'nome_az':
            $sql .= " ORDER BY u.nome ASC, u.username ASC";
            break;

        case 'nome_za':
            $sql .= " ORDER BY u.nome DESC, u.username DESC";
            break;

        case 'contrato_crescente':
            $sql .= " ORDER BY u.dataFimContrato IS NULL, u.dataFimContrato ASC, u.nome ASC";
            break;

        case 'contrato_decrescente':
            $sql .= " ORDER BY u.dataFimContrato IS NULL, u.dataFimContrato DESC, u.nome ASC";
            break;

        default:
            $sql .= " ORDER BY u.nome ASC, u.username ASC";
            break;
    }

    $sql .= " LIMIT :limite OFFSET :offset";

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limite', $registosPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $utilizadores = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter a lista de utilizadores.';
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/nav.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Conteúdo Principal -->
<main class="content">
    <section>

        <?php
        $queryExportar = $_GET;
        unset($queryExportar['pagina']);

        $linkExportar = 'exportar_csv.php';

        if (!empty($queryExportar)) {
            $linkExportar .= '?' . http_build_query($queryExportar);
        }
        ?>

        <div class="actions-top">
            <h2>
                <strong>
                    <i class="fas fa-users"></i> Gestão de Utilizadores
                </strong>
            </h2>

            <div class="d-flex gap-2">
                <a href="novo.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo utilizador
                </a>
            </div>
        </div>

        <hr>

        <!-- Pesquisa e filtros -->
        <div class="accordion mb-5" id="accordionPesquisaUtilizadores">

            <div class="accordion-item border-0 shadow-sm">

                <h2 class="accordion-header" id="headingPesquisaUtilizadores">
                    <button class="accordion-button collapsed justify-content-center text-center" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapsePesquisaUtilizadores"
                        aria-expanded="false" aria-controls="collapsePesquisaUtilizadores">

                        <strong>
                            <i class="fas fa-magnifying-glass"></i> Pesquisa, filtros e ordenação
                        </strong>

                    </button>
                </h2>

                <div id="collapsePesquisaUtilizadores" class="accordion-collapse collapse"
                    aria-labelledby="headingPesquisaUtilizadores" data-bs-parent="#accordionPesquisaUtilizadores">

                    <div class="accordion-body bg-light">

                        <form action="lista.php" method="get" class="filtros-equipamentos">

                            <div>
                                <label for="filtro_nome" class="form-label fw-semibold">
                                    Nome
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_nome"
                                    name="filtro_nome" placeholder="Ex.: Beatriz Ribeiro"
                                    value="<?= e($filtroNome) ?>">
                            </div>

                            <div>
                                <label for="filtro_username" class="form-label fw-semibold">
                                    Utilizador
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_username"
                                    name="filtro_username" placeholder="Ex.: beatriz.ribeiro"
                                    value="<?= e($filtroUsername) ?>">
                            </div>

                            <div>
                                <label for="filtro_email" class="form-label fw-semibold">
                                    Email
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_email"
                                    name="filtro_email" placeholder="Ex.: utilizador@email.pt"
                                    value="<?= e($filtroEmail) ?>">
                            </div>

                            <div>
                                <label for="filtro_perfil" class="form-label fw-semibold">
                                    Perfil
                                </label>

                                <select class="form-select text-center" id="filtro_perfil" name="filtro_perfil">
                                    <option value="">Todos</option>

                                    <option value="administrador" <?= $filtroPerfil === 'administrador' ? 'selected' : '' ?>>
                                        Administrador
                                    </option>

                                    <option value="tecnico" <?= $filtroPerfil === 'tecnico' ? 'selected' : '' ?>>
                                        Técnico
                                    </option>

                                    <option value="gestor_hospitalar" <?= $filtroPerfil === 'gestor_hospitalar' ? 'selected' : '' ?>>
                                        Gestor Hospitalar
                                    </option>

                                    <option value="profissional_saude" <?= $filtroPerfil === 'profissional_saude' ? 'selected' : '' ?>>
                                        Profissional de Saúde
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="ordenar_por" class="form-label fw-semibold">
                                    Ordenar por
                                </label>

                                <select class="form-select text-center" id="ordenar_por" name="ordenar_por">
                                    <option value="">Sem ordenação</option>

                                    <option value="nome_az" <?= $ordenarPor === 'nome_az' ? 'selected' : '' ?>>
                                        Nome A-Z
                                    </option>

                                    <option value="nome_za" <?= $ordenarPor === 'nome_za' ? 'selected' : '' ?>>
                                        Nome Z-A
                                    </option>

                                    <option value="contrato_crescente" <?= $ordenarPor === 'contrato_crescente' ? 'selected' : '' ?>>
                                        Fim de contrato crescente
                                    </option>

                                    <option value="contrato_decrescente" <?= $ordenarPor === 'contrato_decrescente' ? 'selected' : '' ?>>
                                        Fim de contrato decrescente
                                    </option>
                                </select>
                            </div>

                            <div class="filtros-botoes">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-magnifying-glass"></i> Pesquisar
                                </button>

                                <a href="lista.php" class="btn btn-secondary">
                                    Limpar
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($erro)): ?>

            <div class="alert alert-danger text-center">
                <?= e($erro) ?>
            </div>

        <?php endif; ?>

        <!-- Tabela -->
        <div class="table-responsive tabela-lista-container">
            <table class="table table-hover table-bordered align-middle text-center tabela-lista">

                <thead>
                    <tr>
                        <th>Utilizador</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Último login</th>
                        <th>Fim contrato</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($erro) && count($utilizadores) > 0): ?>

                        <?php foreach ($utilizadores as $utilizador): ?>

                            <tr>
                                <td>
                                    <strong><?= e($utilizador->username) ?></strong>
                                </td>

                                <td><?= e($utilizador->nome) ?></td>

                                <td><?= e($utilizador->email) ?></td>

                                <td><?= e(texto_perfil_utilizador($utilizador->perfil)) ?></td>

                                <td><?= e(formatar_data_hora_utilizador($utilizador->lastLogin)) ?></td>

                                <td><?= e(formatar_data_utilizador($utilizador->dataFimContrato)) ?></td>

                                <td>
                                    <div class="acoes-tabela">

                                        <?php if ((int) $utilizador->idUtilizador !== $idUtilizadorSessao && $utilizador->username !== $usernameSessao): ?>

                                            <a href="apagar.php?id_utilizador=<?= aes_encrypt($utilizador->idUtilizador) ?>" class="btn btn-sm btn-acao btn-arquivar" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Conta atual
                                            </span>

                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php elseif (empty($erro)): ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                Não existem utilizadores registados para os filtros selecionados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>

            <nav aria-label="Paginação de utilizadores" class="mt-4">

                <ul class="pagination justify-content-center paginacao-equipamentos">

                    <?php
                    $queryAnterior = $_GET;
                    $queryAnterior['pagina'] = max(1, $paginaAtual - 1);
                    ?>

                    <li class="page-item <?= $paginaAtual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="lista.php?<?= e(http_build_query($queryAnterior)) ?>" title="Página anterior">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <?php for ($pagina = 1; $pagina <= $totalPaginas; $pagina++): ?>

                        <?php
                        $queryPagina = $_GET;
                        $queryPagina['pagina'] = $pagina;
                        ?>

                        <li class="page-item <?= $pagina === $paginaAtual ? 'active' : '' ?>">
                            <a class="page-link" href="lista.php?<?= e(http_build_query($queryPagina)) ?>">
                                <?= e($pagina) ?>
                            </a>
                        </li>

                    <?php endfor; ?>

                    <?php
                    $querySeguinte = $_GET;
                    $querySeguinte['pagina'] = min($totalPaginas, $paginaAtual + 1);
                    ?>

                    <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="lista.php?<?= e(http_build_query($querySeguinte)) ?>" title="Página seguinte">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>

                </ul>

            </nav>

        <?php endif; ?>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>