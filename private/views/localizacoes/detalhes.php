<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$idLocalizacaoEncrypted = $_GET['id_localizacao'] ?? null;
$idLocalizacao = aes_decrypt($idLocalizacaoEncrypted);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: lista.php');
    exit;
}

$idLocalizacao = (int) $idLocalizacao;

$page_title = APP_NAME . ' - Consultar Localização';
$body_class = 'pagina-novo-equipamento';

$erro = '';
$localizacao = null;
$equipamentos = [];

function classe_estado_detalhes_localizacao($estado)
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

function classe_criticidade_detalhes_localizacao($criticidade)
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

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT *
        FROM Localizacao
        WHERE idLocalizacao = :idLocalizacao
          AND ativo = true
    ");

    $stmt->bindParam(':idLocalizacao', $idLocalizacao, PDO::PARAM_INT);
    $stmt->execute();

    $localizacao = $stmt->fetch();

    if (!$localizacao) {
        header('Location: lista.php');
        exit;
    }

    $stmtEquipamentos = $ligacao->prepare("
        SELECT
            e.codigoInterno,
            e.designacao,
            e.numeroSerie,
            ee.descricao AS estado,
            cr.descricao AS criticidade
        FROM Equipamento e
        INNER JOIN EstadoEquipamento ee
            ON e.idEstadoEquipamento = ee.idEstadoEquipamento
        INNER JOIN CriticidadeEquipamento cr
            ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
        WHERE e.idLocalizacao = :idLocalizacao
          AND e.ativo = true
        ORDER BY e.codigoInterno
    ");

    $stmtEquipamentos->bindParam(':idLocalizacao', $idLocalizacao, PDO::PARAM_INT);
    $stmtEquipamentos->execute();

    $equipamentos = $stmtEquipamentos->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao obter os dados da localização.';
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
                    <i class="fas fa-eye"></i> Consultar Localização
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

        <?php if ($localizacao): ?>

            <div class="card mb-4">
                <div class="card-body">

                    <h3>
                        <i class="fas fa-location-dot"></i> Dados da localização
                    </h3>

                    <table class="table table-bordered table-hover align-middle tabela-detalhes">
                        <tbody>
                            <tr>
                                <th>Categoria</th>
                                <td><?= e($localizacao->categoria) ?></td>
                            </tr>

                            <tr>
                                <th>Edifício</th>
                                <td><?= e($localizacao->edificio) ?></td>
                            </tr>

                            <tr>
                                <th>Piso</th>
                                <td><?= e($localizacao->piso) ?></td>
                            </tr>

                            <tr>
                                <th>Serviço / Departamento</th>
                                <td><?= e($localizacao->servico) ?></td>
                            </tr>

                            <tr>
                                <th>Sala / Gabinete</th>
                                <td><?= e($localizacao->sala) ?></td>
                            </tr>

                            <tr>
                                <th>Observações</th>
                                <td><?= e($localizacao->observacoes ?: '-') ?></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">

                    <h3>
                        <i class="fas fa-laptop-medical"></i> Equipamentos presentes nesta localização
                    </h3>

                    <div class="table-responsive tabela-lista-container">
                        <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-paginada-dashboard"
                            data-linhas-pagina="5">

                            <thead>
                                <tr>
                                    <th>Código interno</th>
                                    <th>Equipamento</th>
                                    <th>Número de série</th>
                                    <th>Estado</th>
                                    <th>Criticidade</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($equipamentos)): ?>

                                    <?php foreach ($equipamentos as $equipamento): ?>
                                        <tr>
                                            <td><?= e($equipamento->codigoInterno) ?></td>

                                            <td>
                                                <strong><?= e($equipamento->designacao) ?></strong>
                                            </td>

                                            <td><?= e($equipamento->numeroSerie) ?></td>

                                            <td class="<?= e(classe_estado_detalhes_localizacao($equipamento->estado)) ?>">
                                                <?= e($equipamento->estado) ?>
                                            </td>

                                            <td class="<?= e(classe_criticidade_detalhes_localizacao($equipamento->criticidade)) ?>">
                                                <?= e($equipamento->criticidade) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Não existem equipamentos ativos nesta localização.
                                        </td>
                                    </tr>

                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        <?php endif; ?>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>