<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Dashboard';
$body_class = 'pagina-dashboard';

$erro = '';

$totalEquipamentos = 0;
$totalAtivos = 0;
$totalManutencao = 0;
$totalInativos = 0;

$totalGarantiasExpiradas = 0;
$totalGarantiasExpirar = 0;
$totalSemDocumentacao = 0;
$totalCriticidadeElevada = 0;
$totalDocumentosExpirar = 0;
$totalDocumentosExpirados = 0;
$totalAvariadosForaServico = 0;

$resumoServicos = [];
$garantiasExpiradas = [];
$garantiasExpirar = [];
$equipamentosSemDocumentacao = [];
$equipamentosCriticidadeElevada = [];
$documentosExpirar = [];
$documentosExpirados = [];
$equipamentosAvariadosForaServico = [];

try {
    $ligacao = db_connect();

    $totalEquipamentos = (int) $ligacao
        ->query("SELECT COUNT(*) AS total FROM Equipamento WHERE ativo = true")
        ->fetch()
        ->total;

    $totalAtivos = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento e
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND ee.descricao = 'Ativo'
        ")
        ->fetch()
        ->total;

    $totalManutencao = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento e
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND ee.descricao = 'Em manutenção'
        ")
        ->fetch()
        ->total;

    $totalInativos = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento e
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND ee.descricao IN ('Inativo', 'Abatido')
        ")
        ->fetch()
        ->total;

    $totalGarantiasExpiradas = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM GarantiaContrato gc
            INNER JOIN Equipamento e
                ON gc.idEquipamento = e.idEquipamento
            WHERE gc.ativo = true
              AND e.ativo = true
              AND gc.dataFim < CURDATE()
        ")
        ->fetch()
        ->total;

    $totalGarantiasExpirar = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM GarantiaContrato gc
            INNER JOIN Equipamento e
                ON gc.idEquipamento = e.idEquipamento
            WHERE gc.ativo = true
              AND e.ativo = true
              AND gc.dataFim BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ")
        ->fetch()
        ->total;

    $totalSemDocumentacao = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento e
            WHERE e.ativo = true
              AND NOT EXISTS (
                    SELECT 1
                    FROM Documento d
                    WHERE d.idEquipamento = e.idEquipamento
                      AND d.ativo = true
              )
        ")
        ->fetch()
        ->total;

    $totalCriticidadeElevada = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento e
            INNER JOIN CriticidadeEquipamento cr
                ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
            WHERE e.ativo = true
              AND cr.descricao IN ('Alta', 'Suporte de vida')
        ")
        ->fetch()
        ->total;

    $totalDocumentosExpirar = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Documento d
            INNER JOIN Equipamento e
                ON d.idEquipamento = e.idEquipamento
            WHERE d.ativo = true
              AND e.ativo = true
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ")
        ->fetch()
        ->total;

    $totalDocumentosExpirados = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Documento d
            INNER JOIN Equipamento e
                ON d.idEquipamento = e.idEquipamento
            WHERE d.ativo = true
              AND e.ativo = true
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade < CURDATE()
        ")
        ->fetch()
        ->total;

    $totalAvariadosForaServico = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento e
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND ee.descricao IN ('Inativo', 'Abatido')
        ")
        ->fetch()
        ->total;

    $resumoServicos = $ligacao
        ->query("
            SELECT
                l.servico,
                COUNT(e.idEquipamento) AS totalEquipamentos,
                SUM(CASE WHEN ee.descricao = 'Ativo' THEN 1 ELSE 0 END) AS totalAtivos,
                SUM(CASE WHEN ee.descricao = 'Em manutenção' THEN 1 ELSE 0 END) AS totalManutencao,
                SUM(CASE WHEN ee.descricao IN ('Inativo', 'Abatido') THEN 1 ELSE 0 END) AS totalInativos,
                SUM(CASE WHEN cr.descricao = 'Suporte de vida' THEN 1 ELSE 0 END) AS totalSuporteVida
            FROM Localizacao l
            LEFT JOIN Equipamento e
                ON l.idLocalizacao = e.idLocalizacao
               AND e.ativo = true
            LEFT JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            LEFT JOIN CriticidadeEquipamento cr
                ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
            WHERE l.ativo = true
            GROUP BY l.servico
            ORDER BY l.servico
        ")
        ->fetchAll();

    $garantiasExpiradas = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                l.servico,
                gc.dataFim,
                COALESCE(f.designacao, 'Sem fornecedor') AS fornecedor
            FROM GarantiaContrato gc
            INNER JOIN Equipamento e
                ON gc.idEquipamento = e.idEquipamento
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            LEFT JOIN Fornecedor f
                ON gc.idFornecedorResponsavel = f.idFornecedor
            WHERE gc.ativo = true
              AND e.ativo = true
              AND gc.dataFim < CURDATE()
            ORDER BY gc.dataFim ASC
        ")
        ->fetchAll();

    $garantiasExpirar = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                l.servico,
                gc.dataFim,
                COALESCE(f.designacao, 'Sem fornecedor') AS fornecedor
            FROM GarantiaContrato gc
            INNER JOIN Equipamento e
                ON gc.idEquipamento = e.idEquipamento
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            LEFT JOIN Fornecedor f
                ON gc.idFornecedorResponsavel = f.idFornecedor
            WHERE gc.ativo = true
              AND e.ativo = true
              AND gc.dataFim BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY gc.dataFim ASC
        ")
        ->fetchAll();

    $equipamentosSemDocumentacao = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                l.servico,
                l.sala,
                ee.descricao AS estado
            FROM Equipamento e
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND NOT EXISTS (
                    SELECT 1
                    FROM Documento d
                    WHERE d.idEquipamento = e.idEquipamento
                      AND d.ativo = true
              )
            ORDER BY e.codigoInterno
        ")
        ->fetchAll();

    $equipamentosCriticidadeElevada = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                l.servico,
                l.sala,
                cr.descricao AS criticidade,
                ee.descricao AS estado
            FROM Equipamento e
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            INNER JOIN CriticidadeEquipamento cr
                ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND cr.descricao IN ('Alta', 'Suporte de vida')
            ORDER BY e.codigoInterno
        ")
        ->fetchAll();

    $documentosExpirar = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao AS equipamento,
                d.nomeDocumento,
                td.descricao AS tipoDocumento,
                d.dataValidade,
                COALESCE(f.designacao, 'Sem fornecedor') AS fornecedor
            FROM Documento d
            INNER JOIN Equipamento e
                ON d.idEquipamento = e.idEquipamento
            INNER JOIN TipoDocumento td
                ON d.idTipoDocumento = td.idTipoDocumento
            LEFT JOIN Fornecedor f
                ON d.idFornecedor = f.idFornecedor
            WHERE d.ativo = true
              AND e.ativo = true
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY d.dataValidade ASC
        ")
        ->fetchAll();

    $documentosExpirados = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao AS equipamento,
                d.nomeDocumento,
                td.descricao AS tipoDocumento,
                d.dataValidade,
                COALESCE(f.designacao, 'Sem fornecedor') AS fornecedor
            FROM Documento d
            INNER JOIN Equipamento e
                ON d.idEquipamento = e.idEquipamento
            INNER JOIN TipoDocumento td
                ON d.idTipoDocumento = td.idTipoDocumento
            LEFT JOIN Fornecedor f
                ON d.idFornecedor = f.idFornecedor
            WHERE d.ativo = true
              AND e.ativo = true
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade < CURDATE()
            ORDER BY d.dataValidade ASC
        ")
        ->fetchAll();

    $equipamentosAvariadosForaServico = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                l.servico,
                l.sala,
                ee.descricao AS estado,
                e.observacoes
            FROM Equipamento e
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND ee.descricao IN ('Inativo', 'Abatido')
            ORDER BY e.codigoInterno
        ")
        ->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter dados do dashboard.';
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
                    <i class="fas fa-chart-bar"></i> Dashboard
                </strong>
            </h2>
        </div>

        <hr>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger text-center">
                <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-intro mb-4">
            <div>
                <h3>Resumo e indicadores</h3>
                <p>
                    Consulta rápida dos principais números do inventário hospitalar,
                    alertas de gestão e distribuição dos equipamentos.
                </p>
            </div>

            <i class="fas fa-chart-line"></i>
        </div>

        <h3 class="dashboard-titulo-seccao">
            <i class="fas fa-chart-simple"></i> Métricas principais
        </h3>

        <div class="row mt-4">

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-total"
                    data-secao="secResumoServico" data-collapse="">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-laptop-medical"></i> Total
                        </h5>
                        <p class="card-text"><?= e($totalEquipamentos) ?></p>
                        <p>Equipamentos registados</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-ativos"
                    data-secao="secResumoServico" data-collapse="">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-circle-check"></i> Ativos
                        </h5>
                        <p class="card-text"><?= e($totalAtivos) ?></p>
                        <p>Equipamentos em utilização</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-manutencao"
                    data-secao="secResumoServico" data-collapse="">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-screwdriver-wrench"></i> Manutenção
                        </h5>
                        <p class="card-text"><?= e($totalManutencao) ?></p>
                        <p>Equipamentos em manutenção</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-inativos"
                    data-secao="secResumoServico" data-collapse="">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-circle-xmark"></i> Inativos
                        </h5>
                        <p class="card-text"><?= e($totalInativos) ?></p>
                        <p>Equipamentos fora de utilização</p>
                    </div>
                </div>
            </div>

        </div>

        <h3 class="dashboard-titulo-seccao mt-5">
            <i class="fas fa-triangle-exclamation"></i> Alertas de gestão
        </h3>

        <div class="row mt-4">

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                    data-secao="secGarantiasExpiradas" data-collapse="collapseGarantiasExpiradas">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-calendar-xmark"></i> Garantia expirada
                        </h5>
                        <p class="card-text"><?= e($totalGarantiasExpiradas) ?></p>
                        <p>Equipamentos com garantia expirada</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-manutencao"
                    data-secao="secGarantiasExpirar" data-collapse="collapseGarantiasExpirar">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-calendar-days"></i> Garantia a expirar
                        </h5>
                        <p class="card-text"><?= e($totalGarantiasExpirar) ?></p>
                        <p>Nos próximos 30 dias</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-inativos"
                    data-secao="secSemDocumentacao" data-collapse="collapseSemDocumentacao">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-file-circle-exclamation"></i> Sem documentação
                        </h5>
                        <p class="card-text"><?= e($totalSemDocumentacao) ?></p>
                        <p>Equipamentos sem documentação associada</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                    data-secao="secCriticidadeElevada" data-collapse="collapseCriticidadeElevada">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-triangle-exclamation"></i> Criticidade elevada
                        </h5>
                        <p class="card-text"><?= e($totalCriticidadeElevada) ?></p>
                        <p>Equipamentos críticos ou de suporte de vida</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-2">

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-manutencao"
                    data-secao="secDocumentosExpirar" data-collapse="collapseDocumentosExpirar">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-file-circle-exclamation"></i> Documentos a expirar
                        </h5>
                        <p class="card-text"><?= e($totalDocumentosExpirar) ?></p>
                        <p>Documentos com validade próxima</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                    data-secao="secDocumentosExpirados" data-collapse="collapseDocumentosExpirados">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-file-circle-xmark"></i> Documentos expirados
                        </h5>
                        <p class="card-text"><?= e($totalDocumentosExpirados) ?></p>
                        <p>Documentos fora de validade</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-link dashboard-alerta-vermelho"
                    data-secao="secAvariadosForaServico" data-collapse="collapseAvariadosForaServico">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-screwdriver-wrench"></i> Avariados / fora de serviço
                        </h5>
                        <p class="card-text"><?= e($totalAvariadosForaServico) ?></p>
                        <p>Equipamentos avariados ou fora de serviço</p>
                    </div>
                </div>
            </div>

        </div>

        <h3 class="dashboard-titulo-seccao mt-5" id="secResumoServico">
            <i class="fas fa-hospital"></i> Resumo por serviço
        </h3>

        <div class="table-responsive tabela-lista-container">
            <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                data-linhas-pagina="5">
                <thead>
                    <tr>
                        <th>Serviço / Departamento</th>
                        <th>Total de equipamentos</th>
                        <th>Ativos</th>
                        <th>Em manutenção</th>
                        <th>Inativos</th>
                        <th>Suporte de vida</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($resumoServicos)): ?>
                        <?php foreach ($resumoServicos as $linha): ?>
                            <tr>
                                <td><?= e($linha->servico) ?></td>
                                <td><?= e((int) $linha->totalEquipamentos) ?></td>
                                <td><?= e((int) $linha->totalAtivos) ?></td>
                                <td><?= e((int) $linha->totalManutencao) ?></td>
                                <td><?= e((int) $linha->totalInativos) ?></td>
                                <td><?= e((int) $linha->totalSuporteVida) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Não existem dados por serviço.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h3 class="dashboard-titulo-seccao mt-5">
            <i class="fas fa-list-check"></i> Análise detalhada
        </h3>

        <div class="accordion mb-4 dashboard-accordion" id="accordionDashboard">

            <div class="accordion-item" id="secGarantiasExpiradas">
                <h2 class="accordion-header" id="headingGarantiasExpiradas">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseGarantiasExpiradas" aria-expanded="false"
                        aria-controls="collapseGarantiasExpiradas">
                        <strong>
                            <i class="fas fa-calendar-xmark"></i> Equipamentos com garantia expirada
                        </strong>
                    </button>
                </h2>

                <div id="collapseGarantiasExpiradas" class="accordion-collapse collapse"
                    aria-labelledby="headingGarantiasExpiradas" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Serviço</th>
                                    <th>Fim da garantia</th>
                                    <th>Fornecedor / Entidade</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($garantiasExpiradas)): ?>
                                    <?php foreach ($garantiasExpiradas as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->designacao) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->dataFim) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Não existem equipamentos com garantia expirada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="accordion-item" id="secGarantiasExpirar">
                <h2 class="accordion-header" id="headingGarantiasExpirar">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseGarantiasExpirar" aria-expanded="false"
                        aria-controls="collapseGarantiasExpirar">
                        <strong>
                            <i class="fas fa-calendar-days"></i> Equipamentos com garantia a expirar nos próximos 30 dias
                        </strong>
                    </button>
                </h2>

                <div id="collapseGarantiasExpirar" class="accordion-collapse collapse"
                    aria-labelledby="headingGarantiasExpirar" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Serviço</th>
                                    <th>Fim da garantia</th>
                                    <th>Fornecedor / Entidade</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($garantiasExpirar)): ?>
                                    <?php foreach ($garantiasExpirar as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->designacao) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->dataFim) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Não existem garantias a expirar nos próximos 30 dias.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="accordion-item" id="secSemDocumentacao">
                <h2 class="accordion-header" id="headingSemDocumentacao">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseSemDocumentacao" aria-expanded="false"
                        aria-controls="collapseSemDocumentacao">
                        <strong>
                            <i class="fas fa-file-circle-exclamation"></i> Equipamentos sem documentação associada
                        </strong>
                    </button>
                </h2>

                <div id="collapseSemDocumentacao" class="accordion-collapse collapse"
                    aria-labelledby="headingSemDocumentacao" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Serviço</th>
                                    <th>Localização</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($equipamentosSemDocumentacao)): ?>
                                    <?php foreach ($equipamentosSemDocumentacao as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->designacao) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->sala) ?></td>
                                            <td><?= e($linha->estado) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Todos os equipamentos ativos têm documentação associada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="accordion-item" id="secCriticidadeElevada">
                <h2 class="accordion-header" id="headingCriticidadeElevada">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseCriticidadeElevada" aria-expanded="false"
                        aria-controls="collapseCriticidadeElevada">
                        <strong>
                            <i class="fas fa-triangle-exclamation"></i> Equipamentos de criticidade elevada
                        </strong>
                    </button>
                </h2>

                <div id="collapseCriticidadeElevada" class="accordion-collapse collapse"
                    aria-labelledby="headingCriticidadeElevada" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Serviço</th>
                                    <th>Localização</th>
                                    <th>Criticidade</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($equipamentosCriticidadeElevada)): ?>
                                    <?php foreach ($equipamentosCriticidadeElevada as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->designacao) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->sala) ?></td>
                                            <td><?= e($linha->criticidade) ?></td>
                                            <td><?= e($linha->estado) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Não existem equipamentos de criticidade elevada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="accordion-item" id="secDocumentosExpirar">
                <h2 class="accordion-header" id="headingDocumentosExpirar">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseDocumentosExpirar" aria-expanded="false"
                        aria-controls="collapseDocumentosExpirar">
                        <strong>
                            <i class="fas fa-file-circle-exclamation"></i> Documentos a expirar
                        </strong>
                    </button>
                </h2>

                <div id="collapseDocumentosExpirar" class="accordion-collapse collapse"
                    aria-labelledby="headingDocumentosExpirar" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Documento</th>
                                    <th>Tipo de documento</th>
                                    <th>Data de validade</th>
                                    <th>Fornecedor associado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($documentosExpirar)): ?>
                                    <?php foreach ($documentosExpirar as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->equipamento) ?></td>
                                            <td><?= e($linha->nomeDocumento) ?></td>
                                            <td><?= e($linha->tipoDocumento) ?></td>
                                            <td><?= e($linha->dataValidade) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Não existem documentos a expirar nos próximos 30 dias.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="accordion-item" id="secDocumentosExpirados">
                <h2 class="accordion-header" id="headingDocumentosExpirados">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseDocumentosExpirados" aria-expanded="false"
                        aria-controls="collapseDocumentosExpirados">
                        <strong>
                            <i class="fas fa-file-circle-xmark"></i> Documentos expirados
                        </strong>
                    </button>
                </h2>

                <div id="collapseDocumentosExpirados" class="accordion-collapse collapse"
                    aria-labelledby="headingDocumentosExpirados" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Documento</th>
                                    <th>Tipo de documento</th>
                                    <th>Data de validade</th>
                                    <th>Fornecedor associado</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($documentosExpirados)): ?>
                                    <?php foreach ($documentosExpirados as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->equipamento) ?></td>
                                            <td><?= e($linha->nomeDocumento) ?></td>
                                            <td><?= e($linha->tipoDocumento) ?></td>
                                            <td><?= e($linha->dataValidade) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Não existem documentos expirados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="accordion-item" id="secAvariadosForaServico">
                <h2 class="accordion-header" id="headingAvariadosForaServico">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseAvariadosForaServico" aria-expanded="false"
                        aria-controls="collapseAvariadosForaServico">
                        <strong>
                            <i class="fas fa-screwdriver-wrench"></i> Equipamentos avariados ou fora de serviço
                        </strong>
                    </button>
                </h2>

                <div id="collapseAvariadosForaServico" class="accordion-collapse collapse"
                    aria-labelledby="headingAvariadosForaServico" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Serviço</th>
                                    <th>Localização</th>
                                    <th>Estado</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($equipamentosAvariadosForaServico)): ?>
                                    <?php foreach ($equipamentosAvariadosForaServico as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->designacao) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->sala) ?></td>
                                            <td><?= e($linha->estado) ?></td>
                                            <td><?= e($linha->observacoes ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Não existem equipamentos inativos ou abatidos.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>

        <h3 class="dashboard-titulo-seccao mt-5">
            <i class="fas fa-chart-pie"></i> Distribuições gerais
        </h3>

        <div class="row mt-4">

            <div class="col-12 col-lg-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-grafico-card">
                    <div class="card-body">

                        <h4 id="secDistribuicaoCategoria" class="text-center">
                            <i class="fas fa-chart-pie"></i> Distribuição por categoria
                        </h4>

                        <div class="row align-items-center mt-4">

                            <div class="col-12 col-md-6 text-center">
                                <div class="grafico-pie grafico-categoria"></div>
                            </div>

                            <div class="col-12 col-md-6 mt-4 mt-md-0">

                                <p class="legenda-grafico">
                                    <span class="cor-grafico azul"></span>
                                    <strong>Monitorização</strong><br>
                                </p>

                                <p class="legenda-grafico">
                                    <span class="cor-grafico verde"></span>
                                    <strong>Suporte de vida</strong><br>
                                </p>

                                <p class="legenda-grafico">
                                    <span class="cor-grafico amarelo"></span>
                                    <strong>Infusão</strong><br>
                                </p>

                                <p class="legenda-grafico">
                                    <span class="cor-grafico vermelho"></span>
                                    <strong>Diagnóstico</strong><br>
                                </p>

                                <p class="legenda-grafico">
                                    <span class="cor-grafico cinzento"></span>
                                    <strong>Outros</strong><br>
                                </p>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-grafico-card">
                    <div class="card-body">

                        <h4 id="secDistribuicaoLocalizacao" class="text-center">
                            <i class="fas fa-chart-pie"></i> Distribuição por localização
                        </h4>

                        <div class="row align-items-center mt-4">

                            <div class="col-12 col-md-6 text-center">
                                <div class="grafico-pie grafico-localizacao"></div>
                            </div>

                            <div class="col-12 col-md-6 mt-4 mt-md-0">

                                <p class="legenda-grafico">
                                    <span class="cor-grafico azul"></span>
                                    <strong>Serviços hospitalares</strong><br>
                                </p>

                                <p class="legenda-grafico">
                                    <span class="cor-grafico verde"></span>
                                    <strong>Localizações clínicas</strong><br>
                                </p>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>