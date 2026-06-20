<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$idFornecedorEncrypted = $_GET['id_fornecedor'] ?? null;
$idFornecedor = aes_decrypt($idFornecedorEncrypted);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit;
}

$idFornecedor = (int) $idFornecedor;

$page_title = APP_NAME . ' - Consultar Fornecedor';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$fornecedor = null;
$equipamentosAssociados = [];

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT *
        FROM Fornecedor
        WHERE idFornecedor = :idFornecedor
    ");

    $stmt->bindParam(':idFornecedor', $idFornecedor, PDO::PARAM_INT);
    $stmt->execute();

    $fornecedor = $stmt->fetch();

    if (!$fornecedor) {
        header('Location: lista.php');
        exit;
    }

    $stmtEquipamentos = $ligacao->prepare("
        SELECT
            e.codigoInterno,
            e.designacao,
            e.numeroSerie,
            ef.tipoRelacao,
            ef.observacoes
        FROM EquipamentoFornecedor ef
        INNER JOIN Equipamento e
            ON ef.idEquipamento = e.idEquipamento
        WHERE ef.idFornecedor = :idFornecedor
          AND e.ativo = true
        ORDER BY e.codigoInterno, ef.tipoRelacao
    ");

    $stmtEquipamentos->bindParam(':idFornecedor', $idFornecedor, PDO::PARAM_INT);
    $stmtEquipamentos->execute();

    $equipamentosAssociados = $stmtEquipamentos->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter os dados do fornecedor.';
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
                    <i class="fas fa-eye"></i> Consultar Fornecedor
                </strong>

                <?php if ($fornecedor && $fornecedor->ativo == 1): ?>
                    <span class="badge bg-success">Ativo</span>
                <?php elseif ($fornecedor): ?>
                    <span class="badge bg-secondary">Inativo</span>
                <?php endif; ?>
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

        <?php if ($fornecedor): ?>

            <ul class="nav nav-tabs mb-4" id="separadoresDetalhesFornecedor" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="detalhes-geral-tab" data-bs-toggle="tab"
                        data-bs-target="#detalhes-geral" type="button" role="tab">
                        Identificação e contactos
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="detalhes-contactos-tab" data-bs-toggle="tab"
                        data-bs-target="#detalhes-contactos" type="button" role="tab">
                        Pessoas de contacto e observações
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="detalhes-equipamentos-tab" data-bs-toggle="tab"
                        data-bs-target="#detalhes-equipamentos" type="button" role="tab">
                        Equipamentos associados
                    </button>
                </li>

            </ul>

            <div class="tab-content" id="conteudoSeparadoresDetalhesFornecedor">

                <!-- Separador 1 -->
                <div class="tab-pane fade show active" id="detalhes-geral" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-building"></i> Identificação do fornecedor
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Nome da empresa</th>
                                        <td><?= e($fornecedor->designacao) ?></td>
                                    </tr>

                                    <tr>
                                        <th>NIF</th>
                                        <td><?= e($fornecedor->nif) ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-phone"></i> Contactos gerais
                            </h3>

                            <table class="table table-bordered table-hover align-middle tabela-detalhes">
                                <tbody>
                                    <tr>
                                        <th>Contacto telefónico</th>
                                        <td><?= e($fornecedor->telefone ?: '-') ?></td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td><?= e($fornecedor->email) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Morada</th>
                                        <td><?= e($fornecedor->morada ?: '-') ?></td>
                                    </tr>

                                    <tr>
                                        <th>Website</th>
                                        <td><?= e($fornecedor->website ?: '-') ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

                <!-- Separador 2 -->
                <div class="tab-pane fade" id="detalhes-contactos" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-users"></i> Pessoas de contacto
                            </h3>

                            <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
                                <thead>
                                    <tr>
                                        <th>Pessoa de contacto</th>
                                        <th>Telefone</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($fornecedor->pessoaContacto) || !empty($fornecedor->pessoaContacto2)): ?>

                                        <?php if (!empty($fornecedor->pessoaContacto)): ?>
                                            <tr>
                                                <td><?= e($fornecedor->pessoaContacto) ?></td>
                                                <td><?= e($fornecedor->telefonePessoaContacto ?: '-') ?></td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($fornecedor->pessoaContacto2)): ?>
                                            <tr>
                                                <td><?= e($fornecedor->pessoaContacto2) ?></td>
                                                <td><?= e($fornecedor->telefonePessoaContacto2 ?: '-') ?></td>
                                            </tr>
                                        <?php endif; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="2" class="text-center">
                                                Não existem pessoas de contacto registadas.
                                            </td>
                                        </tr>

                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-circle-info"></i> Observações
                            </h3>

                            <p class="mb-0">
                                <?= e($fornecedor->observacoes ?: '-') ?>
                            </p>

                        </div>
                    </div>

                </div>

                <!-- Separador 3 -->
                <div class="tab-pane fade" id="detalhes-equipamentos" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-laptop-medical"></i> Equipamentos associados
                            </h3>

                            <div class="table-responsive tabela-lista-container">
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista">

                                    <thead>
                                        <tr>
                                            <th>Código interno</th>
                                            <th>Equipamento</th>
                                            <th>Número de série</th>
                                            <th>Tipo de associação</th>
                                            <th>Observações da associação</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (!empty($equipamentosAssociados)): ?>

                                            <?php foreach ($equipamentosAssociados as $equipamento): ?>
                                                <tr>
                                                    <td><?= e($equipamento->codigoInterno) ?></td>

                                                    <td>
                                                        <strong><?= e($equipamento->designacao) ?></strong>
                                                    </td>

                                                    <td><?= e($equipamento->numeroSerie) ?></td>

                                                    <td><?= e($equipamento->tipoRelacao) ?></td>

                                                    <td><?= e($equipamento->observacoes ?: '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                        <?php else: ?>

                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    Não existem equipamentos ativos associados a este fornecedor.
                                                </td>
                                            </tr>

                                        <?php endif; ?>
                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        <?php endif; ?>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>