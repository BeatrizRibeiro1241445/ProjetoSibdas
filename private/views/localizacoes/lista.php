<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Localizações';
$body_class = '';

$erro = '';
$localizacoes = [];

$podeGerirLocalizacoes = in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico']);

$filtroCategoria = trim($_GET['filtro_categoria'] ?? '');
$filtroEdificio = trim($_GET['filtro_edificio'] ?? '');
$filtroPiso = trim($_GET['filtro_piso'] ?? '');
$filtroServico = trim($_GET['filtro_servico'] ?? '');
$filtroSala = trim($_GET['filtro_sala'] ?? '');

$paginaAtual = max(1, (int) ($_GET['pagina'] ?? 1));
$registosPorPagina = 5;
$offset = ($paginaAtual - 1) * $registosPorPagina;

$totalRegistos = 0;
$totalPaginas = 1;

try {
    $ligacao = db_connect();

    $sql = "
        SELECT
            l.idLocalizacao,
            l.categoria,
            l.edificio,
            l.piso,
            l.servico,
            l.sala
        FROM Localizacao l
        WHERE l.ativo = true
    ";

    $parametros = [];

    if ($filtroCategoria !== '') {
        $sql .= " AND l.categoria LIKE :categoria";
        $parametros[':categoria'] = '%' . $filtroCategoria . '%';
    }

    if ($filtroEdificio !== '') {
        $sql .= " AND l.edificio LIKE :edificio";
        $parametros[':edificio'] = '%' . $filtroEdificio . '%';
    }

    if ($filtroPiso !== '') {
        $sql .= " AND l.piso LIKE :piso";
        $parametros[':piso'] = '%' . $filtroPiso . '%';
    }

    if ($filtroServico !== '') {
        $sql .= " AND l.servico LIKE :servico";
        $parametros[':servico'] = '%' . $filtroServico . '%';
    }

    if ($filtroSala !== '') {
        $sql .= " AND l.sala LIKE :sala";
        $parametros[':sala'] = '%' . $filtroSala . '%';
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

    $sql .= "
        ORDER BY
            l.edificio,
            l.piso,
            l.servico,
            l.sala
    ";

    $sql .= " LIMIT :limite OFFSET :offset";

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limite', $registosPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $localizacoes = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter a lista de localizações.';
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
                    <i class="fas fa-location-dot"></i> Gestão de Localizações
                </strong>
            </h2>

            <?php if ($podeGerirLocalizacoes): ?>
                <a href="novo.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova localização
                </a>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Pesquisa e filtros -->
        <div class="accordion mb-5" id="accordionPesquisaLocalizacoes">

            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header" id="headingPesquisaLocalizacoes">
                    <button class="accordion-button collapsed justify-content-center text-center" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapsePesquisaLocalizacoes"
                        aria-expanded="false" aria-controls="collapsePesquisaLocalizacoes">

                        <strong>
                            <i class="fas fa-magnifying-glass"></i> Pesquisa e filtros
                        </strong>

                    </button>
                </h2>

                <div id="collapsePesquisaLocalizacoes" class="accordion-collapse collapse"
                    aria-labelledby="headingPesquisaLocalizacoes" data-bs-parent="#accordionPesquisaLocalizacoes">

                    <div class="accordion-body bg-light">

                        <form action="lista.php" method="get" class="filtros-equipamentos">

                            <div>
                                <label for="filtro_categoria" class="form-label fw-semibold">Categoria</label>
                                <input type="text" class="form-control text-center" id="filtro_categoria"
                                    name="filtro_categoria" placeholder="Ex.: Área clínica crítica"
                                    value="<?= e($filtroCategoria) ?>">
                            </div>

                            <div>
                                <label for="filtro_edificio" class="form-label fw-semibold">Edifício</label>
                                <input type="text" class="form-control text-center" id="filtro_edificio"
                                    name="filtro_edificio" placeholder="Ex.: Hospital Central"
                                    value="<?= e($filtroEdificio) ?>">
                            </div>

                            <div>
                                <label for="filtro_piso" class="form-label fw-semibold">Piso</label>
                                <input type="text" class="form-control text-center" id="filtro_piso"
                                    name="filtro_piso" placeholder="Ex.: 2"
                                    value="<?= e($filtroPiso) ?>">
                            </div>

                            <div>
                                <label for="filtro_servico" class="form-label fw-semibold">
                                    Serviço / Departamento
                                </label>
                                <input type="text" class="form-control text-center" id="filtro_servico"
                                    name="filtro_servico" placeholder="Ex.: Unidade de Cuidados Intensivos"
                                    value="<?= e($filtroServico) ?>">
                            </div>

                            <div>
                                <label for="filtro_sala" class="form-label fw-semibold">Sala / Gabinete</label>
                                <input type="text" class="form-control text-center" id="filtro_sala"
                                    name="filtro_sala" placeholder="Ex.: Sala 1"
                                    value="<?= e($filtroSala) ?>">
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
                        <th>Categoria</th>
                        <th>Edifício</th>
                        <th>Piso</th>
                        <th>Serviço / Departamento</th>
                        <th>Sala / Gabinete</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($erro) && count($localizacoes) > 0): ?>

                        <?php foreach ($localizacoes as $localizacao): ?>
                            <tr>
                                <td><?= e($localizacao->categoria) ?></td>
                                <td><?= e($localizacao->edificio) ?></td>
                                <td><?= e($localizacao->piso) ?></td>
                                <td><?= e($localizacao->servico) ?></td>
                                <td><?= e($localizacao->sala) ?></td>

                                <td>
                                    <div class="acoes-tabela">
                                        <a href="detalhes.php?id_localizacao=<?= aes_encrypt($localizacao->idLocalizacao) ?>" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <?php if ($podeGerirLocalizacoes): ?>
                                            <a href="editar.php?id_localizacao=<?= aes_encrypt($localizacao->idLocalizacao) ?>" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                                <i class="fas fa-pen-to-square"></i>
                                            </a>

                                            <a href="apagar.php?id_localizacao=<?= aes_encrypt($localizacao->idLocalizacao) ?>" class="btn btn-sm btn-acao btn-arquivar" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php elseif (empty($erro)): ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                Não existem localizações registadas para os filtros selecionados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>

            <nav aria-label="Paginação de localizações" class="mt-4">

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