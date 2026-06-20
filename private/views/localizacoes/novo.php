<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico'])) {
    header('Location: lista.php');
    exit;
}

$page_title = APP_NAME . ' - Nova Localização';
$body_class = 'pagina-novo-equipamento';

$erros = [];
$erroSistema = '';
$sucesso = '';

$categoria = '';
$edificio = '';
$piso = '';
$servico = '';
$sala = '';
$observacoes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
            $ligacao = db_connect();

            $stmtDuplicado = $ligacao->prepare("
                SELECT COUNT(*) AS total
                FROM Localizacao
                WHERE ativo = true
                  AND categoria = :categoria
                  AND edificio = :edificio
                  AND piso = :piso
                  AND servico = :servico
                  AND sala = :sala
            ");

            $stmtDuplicado->bindValue(':categoria', $categoria, PDO::PARAM_STR);
            $stmtDuplicado->bindValue(':edificio', $edificio, PDO::PARAM_STR);
            $stmtDuplicado->bindValue(':piso', $piso, PDO::PARAM_STR);
            $stmtDuplicado->bindValue(':servico', $servico, PDO::PARAM_STR);
            $stmtDuplicado->bindValue(':sala', $sala, PDO::PARAM_STR);
            $stmtDuplicado->execute();

            $existe = (int) $stmtDuplicado->fetch()->total;

            if ($existe > 0) {
                $erros[] = 'Já existe uma localização igual registada.';
            } else {
                $stmt = $ligacao->prepare("
                    INSERT INTO Localizacao
                        (categoria, edificio, piso, servico, sala, observacoes, ativo)
                    VALUES
                        (:categoria, :edificio, :piso, :servico, :sala, :observacoes, true)
                ");

                $stmt->bindValue(':categoria', $categoria, PDO::PARAM_STR);
                $stmt->bindValue(':edificio', $edificio, PDO::PARAM_STR);
                $stmt->bindValue(':piso', $piso, PDO::PARAM_STR);
                $stmt->bindValue(':servico', $servico, PDO::PARAM_STR);
                $stmt->bindValue(':sala', $sala, PDO::PARAM_STR);

                if ($observacoes === '') {
                    $stmt->bindValue(':observacoes', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':observacoes', $observacoes, PDO::PARAM_STR);
                }

                $stmt->execute();

                $sucesso = 'Localização registada com sucesso.';

                $categoria = '';
                $edificio = '';
                $piso = '';
                $servico = '';
                $sala = '';
                $observacoes = '';
            }
        } catch (PDOException $e) {
            $erroSistema = 'Erro ao guardar a localização.';
        }
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
                    <i class="fas fa-plus"></i> Inserir Nova Localização
                </strong>
            </h2>

            <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <hr>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success text-center">
                <?= e($sucesso) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erroSistema)): ?>
            <div class="alert alert-danger text-center">
                <?= e($erroSistema) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <strong>Foram encontrados os seguintes erros:</strong>

                <ul class="mb-0 mt-2">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="#" method="post" class="formulario-equipamento" novalidate>

            <div class="card mb-4">
                <div class="card-body">

                    <h3>
                        <i class="fas fa-location-dot"></i> Dados da localização
                    </h3>

                    <div class="row mb-3">

                        <div class="col-12 col-md-6">
                            <label for="categoria" class="form-label">Categoria</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="categoria"
                                name="categoria"
                                placeholder="Ex.: Área clínica crítica, Área cirúrgica, Área técnica"
                                value="<?= e($categoria) ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="edificio" class="form-label">Edifício</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="edificio"
                                name="edificio" placeholder="Ex.: Hospital Central"
                                value="<?= e($edificio) ?>">
                        </div>

                    </div>

                    <div class="row mb-3">

                        <div class="col-12 col-md-4">
                            <label for="piso" class="form-label">Piso</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="piso"
                                name="piso" placeholder="Ex.: 2"
                                value="<?= e($piso) ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="servico" class="form-label">Serviço / Departamento</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="servico"
                                name="servico" placeholder="Ex.: Unidade de Cuidados Intensivos"
                                value="<?= e($servico) ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="sala" class="form-label">Sala / Gabinete</label>
                            <input type="text" class="form-control campo-obrigatorio-localizacao" id="sala"
                                name="sala" placeholder="Ex.: Sala 1, Gabinete 4 ou Reanimação"
                                value="<?= e($sala) ?>">
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
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="4"
                            placeholder="Observações adicionais sobre esta localização."><?= e($observacoes) ?></textarea>
                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    Guardar localização
                </button>
            </div>

        </form>

        <p id="mensagem-formulario"></p>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>