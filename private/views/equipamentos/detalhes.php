<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$idEquipamentoEncrypted = $_GET['id_equipamento'] ?? null;
$idEquipamento = aes_decrypt($idEquipamentoEncrypted);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit;
}

$idEquipamento = (int) $idEquipamento;

$page_title = APP_NAME . ' - Consultar Equipamento';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$equipamento = null;
$fornecedoresAssociados = [];
$documentosAssociados = [];
$movimentacoesLocalizacao = [];
$garantiaContrato = null;

function classe_estado_detalhes_equipamento($estado)
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

function classe_criticidade_detalhes_equipamento($criticidade)
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

function formato_data_detalhes_equipamento($data)
{
    if (empty($data)) {
        return '-';
    }

    return date('d/m/Y', strtotime($data));
}

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT
            e.*,
            ce.descricao AS categoria,
            ee.descricao AS estado,
            cr.descricao AS criticidade,
            te.descricao AS tipoEntrada,
            l.categoria AS categoriaLocalizacao,
            l.edificio,
            l.piso,
            l.servico,
            l.sala,
            l.observacoes AS observacoesLocalizacao
        FROM Equipamento e
        INNER JOIN CategoriaEquipamento ce
            ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
        INNER JOIN EstadoEquipamento ee
            ON e.idEstadoEquipamento = ee.idEstadoEquipamento
        INNER JOIN CriticidadeEquipamento cr
            ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
        INNER JOIN TipoEntrada te
            ON e.idTipoEntrada = te.idTipoEntrada
        INNER JOIN Localizacao l
            ON e.idLocalizacao = l.idLocalizacao
        WHERE e.idEquipamento = :idEquipamento
          AND e.ativo = true
    ");

    $stmt->bindParam(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmt->execute();

    $equipamento = $stmt->fetch();

    if (!$equipamento) {
        header('Location: lista.php');
        exit;
    }

    $stmtFornecedores = $ligacao->prepare("
        SELECT
            f.designacao,
            f.telefone,
            f.email,
            f.website,
            f.pessoaContacto,
            f.telefonePessoaContacto,
            f.pessoaContacto2,
            f.telefonePessoaContacto2,
            ef.tipoRelacao,
            ef.observacoes
        FROM EquipamentoFornecedor ef
        INNER JOIN Fornecedor f
            ON ef.idFornecedor = f.idFornecedor
        WHERE ef.idEquipamento = :idEquipamento
          AND f.ativo = true
        ORDER BY f.designacao, ef.tipoRelacao
    ");

    $stmtFornecedores->bindParam(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtFornecedores->execute();

    $fornecedoresAssociados = $stmtFornecedores->fetchAll();


    $stmtMovimentacoes = $ligacao->prepare("
        SELECT
            me.dataLocalizacao,
            me.responsavel,
            me.motivo,
            l.categoria,
            l.edificio,
            l.piso,
            l.servico,
            l.sala
        FROM MovimentacaoEquipamento me
        INNER JOIN Localizacao l
            ON me.idLocalizacao = l.idLocalizacao
        WHERE me.idEquipamento = :idEquipamento
          AND me.ativo = true
        ORDER BY me.dataLocalizacao ASC, me.idMovimentacaoEquipamento ASC
    ");

    $stmtMovimentacoes->bindParam(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtMovimentacoes->execute();

    $movimentacoesLocalizacao = $stmtMovimentacoes->fetchAll();

    if (empty($movimentacoesLocalizacao)) {
        $movimentacoesLocalizacao[] = (object) [
            'dataLocalizacao' => $equipamento->dataAquisicao,
            'responsavel' => 'Registo existente',
            'motivo' => 'Localização',
            'categoria' => $equipamento->categoriaLocalizacao,
            'edificio' => $equipamento->edificio,
            'piso' => $equipamento->piso,
            'servico' => $equipamento->servico,
            'sala' => $equipamento->sala
        ];
    }

    $stmtDocumentos = $ligacao->prepare("
        SELECT
            td.descricao AS tipoDocumento,
            d.nomeDocumento,
            d.dataDocumento,
            d.dataValidade,
            d.nomeFicheiro,
            d.caminhoFicheiro,
            COALESCE(f.designacao, 'Sem fornecedor') AS fornecedor
        FROM Documento d
        INNER JOIN TipoDocumento td
            ON d.idTipoDocumento = td.idTipoDocumento
        LEFT JOIN Fornecedor f
            ON d.idFornecedor = f.idFornecedor
        WHERE d.idEquipamento = :idEquipamento
          AND d.ativo = true
        ORDER BY d.dataDocumento DESC, d.nomeDocumento
    ");

    $stmtDocumentos->bindParam(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtDocumentos->execute();

    $documentosAssociados = $stmtDocumentos->fetchAll();

    $stmtGarantia = $ligacao->prepare("
        SELECT
            gc.*,
            COALESCE(f.designacao, 'Sem fornecedor') AS fornecedorResponsavel
        FROM GarantiaContrato gc
        LEFT JOIN Fornecedor f
            ON gc.idFornecedorResponsavel = f.idFornecedor
        WHERE gc.idEquipamento = :idEquipamento
          AND gc.ativo = true
        ORDER BY gc.dataFim DESC
        LIMIT 1
    ");

    $stmtGarantia->bindParam(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtGarantia->execute();

    $garantiaContrato = $stmtGarantia->fetch();
} catch (PDOException $e) {
    $erro = 'Erro ao obter os dados do equipamento.';
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
                    <i class="fas fa-eye"></i> Consultar Equipamento
                </strong>
            </h2>

            <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <hr>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger text-center">
                <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <?php if ($equipamento): ?>

            <ul class="nav nav-tabs mb-4" id="separadoresDetalhesEquipamento" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral"
                        type="button" role="tab">
                        Dados gerais
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fornecedores-tab" data-bs-toggle="tab" data-bs-target="#fornecedores"
                        type="button" role="tab">
                        Fornecedores associados
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao"
                        type="button" role="tab">
                        Localização
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documentacao-tab" data-bs-toggle="tab" data-bs-target="#documentacao"
                        type="button" role="tab">
                        Documentação associada
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="garantias-tab" data-bs-toggle="tab" data-bs-target="#garantias"
                        type="button" role="tab">
                        Garantias e contratos
                    </button>
                </li>

            </ul>

            <div class="tab-content" id="conteudoSeparadoresDetalhesEquipamento">

                <!-- Separador: Dados gerais -->
                <div class="tab-pane fade show active" id="geral" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-laptop-medical"></i> Dados gerais do equipamento
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Código interno</th>
                                        <td><?= e($equipamento->codigoInterno) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Designação</th>
                                        <td><?= e($equipamento->designacao) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Número de série</th>
                                        <td><?= e($equipamento->numeroSerie) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Categoria / Grupo</th>
                                        <td><?= e($equipamento->categoria) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Marca</th>
                                        <td><?= e($equipamento->marca ?: '-') ?></td>
                                    </tr>

                                    <tr>
                                        <th>Modelo</th>
                                        <td><?= e($equipamento->modelo ?: '-') ?></td>
                                    </tr>

                                    <tr>
                                        <th>Fabricante</th>
                                        <td><?= e($equipamento->fabricante ?: '-') ?></td>
                                    </tr>

                                    <tr>
                                        <th>Estado atual</th>
                                        <td class="<?= e(classe_estado_detalhes_equipamento($equipamento->estado)) ?>">
                                            <?= e($equipamento->estado) ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Criticidade</th>
                                        <td class="<?= e(classe_criticidade_detalhes_equipamento($equipamento->criticidade)) ?>">
                                            <?= e($equipamento->criticidade) ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Ano de fabrico</th>
                                        <td><?= e($equipamento->anoFabrico ?: '-') ?></td>
                                    </tr>

                                    <tr>
                                        <th>Data de aquisição</th>
                                        <td><?= e(formato_data_detalhes_equipamento($equipamento->dataAquisicao)) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Custo de aquisição</th>
                                        <td><?= e(number_format((float) $equipamento->custoAquisicao, 2, ',', '.')) ?> €</td>
                                    </tr>

                                    <tr>
                                        <th>Tipo de entrada</th>
                                        <td><?= e($equipamento->tipoEntrada) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Observações / utilização</th>
                                        <td><?= e($equipamento->observacoes ?: '-') ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador: Fornecedores associados -->
                <div class="tab-pane fade" id="fornecedores" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-truck-medical"></i> Fornecedores associados
                            </h3>

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario tabela-paginada-dashboard"
                                data-linhas-pagina="5">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Tipo de associação</th>
                                        <th>Contacto telefónico</th>
                                        <th>Email</th>
                                        <th>Website</th>
                                        <th>Pessoas de contacto</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($fornecedoresAssociados)): ?>

                                        <?php foreach ($fornecedoresAssociados as $fornecedor): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= e($fornecedor->designacao) ?></strong>
                                                </td>

                                                <td><?= e($fornecedor->tipoRelacao) ?></td>

                                                <td><?= e($fornecedor->telefone ?: '-') ?></td>

                                                <td><?= e($fornecedor->email ?: '-') ?></td>

                                                <td><?= e($fornecedor->website ?: '-') ?></td>

                                                <td class="text-start">
                                                    <?php if (!empty($fornecedor->pessoaContacto)): ?>
                                                        <?= e($fornecedor->pessoaContacto) ?> - <?= e($fornecedor->telefonePessoaContacto ?: '-') ?><br>
                                                    <?php endif; ?>

                                                    <?php if (!empty($fornecedor->pessoaContacto2)): ?>
                                                        <?= e($fornecedor->pessoaContacto2) ?> - <?= e($fornecedor->telefonePessoaContacto2 ?: '-') ?>
                                                    <?php endif; ?>

                                                    <?php if (empty($fornecedor->pessoaContacto) && empty($fornecedor->pessoaContacto2)): ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Não existem fornecedores ativos associados a este equipamento.
                                            </td>
                                        </tr>

                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador: Localização -->
                <div class="tab-pane fade" id="localizacao" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-location-dot"></i> Localização
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Localização atual</th>
                                        <td>
                                            <?= e($equipamento->edificio) ?> -
                                            Piso <?= e($equipamento->piso) ?> -
                                            <?= e($equipamento->servico) ?> -
                                            <?= e($equipamento->sala) ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Categoria da localização</th>
                                        <td><?= e($equipamento->categoriaLocalizacao ?: '-') ?></td>
                                    </tr>

                                    <tr>
                                        <th>Observações da localização</th>
                                        <td><?= e($equipamento->observacoesLocalizacao ?: '-') ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-clock-rotate-left"></i> Histórico de movimentações
                            </h3>

                            <div class="table-responsive tabela-lista-container">
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario tabela-paginada-dashboard"
                                    data-linhas-pagina="5">
                                    <thead>
                                        <tr>
                                            <th>Localização</th>
                                            <th>Data</th>
                                            <th>Responsável</th>
                                            <th>Motivo / observação</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (!empty($movimentacoesLocalizacao)): ?>

                                            <?php foreach ($movimentacoesLocalizacao as $movimentacao): ?>
                                                <tr>
                                                    <td>
                                                        <?= e($movimentacao->edificio) ?> -
                                                        Piso <?= e($movimentacao->piso) ?> -
                                                        <?= e($movimentacao->servico) ?> -
                                                        <?= e($movimentacao->sala) ?>
                                                    </td>

                                                    <td><?= e(formato_data_detalhes_equipamento($movimentacao->dataLocalizacao)) ?></td>

                                                    <td><?= e($movimentacao->responsavel ?: '-') ?></td>

                                                    <td><?= e($movimentacao->motivo ?: '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                        <?php else: ?>

                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    Não existem movimentações registadas para este equipamento.
                                                </td>
                                            </tr>

                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Documentação associada -->
                <div class="tab-pane fade" id="documentacao" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-file-medical"></i> Documentação associada
                            </h3>

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario tabela-paginada-dashboard"
                                data-linhas-pagina="5">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nome do documento</th>
                                        <th>Data</th>
                                        <th>Validade / Expiração</th>
                                        <th>Fornecedor</th>
                                        <th>Ficheiro</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($documentosAssociados)): ?>

                                        <?php foreach ($documentosAssociados as $documento): ?>
                                            <tr>
                                                <td><?= e($documento->tipoDocumento) ?></td>

                                                <td><?= e($documento->nomeDocumento) ?></td>

                                                <td><?= e(formato_data_detalhes_equipamento($documento->dataDocumento)) ?></td>

                                                <td><?= e($documento->dataValidade ? formato_data_detalhes_equipamento($documento->dataValidade) : 'Sem validade definida') ?></td>

                                                <td><?= e($documento->fornecedor) ?></td>

                                                <td><?= e($documento->nomeFicheiro ?: '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Não existem documentos ativos associados a este equipamento.
                                            </td>
                                        </tr>

                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador: Garantias e contratos -->
                <div class="tab-pane fade" id="garantias" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-file-contract"></i> Garantias e contratos
                            </h3>

                            <?php if ($garantiaContrato): ?>

                                <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                    <tbody>
                                        <tr>
                                            <th>Tipo</th>
                                            <td><?= e($garantiaContrato->tipo) ?></td>
                                        </tr>

                                        <tr>
                                            <th>Número da garantia/contrato</th>
                                            <td><?= e($garantiaContrato->numeroContrato) ?></td>
                                        </tr>

                                        <tr>
                                            <th>Data de início</th>
                                            <td><?= e(formato_data_detalhes_equipamento($garantiaContrato->dataInicio)) ?></td>
                                        </tr>

                                        <tr>
                                            <th>Data de fim</th>
                                            <td><?= e(formato_data_detalhes_equipamento($garantiaContrato->dataFim)) ?></td>
                                        </tr>

                                        <tr>
                                            <th>Entidade responsável</th>
                                            <td><?= e($garantiaContrato->fornecedorResponsavel) ?></td>
                                        </tr>

                                        <tr>
                                            <th>Periodicidade</th>
                                            <td><?= e($garantiaContrato->periodicidade ?: '-') ?></td>
                                        </tr>

                                        <tr>
                                            <th>Observações</th>
                                            <td><?= e($garantiaContrato->observacoes ?: '-') ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                            <?php else: ?>

                                <div class="alert alert-info text-center mb-0">
                                    Não existe garantia ou contrato ativo associado a este equipamento.
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>

                </div>

            </div>

        <?php endif; ?>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>