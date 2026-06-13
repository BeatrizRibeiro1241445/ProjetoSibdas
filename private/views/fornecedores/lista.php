<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Fornecedores';
$body_class = '';

$erro = '';
$fornecedores = [];

$filtroNif = trim($_GET['filtro_nif'] ?? '');
$filtroDesignacao = trim($_GET['filtro_designacao'] ?? '');
$filtroEmail = trim($_GET['filtro_email'] ?? '');
$filtroTelefone = trim($_GET['filtro_telefone'] ?? '');
$filtroTipoFornecedor = trim($_GET['filtro_tipo_fornecedor'] ?? '');
$filtroEquipamento = trim($_GET['filtro_equipamento'] ?? '');

$paginaAtual = max(1, (int) ($_GET['pagina'] ?? 1));
$registosPorPagina = 5;
$offset = ($paginaAtual - 1) * $registosPorPagina;

$totalRegistos = 0;
$totalPaginas = 1;

try {
    $ligacao = db_connect();

    $sql = "
        SELECT DISTINCT
            f.idFornecedor,
            f.nif,
            f.email,
            f.designacao,
            f.telefone,
            f.morada,
            f.website,
            f.pessoaContacto,
            f.telefonePessoaContacto,
            f.tipoFornecedor
        FROM Fornecedor f
        LEFT JOIN EquipamentoFornecedor ef
            ON f.idFornecedor = ef.idFornecedor
        LEFT JOIN Equipamento e
            ON ef.idEquipamento = e.idEquipamento
        WHERE f.ativo = true
    ";

    $parametros = [];

    if ($filtroDesignacao !== '') {
        $sql .= " AND f.designacao LIKE :designacao";
        $parametros[':designacao'] = '%' . $filtroDesignacao . '%';
    }

    if ($filtroNif !== '') {
        $sql .= " AND f.nif LIKE :nif";
        $parametros[':nif'] = '%' . $filtroNif . '%';
    }

    if ($filtroTipoFornecedor !== '') {
        $sql .= " AND f.tipoFornecedor LIKE :tipoFornecedor";
        $parametros[':tipoFornecedor'] = '%' . $filtroTipoFornecedor . '%';
    }

    if ($filtroEmail !== '') {
        $sql .= " AND f.email LIKE :email";
        $parametros[':email'] = '%' . $filtroEmail . '%';
    }

    if ($filtroTelefone !== '') {
        $sql .= " AND f.telefone LIKE :telefone";
        $parametros[':telefone'] = '%' . $filtroTelefone . '%';
    }

    if ($filtroEquipamento !== '') {
        $sql .= " AND e.designacao LIKE :equipamento";
        $parametros[':equipamento'] = '%' . $filtroEquipamento . '%';
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

    $sql .= " ORDER BY f.designacao";
    $sql .= " LIMIT :limite OFFSET :offset";

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limite', $registosPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $fornecedores = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter a lista de fornecedores.';
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
                    <i class="fas fa-truck-medical"></i> Gestão de Fornecedores
                </strong>
            </h2>

            <a href="novo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo fornecedor
            </a>
        </div>

        <hr>

        <!-- Pesquisa e filtros -->
        <div class="accordion mb-5" id="accordionPesquisaFornecedores">

            <div class="accordion-item border-0 shadow-sm">

                <h2 class="accordion-header" id="headingPesquisaFornecedores">
                    <button class="accordion-button collapsed justify-content-center text-center" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapsePesquisaFornecedores"
                        aria-expanded="false" aria-controls="collapsePesquisaFornecedores">

                        <strong>
                            <i class="fas fa-magnifying-glass"></i> Pesquisa e filtros
                        </strong>

                    </button>
                </h2>

                <div id="collapsePesquisaFornecedores" class="accordion-collapse collapse"
                    aria-labelledby="headingPesquisaFornecedores" data-bs-parent="#accordionPesquisaFornecedores">

                    <div class="accordion-body bg-light">

                        <form action="lista.php" method="get" class="filtros-equipamentos">

                            <div>
                                <label for="filtro_designacao" class="form-label fw-semibold">
                                    Nome da empresa
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_designacao"
                                    name="filtro_designacao" placeholder="Ex.: MedTech Portugal"
                                    value="<?= e($filtroDesignacao) ?>">
                            </div>

                            <div>
                                <label for="filtro_nif" class="form-label fw-semibold">
                                    NIF
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_nif"
                                    name="filtro_nif" placeholder="Ex.: 509000000"
                                    value="<?= e($filtroNif) ?>">
                            </div>

                            <div>
                                <label for="filtro_tipo_fornecedor" class="form-label fw-semibold">
                                    Tipo de fornecedor
                                </label>

                                <select class="form-select text-center" id="filtro_tipo_fornecedor"
                                    name="filtro_tipo_fornecedor">

                                    <option value="">Todos</option>

                                    <option value="Fabricante" <?= $filtroTipoFornecedor === 'Fabricante' ? 'selected' : '' ?>>
                                        Fabricante
                                    </option>

                                    <option value="Fornecedor comercial" <?= $filtroTipoFornecedor === 'Fornecedor comercial' ? 'selected' : '' ?>>
                                        Fornecedor comercial
                                    </option>

                                    <option value="Assistência técnica" <?= $filtroTipoFornecedor === 'Assistência técnica' ? 'selected' : '' ?>>
                                        Empresa de assistência técnica
                                    </option>

                                </select>
                            </div>

                            <div>
                                <label for="filtro_email" class="form-label fw-semibold">
                                    Email
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_email"
                                    name="filtro_email" placeholder="Ex.: geral@medtech.pt"
                                    value="<?= e($filtroEmail) ?>">
                            </div>

                            <div>
                                <label for="filtro_telefone" class="form-label fw-semibold">
                                    Telefone
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_telefone"
                                    name="filtro_telefone" placeholder="Ex.: +351 220 000 000"
                                    value="<?= e($filtroTelefone) ?>">
                            </div>

                            <div>
                                <label for="filtro_equipamento" class="form-label fw-semibold">
                                    Equipamento associado
                                </label>

                                <input type="text" class="form-control text-center" id="filtro_equipamento"
                                    name="filtro_equipamento" placeholder="Ex.: Monitor Multiparamétrico"
                                    value="<?= e($filtroEquipamento) ?>">
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

        <!-- Tabela -->
        <div class="table-responsive tabela-lista-container">
            <table class="table table-hover table-bordered align-middle text-center tabela-lista">

                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>NIF</th>
                        <th>Tipo</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($erro)): ?>

                        <tr>
                            <td colspan="6" class="text-center text-danger">
                                <?= e($erro) ?>
                            </td>
                        </tr>

                    <?php elseif (count($fornecedores) > 0): ?>

                        <?php foreach ($fornecedores as $fornecedor): ?>

                            <tr>
                                <td>
                                    <strong><?= e($fornecedor->designacao) ?></strong>
                                </td>

                                <td><?= e($fornecedor->nif) ?></td>

                                <td><?= e($fornecedor->tipoFornecedor) ?></td>

                                <td><?= e($fornecedor->telefone) ?></td>

                                <td><?= e($fornecedor->email) ?></td>

                                <td>
                                    <div class="acoes-tabela">
                                        <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                Não existem fornecedores registados para os filtros selecionados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>

            <nav aria-label="Paginação de fornecedores" class="mt-4">

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