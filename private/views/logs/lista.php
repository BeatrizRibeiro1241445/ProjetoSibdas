<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (($_SESSION['perfil'] ?? '') !== 'administrador') {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$page_title = APP_NAME . ' - Registo de Eventos';
$body_class = '';

$erro = '';
$logs = [];
$tiposEvento = [];

$pesquisa = trim($_GET['pesquisa'] ?? '');
$tipoEvento = trim($_GET['tipoEvento'] ?? '');
$dataInicio = trim($_GET['dataInicio'] ?? '');
$dataFim = trim($_GET['dataFim'] ?? '');

function data_valida_logs($data)
{
    if ($data === '') {
        return true;
    }

    $objetoData = DateTime::createFromFormat('Y-m-d', $data);

    return $objetoData && $objetoData->format('Y-m-d') === $data;
}

if (!data_valida_logs($dataInicio)) {
    $dataInicio = '';
}

if (!data_valida_logs($dataFim)) {
    $dataFim = '';
}

try {
    $ligacao = db_connect();

    $tiposEvento = $ligacao->query("
        SELECT DISTINCT tipoEvento
        FROM LogSistema
        ORDER BY tipoEvento
    ")->fetchAll();

    $condicoes = [];
    $parametros = [];

    if ($pesquisa !== '') {
        $condicoes[] = "(
            username LIKE :pesquisa
            OR perfil LIKE :pesquisa
            OR tipoEvento LIKE :pesquisa
            OR descricao LIKE :pesquisa
            OR ip LIKE :pesquisa
        )";

        $parametros[':pesquisa'] = '%' . $pesquisa . '%';
    }

    if ($tipoEvento !== '') {
        $condicoes[] = "tipoEvento = :tipoEvento";
        $parametros[':tipoEvento'] = $tipoEvento;
    }

    if ($dataInicio !== '') {
        $condicoes[] = "DATE(dataHora) >= :dataInicio";
        $parametros[':dataInicio'] = $dataInicio;
    }

    if ($dataFim !== '') {
        $condicoes[] = "DATE(dataHora) <= :dataFim";
        $parametros[':dataFim'] = $dataFim;
    }

    $where = '';

    if (!empty($condicoes)) {
        $where = 'WHERE ' . implode(' AND ', $condicoes);
    }

    $sql = "
        SELECT
            idLogSistema,
            idUtilizador,
            username,
            perfil,
            tipoEvento,
            descricao,
            ip,
            dataHora
        FROM LogSistema
        $where
        ORDER BY dataHora DESC, idLogSistema DESC
        LIMIT 200
    ";

    $stmt = $ligacao->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
    }

    $stmt->execute();
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao carregar o registo de eventos.';
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
                    <i class="fas fa-clock-rotate-left"></i> Registo de Eventos
                </strong>
            </h2>
        </div>

        <hr>

        <!-- Pesquisa e filtros -->
        <div class="accordion mb-5" id="accordionPesquisaLogs">

            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header" id="headingPesquisaLogs">
                    <button class="accordion-button collapsed justify-content-center text-center" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapsePesquisaLogs"
                        aria-expanded="false" aria-controls="collapsePesquisaLogs">

                        <strong>
                            <i class="fas fa-magnifying-glass"></i> Pesquisa e filtros
                        </strong>

                    </button>
                </h2>

                <div id="collapsePesquisaLogs" class="accordion-collapse collapse"
                    aria-labelledby="headingPesquisaLogs" data-bs-parent="#accordionPesquisaLogs">

                    <div class="accordion-body bg-light">

                        <form action="lista.php" method="get" class="filtros-equipamentos">

                            <div>
                                <label for="pesquisa" class="form-label fw-semibold">
                                    Pesquisa
                                </label>

                                <input type="text" class="form-control text-center" id="pesquisa" name="pesquisa"
                                    placeholder="Ex.: LOGIN_SUCESSO, beatriz.ribeiro, administrador"
                                    value="<?= e($pesquisa) ?>">
                            </div>

                            <div>
                                <label for="tipoEvento" class="form-label fw-semibold">
                                    Tipo de evento
                                </label>

                                <select class="form-select text-center" id="tipoEvento" name="tipoEvento">
                                    <option value="">Todos os eventos</option>

                                    <?php foreach ($tiposEvento as $tipo): ?>
                                        <option value="<?= e($tipo->tipoEvento) ?>" <?= $tipoEvento === $tipo->tipoEvento ? 'selected' : '' ?>>
                                            <?= e($tipo->tipoEvento) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="dataInicio" class="form-label fw-semibold">
                                    Desde
                                </label>

                                <input type="date" class="form-control text-center" id="dataInicio" name="dataInicio"
                                    value="<?= e($dataInicio) ?>">
                            </div>

                            <div>
                                <label for="dataFim" class="form-label fw-semibold">
                                    Até
                                </label>

                                <input type="date" class="form-control text-center" id="dataFim" name="dataFim"
                                    value="<?= e($dataFim) ?>">
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
            <table class="table table-hover table-bordered align-middle text-center tabela-lista tabela-paginada-dashboard"
       data-linhas-pagina="10">

                <thead>
                    <tr>
                        <th>Data / Hora</th>
                        <th>Utilizador</th>
                        <th>Perfil</th>
                        <th>Evento</th>
                        <th>Descrição</th>
                        <th>IP</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($erro) && count($logs) > 0): ?>

                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <?= e(date('d/m/Y H:i:s', strtotime($log->dataHora))) ?>
                                </td>

                                <td>
                                    <?= e($log->username ?: 'Sem sessão') ?>
                                </td>

                                <td>
                                    <?= e($log->perfil ?: '-') ?>
                                </td>

                                <td>
                                    <span class="badge text-bg-primary">
                                        <?= e($log->tipoEvento) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= e($log->descricao ?: '-') ?>
                                </td>

                                <td>
                                    <?= e($log->ip ?: '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php elseif (empty($erro)): ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                Não existem eventos registados para os filtros selecionados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>
        </div>

        <p class="text-muted mt-3 mb-0 text-center">
            São apresentados no máximo os 200 eventos mais recentes, de acordo com os filtros aplicados.
        </p>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
