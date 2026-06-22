<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico', 'gestor_hospitalar'])) {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$idFornecedorEncrypted = $_GET['id_fornecedor'] ?? null;
$idFornecedor = aes_decrypt($idFornecedorEncrypted);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit;
}

$idFornecedor = (int) $idFornecedor;

$erro = '';
$erros = [];
$fornecedor = null;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT *
        FROM Fornecedor
        WHERE idFornecedor = :idFornecedor
          AND ativo = true
    ");

    $stmt->bindParam(':idFornecedor', $idFornecedor, PDO::PARAM_INT);
    $stmt->execute();

    $fornecedor = $stmt->fetch();

    if (!$fornecedor) {
        header('Location: lista.php');
        exit;
    }
} catch (PDOException $err) {
    $erro = 'Erro ao obter os dados do fornecedor.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fornecedor) {

    $designacao = trim($_POST['designacao'] ?? '');
    $nif = trim($_POST['nif'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $morada = trim($_POST['morada'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $pessoaContacto = trim($_POST['pessoaContacto'] ?? '');
    $telefonePessoaContacto = trim($_POST['telefonePessoaContacto'] ?? '');
    $pessoaContacto2 = trim($_POST['pessoaContacto2'] ?? '');
    $telefonePessoaContacto2 = trim($_POST['telefonePessoaContacto2'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    $designacao = preg_replace('/\s+/', ' ', $designacao);
    $nif = preg_replace('/\s+/', '', $nif);
    $email = preg_replace('/\s+/', '', $email);
    $telefone = preg_replace('/\s+/', '', $telefone);
    $morada = preg_replace('/\s+/', ' ', $morada);
    $website = preg_replace('/\s+/', '', $website);
    $pessoaContacto = preg_replace('/\s+/', ' ', $pessoaContacto);
    $telefonePessoaContacto = preg_replace('/\s+/', '', $telefonePessoaContacto);
    $pessoaContacto2 = preg_replace('/\s+/', ' ', $pessoaContacto2);
    $telefonePessoaContacto2 = preg_replace('/\s+/', '', $telefonePessoaContacto2);

    $padraoTelefone = '/^\+[0-9]{8,15}$/';

    if ($designacao === '') {
        $erros[] = 'O nome da empresa é obrigatório.';
    } elseif (mb_strlen($designacao) > 150) {
        $erros[] = 'O nome da empresa não pode ter mais de 150 caracteres.';
    }

    if ($nif === '') {
        $erros[] = 'O NIF é obrigatório.';
    } elseif (!preg_match('/^[0-9]{9}$/', $nif)) {
        $erros[] = 'O NIF deve conter exatamente 9 números.';
    }

    if ($email === '') {
        $erros[] = 'O email é obrigatório.';
    } elseif (mb_strlen($email) > 120) {
        $erros[] = 'O email não pode ter mais de 120 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'O email deve estar num formato válido.';
    }

    if ($telefone === '') {
        $erros[] = 'O contacto telefónico é obrigatório.';
    } elseif (!preg_match($padraoTelefone, $telefone)) {
        $erros[] = 'O contacto telefónico deve começar por +, incluir o indicativo do país e conter apenas números.';
    }

    if ($morada !== '' && mb_strlen($morada) > 200) {
        $erros[] = 'A morada não pode ter mais de 200 caracteres.';
    }

    if ($website !== '') {

        $websiteValidacao = $website;

        if (!preg_match('/^https?:\/\//i', $websiteValidacao)) {
            $websiteValidacao = 'https://' . $websiteValidacao;
        }

        if (mb_strlen($websiteValidacao) > 150) {
            $erros[] = 'O website não pode ter mais de 150 caracteres.';
        } elseif (!filter_var($websiteValidacao, FILTER_VALIDATE_URL)) {
            $erros[] = 'O website deve estar num formato válido.';
        } else {
            $website = $websiteValidacao;
        }
    }
    
    if ($pessoaContacto === '') {
        $erros[] = 'A pessoa de contacto 1 é obrigatória.';
    } elseif (mb_strlen($pessoaContacto) > 120) {
        $erros[] = 'A pessoa de contacto 1 não pode ter mais de 120 caracteres.';
    }

    if ($telefonePessoaContacto === '') {
        $erros[] = 'O telefone da pessoa de contacto 1 é obrigatório.';
    } elseif (!preg_match($padraoTelefone, $telefonePessoaContacto)) {
        $erros[] = 'O telefone da pessoa de contacto 1 deve começar por +, incluir o indicativo do país e conter apenas números.';
    }

    if ($pessoaContacto2 !== '' && mb_strlen($pessoaContacto2) > 120) {
        $erros[] = 'A pessoa de contacto 2 não pode ter mais de 120 caracteres.';
    }

    if ($telefonePessoaContacto2 !== '' && !preg_match($padraoTelefone, $telefonePessoaContacto2)) {
        $erros[] = 'O telefone da pessoa de contacto 2 deve começar por +, incluir o indicativo do país e conter apenas números.';
    }

    if ($pessoaContacto2 !== '' && $telefonePessoaContacto2 === '') {
        $erros[] = 'Se preencher a pessoa de contacto 2, deve preencher também o respetivo telefone.';
    }

    if ($pessoaContacto2 === '' && $telefonePessoaContacto2 !== '') {
        $erros[] = 'Se preencher o telefone da pessoa de contacto 2, deve preencher também o nome da pessoa.';
    }

    if ($observacoes !== '' && mb_strlen($observacoes) > 500) {
        $erros[] = 'As observações não podem ter mais de 500 caracteres.';
    }

    if (empty($erros)) {
        try {
            $stmtDuplicado = $ligacao->prepare("
                SELECT COUNT(*) AS total
                FROM Fornecedor
                WHERE ativo = true
                  AND idFornecedor <> :idFornecedor
                  AND (
                        nif = :nif
                     OR email = :email
                     OR telefone = :telefone
                  )
            ");

            $stmtDuplicado->execute([
                ':idFornecedor' => $idFornecedor,
                ':nif' => $nif,
                ':email' => $email,
                ':telefone' => $telefone
            ]);

            $existe = (int) $stmtDuplicado->fetch()->total;

            if ($existe > 0) {
                $erros[] = 'Já existe outro fornecedor com esse NIF, email ou contacto telefónico.';
            } else {
                $stmtUpdate = $ligacao->prepare("
                    UPDATE Fornecedor
                    SET
                        nif = :nif,
                        email = :email,
                        designacao = :designacao,
                        telefone = :telefone,
                        morada = :morada,
                        website = :website,
                        pessoaContacto = :pessoaContacto,
                        telefonePessoaContacto = :telefonePessoaContacto,
                        pessoaContacto2 = :pessoaContacto2,
                        telefonePessoaContacto2 = :telefonePessoaContacto2,
                        observacoes = :observacoes
                    WHERE idFornecedor = :idFornecedor
                ");

                $stmtUpdate->execute([
                    ':nif' => $nif,
                    ':email' => $email,
                    ':designacao' => $designacao,
                    ':telefone' => $telefone,
                    ':morada' => $morada !== '' ? $morada : null,
                    ':website' => $website !== '' ? $website : null,
                    ':pessoaContacto' => $pessoaContacto,
                    ':telefonePessoaContacto' => $telefonePessoaContacto,
                    ':pessoaContacto2' => $pessoaContacto2 !== '' ? $pessoaContacto2 : null,
                    ':telefonePessoaContacto2' => $telefonePessoaContacto2 !== '' ? $telefonePessoaContacto2 : null,
                    ':observacoes' => $observacoes !== '' ? $observacoes : null,
                    ':idFornecedor' => $idFornecedor
                ]);

                header('Location: lista.php');
                exit;
            }
        } catch (PDOException $err) {
            $erro = 'Erro ao atualizar o fornecedor.';
        }
    }

    $fornecedor->designacao = $designacao;
    $fornecedor->nif = $nif;
    $fornecedor->email = $email;
    $fornecedor->telefone = $telefone;
    $fornecedor->morada = $morada;
    $fornecedor->website = $website;
    $fornecedor->pessoaContacto = $pessoaContacto;
    $fornecedor->telefonePessoaContacto = $telefonePessoaContacto;
    $fornecedor->pessoaContacto2 = $pessoaContacto2;
    $fornecedor->telefonePessoaContacto2 = $telefonePessoaContacto2;
    $fornecedor->observacoes = $observacoes;
}

$page_title = APP_NAME . ' - Editar Fornecedor';
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
                    <i class="fas fa-pen"></i> Editar Fornecedor
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

        <form action="editar.php?id_fornecedor=<?= e($idFornecedorEncrypted) ?>" method="post" class="formulario-equipamento" novalidate>

            <ul class="nav nav-tabs mb-4" id="separadoresFornecedorEditar" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="fornecedor-geral-tab" data-bs-toggle="tab"
                        data-bs-target="#fornecedor-geral" type="button" role="tab">
                        Identificação e contactos
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fornecedor-contactos-tab" data-bs-toggle="tab"
                        data-bs-target="#fornecedor-contactos" type="button" role="tab">
                        Pessoas de contacto e observações
                    </button>
                </li>

            </ul>

            <div class="tab-content" id="conteudoSeparadoresFornecedorEditar">

                <!-- Separador: Identificação e contactos -->
                <div class="tab-pane fade show active" id="fornecedor-geral" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-building"></i> Identificação do fornecedor
                            </h3>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="designacao" class="form-label">Nome da empresa</label>
                                    <input type="text" class="form-control campo-obrigatorio-fornecedor"
                                        id="designacao" name="designacao"
                                        value="<?= e($fornecedor->designacao) ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="nif" class="form-label">NIF</label>
                                    <input type="text" class="form-control campo-obrigatorio-fornecedor" id="nif"
                                        name="nif" value="<?= e($fornecedor->nif) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-phone"></i> Contactos gerais
                            </h3>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="telefone" class="form-label">Contacto telefónico</label>
                                    <input type="text" class="form-control campo-obrigatorio-fornecedor"
                                        id="telefone" name="telefone" value="<?= e($fornecedor->telefone) ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control campo-obrigatorio-fornecedor" id="email"
                                        name="email" value="<?= e($fornecedor->email) ?>">
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="morada" class="form-label">Morada</label>
                                <input type="text" class="form-control" id="morada"
                                    name="morada" value="<?= e($fornecedor->morada ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="website"
                                    name="website" value="<?= e($fornecedor->website ?? '') ?>">
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="reset" class="btn btn-outline-secondary botao-anterior">
                                    Repor
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Guardar informações
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Separador: Pessoas de contacto e observações -->
                <div class="tab-pane fade" id="fornecedor-contactos" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-users"></i> Pessoas de contacto
                            </h3>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="pessoaContacto" class="form-label">Pessoa de contacto 1</label>
                                    <input type="text" class="form-control campo-obrigatorio-fornecedor"
                                        id="pessoaContacto" name="pessoaContacto"
                                        value="<?= e($fornecedor->pessoaContacto ?? '') ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="telefonePessoaContacto" class="form-label">
                                        Telefone da pessoa de contacto 1
                                    </label>
                                    <input type="text" class="form-control campo-obrigatorio-fornecedor"
                                        id="telefonePessoaContacto" name="telefonePessoaContacto"
                                        value="<?= e($fornecedor->telefonePessoaContacto ?? '') ?>">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="pessoaContacto2" class="form-label">Pessoa de contacto 2</label>
                                    <input type="text" class="form-control" id="pessoaContacto2" name="pessoaContacto2"
                                        value="<?= e($fornecedor->pessoaContacto2 ?? '') ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="telefonePessoaContacto2" class="form-label">
                                        Telefone da pessoa de contacto 2
                                    </label>
                                    <input type="text" class="form-control" id="telefonePessoaContacto2"
                                        name="telefonePessoaContacto2"
                                        value="<?= e($fornecedor->telefonePessoaContacto2 ?? '') ?>">
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
                                    rows="4"><?= e($fornecedor->observacoes ?? '') ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="reset" class="btn btn-outline-secondary botao-anterior">
                                    Repor
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Guardar informações
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </form>

        <p id="mensagem-formulario"></p>

    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>