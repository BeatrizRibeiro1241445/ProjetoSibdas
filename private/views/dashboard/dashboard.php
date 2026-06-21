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
$totalAbatidos = 0;
$totalDistribuicaoEstados = 0;

$totalGarantiasExpiradas = 0;
$totalGarantiasExpirar = 0;
$totalSemDocumentacao = 0;
$totalCriticidadeElevada = 0;
$totalDocumentosExpirar = 0;
$totalDocumentosExpirados = 0;
$totalEstadosAtencao = 0;

$resumoServicos = [];
$garantiasExpiradas = [];
$garantiasExpirar = [];
$equipamentosSemDocumentacao = [];
$equipamentosCriticidadeElevada = [];
$documentosExpirar = [];
$documentosExpirados = [];
$equipamentosEstadosAtencao = [];
$distribuicaoCategorias = [];
$distribuicaoServicosGrafico = [];
$distribuicaoEstados = [];
$distribuicaoCriticidade = [];

function formatar_data_dashboard($data)
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y', strtotime($data));
}

try {
    $ligacao = db_connect();

    $totalEquipamentos = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento
            WHERE ativo = true
        ")
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
              AND ee.descricao = 'Inativo'
        ")
        ->fetch()
        ->total;

    $totalAbatidos = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento
            WHERE ativo = false
        ")
        ->fetch()
        ->total;

    $totalDistribuicaoEstados = $totalEquipamentos + $totalAbatidos;

    $totalGarantiasExpiradas = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM (
                SELECT
                    gc.idEquipamento,
                    MAX(gc.dataFim) AS ultimaDataFim
                FROM GarantiaContrato gc
                INNER JOIN Equipamento e
                    ON gc.idEquipamento = e.idEquipamento
                WHERE gc.ativo = true
                  AND e.ativo = true
                  AND gc.dataFim IS NOT NULL
                GROUP BY gc.idEquipamento
                HAVING ultimaDataFim <= CURDATE()
            ) AS garantias_expiradas
        ")
        ->fetch()
        ->total;

    $totalGarantiasExpirar = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM (
                SELECT
                    gc.idEquipamento,
                    MAX(gc.dataFim) AS ultimaDataFim
                FROM GarantiaContrato gc
                INNER JOIN Equipamento e
                    ON gc.idEquipamento = e.idEquipamento
                WHERE gc.ativo = true
                  AND e.ativo = true
                  AND gc.dataFim IS NOT NULL
                GROUP BY gc.idEquipamento
                HAVING ultimaDataFim BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ) AS garantias_expirar
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
                      AND d.nomeFicheiro IS NOT NULL
                      AND d.nomeFicheiro <> ''
                      AND d.caminhoFicheiro IS NOT NULL
                      AND d.caminhoFicheiro <> ''
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
              AND d.nomeFicheiro IS NOT NULL
              AND d.nomeFicheiro <> ''
              AND d.caminhoFicheiro IS NOT NULL
              AND d.caminhoFicheiro <> ''
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
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
              AND d.nomeFicheiro IS NOT NULL
              AND d.nomeFicheiro <> ''
              AND d.caminhoFicheiro IS NOT NULL
              AND d.caminhoFicheiro <> ''
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade <= CURDATE()
        ")
        ->fetch()
        ->total;

    $totalEstadosAtencao = (int) $ligacao
        ->query("
            SELECT COUNT(*) AS total
            FROM Equipamento e
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND ee.descricao <> 'Ativo'
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
                SUM(CASE WHEN ee.descricao = 'Inativo' THEN 1 ELSE 0 END) AS totalInativos,
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
                CONCAT(l.edificio, ' - Piso ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao,
                MAX(gc.dataFim) AS dataFim,
                GROUP_CONCAT(DISTINCT COALESCE(f.designacao, 'Sem fornecedor') SEPARATOR ', ') AS fornecedor
            FROM GarantiaContrato gc
            INNER JOIN Equipamento e
                ON gc.idEquipamento = e.idEquipamento
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            LEFT JOIN Fornecedor f
                ON gc.idFornecedorResponsavel = f.idFornecedor
            WHERE gc.ativo = true
              AND e.ativo = true
              AND gc.dataFim IS NOT NULL
            GROUP BY
                e.idEquipamento,
                e.codigoInterno,
                e.designacao,
                l.servico,
                localizacao
            HAVING MAX(gc.dataFim) <= CURDATE()
            ORDER BY dataFim ASC
        ")
        ->fetchAll();

    $garantiasExpirar = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                l.servico,
                CONCAT(l.edificio, ' - Piso ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao,
                MAX(gc.dataFim) AS dataFim,
                GROUP_CONCAT(DISTINCT COALESCE(f.designacao, 'Sem fornecedor') SEPARATOR ', ') AS fornecedor
            FROM GarantiaContrato gc
            INNER JOIN Equipamento e
                ON gc.idEquipamento = e.idEquipamento
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            LEFT JOIN Fornecedor f
                ON gc.idFornecedorResponsavel = f.idFornecedor
            WHERE gc.ativo = true
              AND e.ativo = true
              AND gc.dataFim IS NOT NULL
            GROUP BY
                e.idEquipamento,
                e.codigoInterno,
                e.designacao,
                l.servico,
                localizacao
            HAVING MAX(gc.dataFim) BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY dataFim ASC
        ")
        ->fetchAll();

    $equipamentosSemDocumentacao = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                ce.descricao AS categoria,
                l.servico,
                CONCAT(l.edificio, ' - Piso ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao,
                ee.descricao AS estado
            FROM Equipamento e
            INNER JOIN CategoriaEquipamento ce
                ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
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
                      AND d.nomeFicheiro IS NOT NULL
                      AND d.nomeFicheiro <> ''
                      AND d.caminhoFicheiro IS NOT NULL
                      AND d.caminhoFicheiro <> ''
              )
            ORDER BY e.codigoInterno
        ")
        ->fetchAll();

    $equipamentosCriticidadeElevada = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                ce.descricao AS categoria,
                l.servico,
                CONCAT(l.edificio, ' - Piso ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao,
                cr.descricao AS criticidade,
                ee.descricao AS estado
            FROM Equipamento e
            INNER JOIN CategoriaEquipamento ce
                ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            INNER JOIN CriticidadeEquipamento cr
                ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND cr.descricao IN ('Alta', 'Suporte de vida')
            ORDER BY
                FIELD(cr.descricao, 'Suporte de vida', 'Alta'),
                e.codigoInterno
        ")
        ->fetchAll();

    $documentosExpirar = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao AS equipamento,
                d.nomeDocumento,
                td.descricao AS tipoDocumento,
                d.dataDocumento,
                d.dataValidade,
                d.nomeFicheiro,
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
              AND d.nomeFicheiro IS NOT NULL
              AND d.nomeFicheiro <> ''
              AND d.caminhoFicheiro IS NOT NULL
              AND d.caminhoFicheiro <> ''
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY d.dataValidade ASC, e.codigoInterno
        ")
        ->fetchAll();

    $documentosExpirados = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao AS equipamento,
                d.nomeDocumento,
                td.descricao AS tipoDocumento,
                d.dataDocumento,
                d.dataValidade,
                d.nomeFicheiro,
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
              AND d.nomeFicheiro IS NOT NULL
              AND d.nomeFicheiro <> ''
              AND d.caminhoFicheiro IS NOT NULL
              AND d.caminhoFicheiro <> ''
              AND d.dataValidade IS NOT NULL
              AND d.dataValidade <= CURDATE()
            ORDER BY d.dataValidade ASC, e.codigoInterno
        ")
        ->fetchAll();

    $equipamentosEstadosAtencao = $ligacao
        ->query("
            SELECT
                e.codigoInterno,
                e.designacao,
                ce.descricao AS categoria,
                l.servico,
                CONCAT(l.edificio, ' - Piso ', l.piso, ' - ', l.servico, ' - ', l.sala) AS localizacao,
                ee.descricao AS estado,
                e.observacoes
            FROM Equipamento e
            INNER JOIN CategoriaEquipamento ce
                ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
            INNER JOIN Localizacao l
                ON e.idLocalizacao = l.idLocalizacao
            INNER JOIN EstadoEquipamento ee
                ON e.idEstadoEquipamento = ee.idEstadoEquipamento
            WHERE e.ativo = true
              AND ee.descricao <> 'Ativo'
            ORDER BY ee.descricao, e.codigoInterno
        ")
        ->fetchAll();

    $distribuicaoCategorias = $ligacao
        ->query("
            SELECT
                ce.descricao AS categoria,
                COUNT(e.idEquipamento) AS total
            FROM CategoriaEquipamento ce
            LEFT JOIN Equipamento e
                ON ce.idCategoriaEquipamento = e.idCategoriaEquipamento
               AND e.ativo = true
            GROUP BY ce.idCategoriaEquipamento, ce.descricao
            HAVING total > 0
            ORDER BY total DESC, ce.descricao
        ")
        ->fetchAll();

    $distribuicaoServicosGrafico = $ligacao
        ->query("
            SELECT
                l.servico,
                COUNT(e.idEquipamento) AS total
            FROM Localizacao l
            LEFT JOIN Equipamento e
                ON l.idLocalizacao = e.idLocalizacao
               AND e.ativo = true
            WHERE l.ativo = true
            GROUP BY l.servico
            HAVING total > 0
            ORDER BY total DESC, l.servico
        ")
        ->fetchAll();

    $distribuicaoEstados = $ligacao
        ->query("
            SELECT
                ee.descricao AS estado,
                COUNT(e.idEquipamento) AS total
            FROM EstadoEquipamento ee
            LEFT JOIN Equipamento e
                ON ee.idEstadoEquipamento = e.idEstadoEquipamento
            GROUP BY ee.idEstadoEquipamento, ee.descricao
            HAVING total > 0
            ORDER BY total DESC, ee.descricao
        ")
        ->fetchAll();

    $distribuicaoCriticidade = $ligacao
        ->query("
            SELECT
                cr.descricao AS criticidade,
                COUNT(e.idEquipamento) AS total
            FROM CriticidadeEquipamento cr
            LEFT JOIN Equipamento e
                ON cr.idCriticidadeEquipamento = e.idCriticidadeEquipamento
               AND e.ativo = true
            GROUP BY cr.idCriticidadeEquipamento, cr.descricao
            HAVING total > 0
            ORDER BY
                FIELD(cr.descricao, 'Suporte de vida', 'Alta', 'Média', 'Baixa'),
                cr.descricao
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
                    alertas de gestão e distribuição dos equipamentos registados.
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
                        <p>Equipamentos ativos na listagem</p>
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

            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100 card-dashboard dashboard-inativos">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-box-archive"></i> Abatidos
                        </h5>
                        <p class="card-text"><?= e($totalAbatidos) ?></p>
                        <p>Equipamentos removidos da listagem</p>
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
                        <p>Equipamentos cuja última garantia/contrato expirou</p>
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
                        <p>Equipamentos sem documentos ativos</p>
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
                    data-secao="secEstadosAtencao" data-collapse="collapseEstadosAtencao">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-screwdriver-wrench"></i> Estados de atenção
                        </h5>
                        <p class="card-text"><?= e($totalEstadosAtencao) ?></p>
                        <p>Equipamentos que não estão no estado Ativo</p>
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
                            <i class="fas fa-calendar-xmark"></i> Equipamentos cuja última garantia/contrato expirou
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
                                    <th>Localização</th>
                                    <th>Fim da garantia/contrato</th>
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
                                            <td><?= e($linha->localizacao) ?></td>
                                            <td><?= e(formatar_data_dashboard($linha->dataFim)) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Não existem equipamentos com garantia/contrato expirado.</td>
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
                            <i class="fas fa-calendar-days"></i> Equipamentos com garantia/contrato a expirar nos próximos 30 dias
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
                                    <th>Localização</th>
                                    <th>Fim da garantia/contrato</th>
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
                                            <td><?= e($linha->localizacao) ?></td>
                                            <td><?= e(formatar_data_dashboard($linha->dataFim)) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Não existem garantias/contratos a expirar nos próximos 30 dias.</td>
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
                                    <th>Categoria</th>
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
                                            <td><?= e($linha->categoria) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->localizacao) ?></td>
                                            <td><?= e($linha->estado) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Todos os equipamentos ativos têm documentação associada.</td>
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
                                    <th>Categoria</th>
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
                                            <td><?= e($linha->categoria) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->localizacao) ?></td>
                                            <td><?= e($linha->criticidade) ?></td>
                                            <td><?= e($linha->estado) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Não existem equipamentos de criticidade elevada.</td>
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
                                    <th>Data do documento</th>
                                    <th>Validade</th>
                                    <th>Fornecedor associado</th>
                                    <th>Ficheiro</th>
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
                                            <td><?= e(formatar_data_dashboard($linha->dataDocumento)) ?></td>
                                            <td><?= e(formatar_data_dashboard($linha->dataValidade)) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                            <td><?= e($linha->nomeFicheiro ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Não existem documentos a expirar nos próximos 30 dias.</td>
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
                                    <th>Data do documento</th>
                                    <th>Validade</th>
                                    <th>Fornecedor associado</th>
                                    <th>Ficheiro</th>
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
                                            <td><?= e(formatar_data_dashboard($linha->dataDocumento)) ?></td>
                                            <td><?= e(formatar_data_dashboard($linha->dataValidade)) ?></td>
                                            <td><?= e($linha->fornecedor) ?></td>
                                            <td><?= e($linha->nomeFicheiro ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Não existem documentos expirados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="accordion-item" id="secEstadosAtencao">
                <h2 class="accordion-header" id="headingEstadosAtencao">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseEstadosAtencao" aria-expanded="false"
                        aria-controls="collapseEstadosAtencao">
                        <strong>
                            <i class="fas fa-screwdriver-wrench"></i> Equipamentos em estados de atenção
                        </strong>
                    </button>
                </h2>

                <div id="collapseEstadosAtencao" class="accordion-collapse collapse"
                    aria-labelledby="headingEstadosAtencao" data-bs-parent="#accordionDashboard">

                    <div class="accordion-body">

                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">
                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Categoria</th>
                                    <th>Serviço</th>
                                    <th>Localização</th>
                                    <th>Estado</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($equipamentosEstadosAtencao)): ?>
                                    <?php foreach ($equipamentosEstadosAtencao as $linha): ?>
                                        <tr>
                                            <td><?= e($linha->codigoInterno) ?></td>
                                            <td><?= e($linha->designacao) ?></td>
                                            <td><?= e($linha->categoria) ?></td>
                                            <td><?= e($linha->servico) ?></td>
                                            <td><?= e($linha->localizacao) ?></td>
                                            <td><?= e($linha->estado) ?></td>
                                            <td><?= e($linha->observacoes ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Todos os equipamentos ativos estão no estado Ativo.</td>
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

                        <?php if (!empty($distribuicaoCategorias)): ?>

                            <?php foreach ($distribuicaoCategorias as $linha): ?>
                                <?php
                                $totalCategoria = (int) $linha->total;
                                $percentagemCategoria = $totalEquipamentos > 0 ? round(($totalCategoria / $totalEquipamentos) * 100, 1) : 0;
                                ?>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= e($linha->categoria) ?></strong>
                                        <span><?= e($totalCategoria) ?> equipamento(s) - <?= e($percentagemCategoria) ?>%</span>
                                    </div>

                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?= e($percentagemCategoria) ?>%;"
                                            aria-valuenow="<?= e($percentagemCategoria) ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="text-center mb-0">
                                Não existem equipamentos ativos para apresentar por categoria.
                            </p>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-grafico-card">
                    <div class="card-body">

                        <h4 id="secDistribuicaoLocalizacao" class="text-center">
                            <i class="fas fa-chart-pie"></i> Distribuição por serviço
                        </h4>

                        <?php if (!empty($distribuicaoServicosGrafico)): ?>

                            <?php foreach ($distribuicaoServicosGrafico as $linha): ?>
                                <?php
                                $totalServico = (int) $linha->total;
                                $percentagemServico = $totalEquipamentos > 0 ? round(($totalServico / $totalEquipamentos) * 100, 1) : 0;
                                ?>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= e($linha->servico) ?></strong>
                                        <span><?= e($totalServico) ?> equipamento(s) - <?= e($percentagemServico) ?>%</span>
                                    </div>

                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?= e($percentagemServico) ?>%;"
                                            aria-valuenow="<?= e($percentagemServico) ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="text-center mb-0">
                                Não existem equipamentos ativos para apresentar por serviço.
                            </p>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-grafico-card">
                    <div class="card-body">

                        <h4 class="text-center">
                            <i class="fas fa-chart-simple"></i> Distribuição por estado
                        </h4>

                        <?php if (!empty($distribuicaoEstados)): ?>

                            <?php foreach ($distribuicaoEstados as $linha): ?>
                                <?php
                                $totalEstado = (int) $linha->total;
                                $percentagemEstado = $totalDistribuicaoEstados > 0 ? round(($totalEstado / $totalDistribuicaoEstados) * 100, 1) : 0;
                                ?>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= e($linha->estado) ?></strong>
                                        <span><?= e($totalEstado) ?> equipamento(s) - <?= e($percentagemEstado) ?>%</span>
                                    </div>

                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?= e($percentagemEstado) ?>%;"
                                            aria-valuenow="<?= e($percentagemEstado) ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="text-center mb-0">
                                Não existem equipamentos para apresentar por estado.
                            </p>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-grafico-card">
                    <div class="card-body">

                        <h4 class="text-center">
                            <i class="fas fa-chart-simple"></i> Distribuição por criticidade
                        </h4>

                        <?php if (!empty($distribuicaoCriticidade)): ?>

                            <?php foreach ($distribuicaoCriticidade as $linha): ?>
                                <?php
                                $totalCriticidade = (int) $linha->total;
                                $percentagemCriticidade = $totalEquipamentos > 0 ? round(($totalCriticidade / $totalEquipamentos) * 100, 1) : 0;
                                ?>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= e($linha->criticidade) ?></strong>
                                        <span><?= e($totalCriticidade) ?> equipamento(s) - <?= e($percentagemCriticidade) ?>%</span>
                                    </div>

                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?= e($percentagemCriticidade) ?>%;"
                                            aria-valuenow="<?= e($percentagemCriticidade) ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="text-center mb-0">
                                Não existem equipamentos ativos para apresentar por criticidade.
                            </p>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>