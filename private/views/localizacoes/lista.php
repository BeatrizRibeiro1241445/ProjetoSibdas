<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Localizações';
$body_class = '';

$erro = '';
$localizacoes = [];

$filtroCodigoEquipamento = trim($_GET['filtro_codigo_equipamento'] ?? '');
$filtroNomeEquipamento = trim($_GET['filtro_nome_equipamento'] ?? '');
$filtroCategoria = trim($_GET['filtro_categoria'] ?? '');
$filtroEdificio = trim($_GET['filtro_edificio'] ?? '');
$filtroPiso = trim($_GET['filtro_piso'] ?? '');
$filtroServico = trim($_GET['filtro_servico'] ?? '');
$filtroSala = trim($_GET['filtro_sala'] ?? '');

try {
    $ligacao = db_connect();

    $sql = "
        SELECT DISTINCT
            l.idLocalizacao,
            l.categoria,
            l.edificio,
            l.piso,
            l.servico,
            l.sala
        FROM Localizacao l
        LEFT JOIN Equipamento e
            ON e.idLocalizacao = l.idLocalizacao
        WHERE l.ativo = true
    ";

    $parametros = [];

    if ($filtroCodigoEquipamento !== '') {
        $sql .= " AND e.codigoInterno LIKE :codigoEquipamento";
        $parametros[':codigoEquipamento'] = '%' . $filtroCodigoEquipamento . '%';
    }

    if ($filtroNomeEquipamento !== '') {
        $sql .= " AND e.designacao LIKE :nomeEquipamento";
        $parametros[':nomeEquipamento'] = '%' . $filtroNomeEquipamento . '%';
    }

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

    $sql .= "
        ORDER BY
            l.edificio,
            l.piso,
            l.servico,
            l.sala
    ";

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

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

            <a href="novo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova localização
            </a>
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
                                <label for="filtro_codigo_equipamento" class="form-label fw-semibold">
                                    Código do equipamento
                                </label>
                                <input type="text" class="form-control text-center" id="filtro_codigo_equipamento"
                                    name="filtro_codigo_equipamento" placeholder="Ex.: 004.002.00"
                                    value="<?= e($filtroCodigoEquipamento) ?>">
                            </div>

                            <div>
                                <label for="filtro_nome_equipamento" class="form-label fw-semibold">
                                    Nome do equipamento
                                </label>
                                <input type="text" class="form-control text-center" id="filtro_nome_equipamento"
                                    name="filtro_nome_equipamento" placeholder="Ex.: Monitor Multiparamétrico"
                                    value="<?= e($filtroNomeEquipamento) ?>">
                            </div>

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
                                    name="filtro_piso" placeholder="Ex.: Piso 2"
                                    value="<?= e($filtroPiso) ?>">
                            </div>

                            <div>
                                <label for="filtro_servico" class="form-label fw-semibold">
                                    Serviço / Departamento
                                </label>
                                <input type="text" class="form-control text-center" id="filtro_servico"
                                    name="filtro_servico" placeholder="Ex.: UCI"
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

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>