<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Equipamentos';
$body_class = '';

$erro = '';
$equipamentos = [];

$filtroCodigo = trim($_GET['filtro_codigo'] ?? '');
$filtroDesignacao = trim($_GET['filtro_designacao'] ?? '');
$filtroSerie = trim($_GET['filtro_serie'] ?? '');
$filtroMarca = trim($_GET['filtro_marca'] ?? '');
$filtroEstado = trim($_GET['filtro_estado'] ?? '');
$filtroCriticidade = trim($_GET['filtro_criticidade'] ?? '');
$filtroCategoria = trim($_GET['filtro_categoria'] ?? '');
$filtroLocalizacao = trim($_GET['filtro_localizacao'] ?? '');
$ordenarPor = trim($_GET['ordenar_por'] ?? '');

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
        WHERE e.ativo = true
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

    if ($filtroMarca !== '') {
        $sql .= " AND e.marca LIKE :marca";
        $parametros[':marca'] = '%' . $filtroMarca . '%';
    }

    if ($filtroEstado !== '') {
        $sql .= " AND ee.descricao = :estado";
        $parametros[':estado'] = $filtroEstado;
    }

    if ($filtroCriticidade !== '') {
        $sql .= " AND cr.descricao = :criticidade";
        $parametros[':criticidade'] = $filtroCriticidade;
    }

    if ($filtroCategoria !== '') {
        $sql .= " AND ce.descricao LIKE :categoria";
        $parametros[':categoria'] = '%' . $filtroCategoria . '%';
    }

    if ($filtroLocalizacao !== '') {
        $sql .= " AND CONCAT(l.edificio, ' ', l.piso, ' ', l.servico, ' ', l.sala) LIKE :localizacao";
        $parametros[':localizacao'] = '%' . $filtroLocalizacao . '%';
    }

    switch ($ordenarPor) {
        case 'codigo_crescente':
            $sql .= " ORDER BY e.codigoInterno ASC";
            break;

        case 'codigo_decrescente':
            $sql .= " ORDER BY e.codigoInterno DESC";
            break;

        case 'designacao_az':
            $sql .= " ORDER BY e.designacao ASC";
            break;

        case 'designacao_za':
            $sql .= " ORDER BY e.designacao DESC";
            break;

        case 'criticidade':
            $sql .= "
                ORDER BY FIELD(cr.descricao, 'Suporte de vida', 'Alta', 'Média', 'Baixa'),
                         e.designacao ASC
            ";
            break;

        case 'estado':
            $sql .= " ORDER BY ee.descricao ASC, e.designacao ASC";
            break;

        default:
            $sql .= " ORDER BY e.codigoInterno ASC";
            break;
    }

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->execute();
    $equipamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter a lista de equipamentos.';
}

function classe_estado_equipamento($estado)
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

function classe_criticidade_equipamento($criticidade)
{
    switch ($criticidade) {
        case 'Suporte de vida':
        case 'Alta':
            return 'table-danger fw-bold';

        case 'Média':
            return 'table-warning fw-bold';

        case 'Baixa':
            return 'table-success fw-bold';

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
                    <i class="fas fa-laptop-medical"></i> Gestão de Equipamentos
                </strong>
            </h2>

            <a href="novo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo equipamento
            </a>
        </div>

        <hr>

        <!-- Pesquisa e filtros -->
        <div class="accordion mb-5" id="accordionPesquisaEquipamentos">

            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header" id="headingPesquisaEquipamentos">
                    <button class="accordion-button collapsed justify-content-center text-center" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapsePesquisaEquipamentos"
                        aria-expanded="false" aria-controls="collapsePesquisaEquipamentos">

                        <strong>
                            <i class="fas fa-magnifying-glass"></i> Pesquisa, filtros e ordenação
                        </strong>

                    </button>
                </h2>

                <div id="collapsePesquisaEquipamentos" class="accordion-collapse collapse"
                    aria-labelledby="headingPesquisaEquipamentos" data-bs-parent="#accordionPesquisaEquipamentos">

                    <div class="accordion-body bg-light">

                        <form action="lista.php" method="get" class="filtros-equipamentos">

                            <div>
                                <label for="filtro_codigo" class="form-label fw-semibold">Código interno</label>
                                <input type="text" class="form-control text-center" id="filtro_codigo"
                                    name="filtro_codigo" placeholder="Ex.: 004.002.00"
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
                                    name="filtro_serie" placeholder="Ex.: MP5-2022"
                                    value="<?= e($filtroSerie) ?>">
                            </div>

                            <div>
                                <label for="filtro_marca" class="form-label fw-semibold">Marca</label>
                                <input type="text" class="form-control text-center" id="filtro_marca"
                                    name="filtro_marca" placeholder="Ex.: Philips"
                                    value="<?= e($filtroMarca) ?>">
                            </div>

                            <div>
                                <label for="filtro_estado" class="form-label fw-semibold">Estado</label>
                                <select class="form-select text-center" id="filtro_estado" name="filtro_estado">
                                    <option value="">Todos</option>

                                    <option value="Ativo" <?= $filtroEstado === 'Ativo' ? 'selected' : '' ?>>
                                        Ativo
                                    </option>

                                    <option value="Em manutenção" <?= $filtroEstado === 'Em manutenção' ? 'selected' : '' ?>>
                                        Em manutenção
                                    </option>

                                    <option value="Inativo" <?= $filtroEstado === 'Inativo' ? 'selected' : '' ?>>
                                        Inativo
                                    </option>

                                    <option value="Em calibração" <?= $filtroEstado === 'Em calibração' ? 'selected' : '' ?>>
                                        Em calibração
                                    </option>

                                    <option value="Abatido" <?= $filtroEstado === 'Abatido' ? 'selected' : '' ?>>
                                        Abatido
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="filtro_criticidade" class="form-label fw-semibold">Criticidade</label>
                                <select class="form-select text-center" id="filtro_criticidade"
                                    name="filtro_criticidade">

                                    <option value="">Todas</option>

                                    <option value="Baixa" <?= $filtroCriticidade === 'Baixa' ? 'selected' : '' ?>>
                                        Baixa
                                    </option>

                                    <option value="Média" <?= $filtroCriticidade === 'Média' ? 'selected' : '' ?>>
                                        Média
                                    </option>

                                    <option value="Alta" <?= $filtroCriticidade === 'Alta' ? 'selected' : '' ?>>
                                        Alta
                                    </option>

                                    <option value="Suporte de vida" <?= $filtroCriticidade === 'Suporte de vida' ? 'selected' : '' ?>>
                                        Suporte de vida
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="filtro_categoria" class="form-label fw-semibold">
                                    Categoria / Grupo
                                </label>
                                <input type="text" class="form-control text-center" id="filtro_categoria"
                                    name="filtro_categoria" placeholder="Ex.: Monitorização"
                                    value="<?= e($filtroCategoria) ?>">
                            </div>

                            <div>
                                <label for="filtro_localizacao" class="form-label fw-semibold">Localização</label>
                                <input type="text" class="form-control text-center" id="filtro_localizacao"
                                    name="filtro_localizacao" placeholder="Ex.: UCI"
                                    value="<?= e($filtroLocalizacao) ?>">
                            </div>

                            <div>
                                <label for="ordenar_por" class="form-label fw-semibold">Ordenar por</label>
                                <select class="form-select text-center" id="ordenar_por" name="ordenar_por">
                                    <option value="">Sem ordenação</option>

                                    <option value="codigo_crescente" <?= $ordenarPor === 'codigo_crescente' ? 'selected' : '' ?>>
                                        Código interno crescente
                                    </option>

                                    <option value="codigo_decrescente" <?= $ordenarPor === 'codigo_decrescente' ? 'selected' : '' ?>>
                                        Código interno decrescente
                                    </option>

                                    <option value="designacao_az" <?= $ordenarPor === 'designacao_az' ? 'selected' : '' ?>>
                                        Designação A-Z
                                    </option>

                                    <option value="designacao_za" <?= $ordenarPor === 'designacao_za' ? 'selected' : '' ?>>
                                        Designação Z-A
                                    </option>

                                    <option value="criticidade" <?= $ordenarPor === 'criticidade' ? 'selected' : '' ?>>
                                        Criticidade
                                    </option>

                                    <option value="estado" <?= $ordenarPor === 'estado' ? 'selected' : '' ?>>
                                        Estado
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

        <!-- Tabela -->
        <div class="table-responsive tabela-lista-container">
            <table class="table table-hover table-bordered align-middle text-center tabela-lista">
                <thead>
                    <tr>
                        <th>Código interno</th>
                        <th>Designação</th>
                        <th>N.º Série</th>
                        <th>Marca / Modelo</th>
                        <th>Estado</th>
                        <th>Criticidade</th>
                        <th>Localização</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($erro)): ?>

                        <tr>
                            <td colspan="8" class="text-center text-danger">
                                <?= e($erro) ?>
                            </td>
                        </tr>

                    <?php elseif (count($equipamentos) > 0): ?>

                        <?php foreach ($equipamentos as $equipamento): ?>
                            <tr>
                                <td>
                                    <strong><?= e($equipamento->codigoInterno) ?></strong>
                                </td>

                                <td><?= e($equipamento->designacao) ?></td>

                                <td><?= e($equipamento->numeroSerie) ?></td>

                                <td>
                                    <?= e($equipamento->marca) ?>
                                    <?php if (!empty($equipamento->modelo)): ?>
                                        / <?= e($equipamento->modelo) ?>
                                    <?php endif; ?>
                                </td>

                                <td class="<?= e(classe_estado_equipamento($equipamento->estado)) ?>">
                                    <?= e($equipamento->estado) ?>
                                </td>

                                <td class="<?= e(classe_criticidade_equipamento($equipamento->criticidade)) ?>">
                                    <?= e($equipamento->criticidade) ?>
                                </td>

                                <td><?= e($equipamento->localizacao) ?></td>

                                <td>
                                    <div class="acoes-tabela">
                                        <a href="detalhes.php" class="btn btn-sm btn-acao btn-consultar" title="Consultar">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="editar.php" class="btn btn-sm btn-acao btn-editar" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <a href="apagar.php" class="btn btn-sm btn-acao btn-arquivar" title="Arquivar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="text-center">
                                Não existem equipamentos registados para os filtros selecionados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>