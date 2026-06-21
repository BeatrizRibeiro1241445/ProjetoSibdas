<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico'])) {
    header('Location: lista.php');
    exit;
}

$page_title = APP_NAME . ' - Equipamentos Removidos';
$body_class = '';

$erro = '';
$equipamentos = [];

$filtroCodigo = trim($_GET['filtro_codigo'] ?? '');
$filtroDesignacao = trim($_GET['filtro_designacao'] ?? '');
$filtroSerie = trim($_GET['filtro_serie'] ?? '');
$filtroEstado = trim($_GET['filtro_estado'] ?? '');
$ordenarPor = trim($_GET['ordenar_por'] ?? '');

$paginaAtual = max(1, (int) ($_GET['pagina'] ?? 1));
$registosPorPagina = 5;
$offset = ($paginaAtual - 1) * $registosPorPagina;

$totalRegistos = 0;
$totalPaginas = 1;

try {
    $ligacao = db_connect();

    $sql = "
        SELECT
            e.idEquipamento,
            e.codigoInterno,
            e.numeroSerie,
            e.designacao,
            e.marca,
            e.modelo,
            ce.descricao AS categoria,
            ee.descricao AS estado,
            cr.descricao AS criticidade,
            CONCAT(l.edificio, ' - ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao
        FROM Equipamento e
        INNER JOIN CategoriaEquipamento ce
            ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
        INNER JOIN EstadoEquipamento ee
            ON e.idEstadoEquipamento = ee.idEstadoEquipamento
        INNER JOIN CriticidadeEquipamento cr
            ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
        INNER JOIN Localizacao l
            ON e.idLocalizacao = l.idLocalizacao
        WHERE e.ativo = false
    ";

    $parametros = [];

    if ($filtroCodigo !== '') {
        $sql .= " AND e.codigoInterno LIKE :codigo";
        $parametros[':codigo'] = '%' . $filtroCodigo . '%';
    }

    if ($filtroDesignacao !== '') {
        $sql .= " AND e.designacao LIKE :designacao";
        $parametros[':designacao'] = '%' . $filtroDesignacao . '%';
    }

    if ($filtroSerie !== '') {
        $sql .= " AND e.numeroSerie LIKE :serie";
        $parametros[':serie'] = '%' . $filtroSerie . '%';
    }

    if ($filtroEstado !== '') {
        $sql .= " AND ee.descricao = :estado";
        $parametros[':estado'] = $filtroEstado;
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
        case 'codigo_decrescente':
            $sql .= " ORDER BY e.codigoInterno DESC";
            break;

        case 'designacao_az':
            $sql .= " ORDER BY e.designacao ASC";
            break;

        case 'designacao_za':
            $sql .= " ORDER BY e.designacao DESC";
            break;

        case 'codigo_crescente':
        default:
            $sql .= " ORDER BY e.codigoInterno ASC";
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
    $equipamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter a lista de equipamentos removidos.';
}

function classe_estado_equipamento_removido($estado)
{
    switch ($estado) {
        case 'Ativo':
            return 'table-success fw-bold';

        case 'Em manutenção':
        case 'Em calibração':
            return 'table-warning fw-bold';

        case 'Inativo':
        case 'Abatido':
            return 'table-secondary fw-bold';

        default:
            return 'fw-bold';
    }
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
                    <i class="fas fa-box-archive"></i> Equipamentos Removidos
                </strong>
            </h2>

            <a href="lista.php" class="btn btn-outline-secondary botao-anterior">
                <i class="fas fa-arrow-left"></i> Voltar aos equipamentos
            </a>
        </div>

        <hr>

        <!-- Pesquisa e filtros -->
        <div class="accordion mb-5" id="accordionPesquisaEquipamentosRemovidos">

            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header" id="headingPesquisaEquipamentosRemovidos">
                    <button class="accordion-button collapsed justify-content-center text-center" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapsePesquisaEquipamentosRemovidos"
                        aria-expanded="false" aria-controls="collapsePesquisaEquipamentosRemovidos">

                        <strong>
                            <i class="fas fa-magnifying-glass"></i> Pesquisa, filtros e ordenação
                        </strong>

                    </button>
                </h2>

                <div id="collapsePesquisaEquipamentosRemovidos" class="accordion-collapse collapse"
                    aria-labelledby="headingPesquisaEquipamentosRemovidos" data-bs-parent="#accordionPesquisaEquipamentosRemovidos">

                    <div class="accordion-body bg-light">

                        <form action="eliminados.php" method="get" class="filtros-equipamentos">

                            <div>
                                <label for="filtro_codigo" class="form-label fw-semibold">Código interno</label>
                                <input type="text" class="form-control text-center" id="filtro_codigo"
                                    name="filtro_codigo" placeholder="Ex.: 001.001.01"
                                    value="<?= e($filtroCodigo) ?>">
                            </div>

                            <div>
                                <label for="filtro_designacao" class="form-label fw-semibold">Designação</label>
                                <input type="text" class="form-control text-center" id="filtro_designacao"
                                    name="filtro_designacao" placeholder="Ex.: Monitor"
                                    value="<?= e($filtroDesignacao) ?>">
                            </div>

                            <div>
                                <label for="filtro_serie" class="form-label fw-semibold">Número de série</label>
                                <input type="text" class="form-control text-center" id="filtro_serie"
                                    name="filtro_serie" placeholder="Ex.: SN123"
                                    value="<?= e($filtroSerie) ?>">
                            </div>

                            <div>
                                <label for="filtro_estado" class="form-label fw-semibold">Estado</label>
                                <select class="form-select text-center" id="filtro_estado" name="filtro_estado">
                                    <option value="">Todos</option>
                                    <option value="Inativo" <?= $filtroEstado === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                                    <option value="Abatido" <?= $filtroEstado === 'Abatido' ? 'selected' : '' ?>>Abatido</option>
                                </select>
                            </div>

                            <div>
                                <label for="ordenar_por" class="form-label fw-semibold">Ordenar por</label>
                                <select class="form-select text-center" id="ordenar_por" name="ordenar_por">
                                    <option value="codigo_crescente" <?= $ordenarPor === 'codigo_crescente' || $ordenarPor === '' ? 'selected' : '' ?>>Código crescente</option>
                                    <option value="codigo_decrescente" <?= $ordenarPor === 'codigo_decrescente' ? 'selected' : '' ?>>Código decrescente</option>
                                    <option value="designacao_az" <?= $ordenarPor === 'designacao_az' ? 'selected' : '' ?>>Designação A-Z</option>
                                    <option value="designacao_za" <?= $ordenarPor === 'designacao_za' ? 'selected' : '' ?>>Designação Z-A</option>
                                </select>
                            </div>

                            <div class="filtros-botoes">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-magnifying-glass"></i> Pesquisar
                                </button>

                                <a href="eliminados.php" class="btn btn-secondary">
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
                        <th>Código interno</th>
                        <th>Número de série</th>
                        <th>Equipamento</th>
                        <th>Categoria</th>
                        <th>Estado</th>
                        <th>Localização</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($erro) && count($equipamentos) > 0): ?>

                        <?php foreach ($equipamentos as $equipamento): ?>
                            <tr>
                                <td><?= e($equipamento->codigoInterno) ?></td>
                                <td><?= e($equipamento->numeroSerie) ?></td>

                                <td>
                                    <strong><?= e($equipamento->designacao) ?></strong><br>
                                    <span class="text-muted"><?= e($equipamento->marca) ?> <?= e($equipamento->modelo) ?></span>
                                </td>

                                <td><?= e($equipamento->categoria) ?></td>

                                <td class="<?= e(classe_estado_equipamento_removido($equipamento->estado)) ?>">
                                    <?= e($equipamento->estado) ?>
                                </td>

                                <td><?= e($equipamento->localizacao) ?></td>

                                <td>
                                    <div class="acoes-tabela">
                                        <a href="restaurar.php?id_equipamento=<?= aes_encrypt($equipamento->idEquipamento) ?>" class="btn btn-sm btn-acao btn-consultar" title="Restaurar">
                                            <i class="fas fa-rotate-left"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php elseif (empty($erro)): ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                Não existem equipamentos removidos para os filtros selecionados.
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>

            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>

            <nav aria-label="Paginação de equipamentos removidos" class="mt-4">

                <ul class="pagination justify-content-center paginacao-equipamentos">

                    <?php
                    $queryAnterior = $_GET;
                    $queryAnterior['pagina'] = max(1, $paginaAtual - 1);
                    ?>

                    <li class="page-item <?= $paginaAtual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="eliminados.php?<?= e(http_build_query($queryAnterior)) ?>" title="Página anterior">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <?php for ($pagina = 1; $pagina <= $totalPaginas; $pagina++): ?>

                        <?php
                        $queryPagina = $_GET;
                        $queryPagina['pagina'] = $pagina;
                        ?>

                        <li class="page-item <?= $pagina === $paginaAtual ? 'active' : '' ?>">
                            <a class="page-link" href="eliminados.php?<?= e(http_build_query($queryPagina)) ?>">
                                <?= e($pagina) ?>
                            </a>
                        </li>

                    <?php endfor; ?>

                    <?php
                    $querySeguinte = $_GET;
                    $querySeguinte['pagina'] = min($totalPaginas, $paginaAtual + 1);
                    ?>

                    <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="eliminados.php?<?= e(http_build_query($querySeguinte)) ?>" title="Página seguinte">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>

                </ul>

            </nav>

        <?php endif; ?>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>