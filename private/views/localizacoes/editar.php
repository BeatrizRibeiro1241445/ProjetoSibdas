<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'gestor_hospitalar'])) {
    header('Location: lista.php');
    exit;
}

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$idLocalizacaoEncrypted = $_GET['id_localizacao'] ?? null;
$idLocalizacao = aes_decrypt($idLocalizacaoEncrypted);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: lista.php');
    exit;
}

$idLocalizacao = (int) $idLocalizacao;

$erro = '';
$sucesso = '';
$erros = [];
$localizacao = null;

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
} catch (PDOException $err) {
    $erro = 'Erro ao obter os dados da localização.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $localizacao) {

    $categoria = trim($_POST['categoria'] ?? '');
    $edificio = trim($_POST['edificio'] ?? '');
    $piso = trim($_POST['piso'] ?? '');
    $servico = trim($_POST['servico'] ?? '');
    $sala = trim($_POST['sala'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    $categoria = preg_replace('/\s+/', ' ', $categoria);
    $edificio = preg_replace('/\s+/', ' ', $edificio);
    $piso = preg_replace('/\s+/', ' ', $piso);
    $servico = preg_replace('/\s+/', ' ', $servico);
    $sala = preg_replace('/\s+/', ' ', $sala);

    $padraoTexto = '/^[\p{L}0-9\s\/ºª.,-]+$/u';
    $padraoPiso = '/^-?[0-9]{1,2}$/';
    $padraoSala = '/^[\p{L}0-9\s\/ºª.,-]+$/u';

    if ($categoria === '') {
        $erros[] = 'A categoria é obrigatória.';
    } elseif (mb_strlen($categoria) > 100) {
        $erros[] = 'A categoria não pode ter mais de 100 caracteres.';
    } elseif (!preg_match($padraoTexto, $categoria)) {
        $erros[] = 'A categoria contém caracteres inválidos.';
    }

    if ($edificio === '') {
        $erros[] = 'O edifício é obrigatório.';
    } elseif (mb_strlen($edificio) > 100) {
        $erros[] = 'O edifício não pode ter mais de 100 caracteres.';
    } elseif (!preg_match($padraoTexto, $edificio)) {
        $erros[] = 'O edifício contém caracteres inválidos.';
    }

    if ($piso === '') {
        $erros[] = 'O piso é obrigatório.';
    } elseif (!preg_match($padraoPiso, $piso)) {
        $erros[] = 'O piso deve ser apenas um número, por exemplo 0, 1, 2 ou -1.';
    }

    if ($servico === '') {
        $erros[] = 'O serviço/departamento é obrigatório.';
    } elseif (mb_strlen($servico) > 120) {
        $erros[] = 'O serviço/departamento não pode ter mais de 120 caracteres.';
    } elseif (!preg_match($padraoTexto, $servico)) {
        $erros[] = 'O serviço/departamento contém caracteres inválidos.';
    }

    if ($sala === '') {
        $erros[] = 'A sala/gabinete é obrigatória.';
    } elseif (mb_strlen($sala) > 80) {
        $erros[] = 'A sala/gabinete não pode ter mais de 80 caracteres.';
    } elseif (!preg_match($padraoSala, $sala)) {
        $erros[] = 'A sala/gabinete contém caracteres inválidos.';
    }

    if ($observacoes !== '' && mb_strlen($observacoes) > 500) {
        $erros[] = 'As observações não podem ter mais de 500 caracteres.';
    }

    if (empty($erros)) {
        try {
            $stmtDuplicado = $ligacao->prepare("
                SELECT COUNT(*) AS total
                FROM Localizacao
                WHERE ativo = true
                  AND categoria = :categoria
                  AND edificio = :edificio
                  AND piso = :piso
                  AND servico = :servico
                  AND sala = :sala
                  AND idLocalizacao <> :idLocalizacao
            ");

            $stmtDuplicado->execute([
                ':categoria' => $categoria,
                ':edificio' => $edificio,
                ':piso' => $piso,
                ':servico' => $servico,
                ':sala' => $sala,
                ':idLocalizacao' => $idLocalizacao
            ]);

            $existe = (int) $stmtDuplicado->fetch()->total;

            if ($existe > 0) {
                $erros[] = 'Já existe outra localização igual registada.';
            } else {
                $stmtUpdate = $ligacao->prepare("
                    UPDATE Localizacao
                    SET
                        categoria = :categoria,
                        edificio = :edificio,
                        piso = :piso,
                        servico = :servico,
                        sala = :sala,
                        observacoes = :observacoes
                    WHERE idLocalizacao = :idLocalizacao
                ");

                $stmtUpdate->execute([
                    ':categoria' => $categoria,
                    ':edificio' => $edificio,
                    ':piso' => $piso,
                    ':servico' => $servico,
                    ':sala' => $sala,
                    ':observacoes' => $observacoes !== '' ? $observacoes : null,
                    ':idLocalizacao' => $idLocalizacao
                ]);

                header('Location: lista.php');
                exit;
            }
        } catch (PDOException $err) {
            $erro = 'Erro ao atualizar a localização.';
        }
    }

    $localizacao->categoria = $categoria;
    $localizacao->edificio = $edificio;
    $localizacao->piso = $piso;
    $localizacao->servico = $servico;
    $localizacao->sala = $sala;
    $localizacao->observacoes = $observacoes;
}

$page_title = APP_NAME . ' - Editar Localização';
$body_class = 'pagina-novo-equipamento';

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
                    <i class="fas fa-pen"></i> Editar Localização
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

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <strong>Foram encontrados os seguintes erros:</strong>

                <ul class="mb-0 mt-2">
                    <?php foreach ($erros as $erroValidacao): ?>
                        <li><?= e($erroValidacao) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="editar.php?id_localizacao=<?= e($idLocalizacaoEncrypted) ?>" method="post" class="formulario-equipamento" novalidate>

            <div class="card mb-4">
                <div class="card-body">

                    <h3>
                        <i class="fas fa-location-dot"></i> Dados da localização
                    </h3>

                    <div class="row mb-3">

                        <div class="col-12 col-md-6">
                            <label for="categoria" class="form-label">Categoria</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="categoria"
                                name="categoria" value="<?= e($localizacao->categoria) ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="edificio" class="form-label">Edifício</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="edificio"
                                name="edificio" value="<?= e($localizacao->edificio) ?>">
                        </div>

                    </div>

                    <div class="row mb-3">

                        <div class="col-12 col-md-4">
                            <label for="piso" class="form-label">Piso</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="piso"
                                name="piso" value="<?= e($localizacao->piso) ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="servico" class="form-label">Serviço / Departamento</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="servico"
                                name="servico" value="<?= e($localizacao->servico) ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="sala" class="form-label">Sala / Gabinete</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="sala"
                                name="sala" value="<?= e($localizacao->sala) ?>">
                        </div>

                    </div>

                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">

                    <h3>
                        <i class="fas fa-circle-info"></i> Observações
                    </h3>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes"
                            rows="4"><?= e($localizacao->observacoes ?? '') ?></textarea>
                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    Guardar alterações
                </button>

            </div>

        </form>

        <p id="mensagem-formulario"></p>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>