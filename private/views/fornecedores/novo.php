<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico', 'gestor_hospitalar'])) {
    header('Location: ' . BASE_URL . '/private/area_pessoal.php');
    exit;
}

$page_title = APP_NAME . ' - Novo Fornecedor';
$body_class = 'pagina-novo-equipamento';

$erros = [];
$erroSistema = '';
$sucesso = '';

$nif = '';
$email = '';
$designacao = '';
$telefone = '';
$morada = '';
$website = '';
$pessoaContacto = '';
$telefonePessoaContacto = '';
$pessoaContacto2 = '';
$telefonePessoaContacto2 = '';
$observacoes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nif = trim($_POST['nif'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $designacao = trim($_POST['designacao'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $morada = trim($_POST['morada'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $pessoaContacto = trim($_POST['pessoaContacto'] ?? '');
    $telefonePessoaContacto = trim($_POST['telefonePessoaContacto'] ?? '');
    $pessoaContacto2 = trim($_POST['pessoaContacto2'] ?? '');
    $telefonePessoaContacto2 = trim($_POST['telefonePessoaContacto2'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    $nif = preg_replace('/\s+/', '', $nif);
    $email = preg_replace('/\s+/', '', $email);
    $designacao = preg_replace('/\s+/', ' ', $designacao);
    $telefone = preg_replace('/\s+/', '', $telefone);
    $morada = preg_replace('/\s+/', ' ', $morada);
    $website = preg_replace('/\s+/', '', $website);
    $pessoaContacto = preg_replace('/\s+/', ' ', $pessoaContacto);
    $telefonePessoaContacto = preg_replace('/\s+/', '', $telefonePessoaContacto);
    $pessoaContacto2 = preg_replace('/\s+/', ' ', $pessoaContacto2);
    $telefonePessoaContacto2 = preg_replace('/\s+/', '', $telefonePessoaContacto2);

    $padraoTelefone = '/^\+[0-9]{8,15}$/';
    $padraoWebsite = '/^https?:\/\/[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(\/.*)?$/';

    if ($designacao === '') {
        $erros[] = 'O nome da empresa é obrigatório.';
    } elseif (mb_strlen($designacao) > 150) {
        $erros[] = 'O nome da empresa não pode ter mais de 150 caracteres.';
    }

    if ($nif === '') {
        $erros[] = 'O NIF é obrigatório.';
    } elseif (!preg_match('/^[0-9]{9}$/', $nif)) {
        $erros[] = 'O NIF deve ter exatamente 9 dígitos.';
    }

    if ($email === '') {
        $erros[] = 'O email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'O email introduzido não é válido.';
    } elseif (mb_strlen($email) > 120) {
        $erros[] = 'O email não pode ter mais de 120 caracteres.';
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
        if (mb_strlen($website) > 150) {
            $erros[] = 'O website não pode ter mais de 150 caracteres.';
        } elseif (!preg_match($padraoWebsite, $website)) {
            $erros[] = 'O website deve ter um formato válido, começando por http:// ou https://.';
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

    if (empty($erros)) {
        try {
            $ligacao = db_connect();

            $stmtDuplicado = $ligacao->prepare("
                SELECT COUNT(*) AS total
                FROM Fornecedor
                WHERE nif = :nif
                  OR email = :email
                  OR telefone = :telefone
            ");

            $stmtDuplicado->execute([
                ':nif' => $nif,
                ':email' => $email,
                ':telefone' => $telefone
            ]);
            $existe = (int) $stmtDuplicado->fetch()->total;

            if ($existe > 0) {
                $erros[] = 'Já existe um fornecedor com esse NIF, email ou contacto telefónico.';
            } else {
                $stmt = $ligacao->prepare("
                    INSERT INTO Fornecedor (
                        nif,
                        email,
                        designacao,
                        telefone,
                        morada,
                        website,
                        pessoaContacto,
                        telefonePessoaContacto,
                        pessoaContacto2,
                        telefonePessoaContacto2,
                        observacoes,
                        ativo
                    ) VALUES (
                        :nif,
                        :email,
                        :designacao,
                        :telefone,
                        :morada,
                        :website,
                        :pessoaContacto,
                        :telefonePessoaContacto,
                        :pessoaContacto2,
                        :telefonePessoaContacto2,
                        :observacoes,
                        true
                    )
                ");

                $stmt->execute([
                    ':nif' => $nif,
                    ':email' => $email,
                    ':designacao' => $designacao,
                    ':telefone' => $telefone !== '' ? $telefone : null,
                    ':morada' => $morada !== '' ? $morada : null,
                    ':website' => $website !== '' ? $website : null,
                    ':pessoaContacto' => $pessoaContacto !== '' ? $pessoaContacto : null,
                    ':telefonePessoaContacto' => $telefonePessoaContacto !== '' ? $telefonePessoaContacto : null,
                    ':pessoaContacto2' => $pessoaContacto2 !== '' ? $pessoaContacto2 : null,
                    ':telefonePessoaContacto2' => $telefonePessoaContacto2 !== '' ? $telefonePessoaContacto2 : null,
                    ':observacoes' => $observacoes !== '' ? $observacoes : null
                ]);

                $sucesso = 'Fornecedor registado com sucesso.';

                $nif = '';
                $email = '';
                $designacao = '';
                $telefone = '';
                $morada = '';
                $website = '';
                $pessoaContacto = '';
                $telefonePessoaContacto = '';
                $pessoaContacto2 = '';
                $telefonePessoaContacto2 = '';
                $observacoes = '';
            }
        } catch (PDOException $e) {
            $erroSistema = 'Erro ao guardar o fornecedor.';
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
                    <i class="fas fa-plus"></i> Registar Fornecedor
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

            <ul class="nav nav-tabs mb-4" id="separadoresFornecedor" role="tablist">

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

            <div class="tab-content" id="conteudoSeparadoresFornecedor">

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
                                    <input type="text" class="form-control" id="designacao" name="designacao"
                                        placeholder="Ex.: MedTech Portugal"
                                        value="<?= e($designacao) ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="nif" class="form-label">NIF</label>
                                    <input type="text" class="form-control" id="nif" name="nif"
                                        placeholder="Ex.: 509000000"
                                        value="<?= e($nif) ?>">
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
                                    <input type="text" class="form-control" id="telefone" name="telefone"
                                        placeholder="Ex.: +351220000000"
                                        value="<?= e($telefone) ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Ex.: geral@fornecedor.pt"
                                        value="<?= e($email) ?>">
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="morada" class="form-label">Morada</label>
                                <input type="text" class="form-control" id="morada" name="morada"
                                    placeholder="Ex.: Rua da Saúde, Porto"
                                    value="<?= e($morada) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="website" name="website"
                                    placeholder="Ex.: https://www.fornecedor.pt"
                                    value="<?= e($website) ?>">
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-primary"
                                    onclick="avancarFornecedorContactos()">
                                    Página seguinte
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
                                    <input type="text" class="form-control" id="pessoaContacto" name="pessoaContacto"
                                        placeholder="Ex.: Ana Silva"
                                        value="<?= e($pessoaContacto) ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="telefonePessoaContacto" class="form-label">
                                        Telefone da pessoa de contacto 1
                                    </label>
                                    <input type="text" class="form-control" id="telefonePessoaContacto" name="telefonePessoaContacto"
                                        placeholder="Ex.: +351910000000"
                                        value="<?= e($telefonePessoaContacto) ?>">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="pessoaContacto2" class="form-label">Pessoa de contacto 2</label>
                                    <input type="text" class="form-control" id="pessoaContacto2" name="pessoaContacto2"
                                        placeholder="Ex.: João Costa"
                                        value="<?= e($pessoaContacto2) ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="telefonePessoaContacto2" class="form-label">
                                        Telefone da pessoa de contacto 2
                                    </label>
                                    <input type="text" class="form-control" id="telefonePessoaContacto2" name="telefonePessoaContacto2"
                                        placeholder="Ex.: +351911000000"
                                        value="<?= e($telefonePessoaContacto2) ?>">
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
                                    placeholder="Observações adicionais sobre o fornecedor."><?= e($observacoes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">

                                <button type="button" class="btn btn-outline-secondary botao-anterior"
                                    onclick="voltarFornecedorGeral()">
                                    Página anterior
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Guardar fornecedor
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