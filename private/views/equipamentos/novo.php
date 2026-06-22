<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico', 'gestor_hospitalar'])) {
    header('Location: lista.php');
    exit;
}

$page_title = APP_NAME . ' - Novo Equipamento';
$body_class = 'pagina-novo-equipamento';

$erros = [];
$erroSistema = '';
$sucesso = '';

$categorias = [];
$estados = [];
$criticidades = [];
$tiposEntrada = [];
$localizacoes = [];
$fornecedores = [];
$tiposDocumento = [];
$tecnicos = [];

$codigoInterno = '';
$numeroSerie = '';
$idCategoriaEquipamento = '';
$idEstadoEquipamento = '';
$idCriticidadeEquipamento = '';
$idTipoEntrada = '';
$designacao = '';
$marca = '';
$modelo = '';
$fabricante = '';
$dataAquisicao = '';
$anoFabrico = '';
$custoAquisicao = '';
$observacoes = '';

$fornecedoresAssociados = [];
$localizacoesAssociadas = [];
$documentosAdicionados = [];

$tipoGarantiaContrato = '';
$numeroContrato = '';
$dataInicioGarantia = '';
$dataFimGarantia = '';
$idFornecedorResponsavel = '';
$periodicidade = '';
$observacoesGarantia = '';

$tiposRelacaoPermitidos = [
    'Fabricante',
    'Fornecedor comercial',
    'Assistência técnica',
    'Consumíveis/acessórios'
];

$tiposGarantiaPermitidos = [
    'Garantia',
    'Contrato de manutenção',
    'Contrato de assistência técnica',
    'Contrato de calibração'
];

$periodicidadesPermitidas = [
    'Mensal',
    'Trimestral',
    'Semestral',
    'Anual',
    'Bienal',
    'Pontual'
];

function existe_id_lista($id, $lista, $campo)
{
    foreach ($lista as $item) {
        if ((string) $item->$campo === (string) $id) {
            return true;
        }
    }

    return false;
}

function texto_fornecedor($idFornecedor, $fornecedores)
{
    foreach ($fornecedores as $fornecedor) {
        if ((string) $fornecedor->idFornecedor === (string) $idFornecedor) {
            return $fornecedor->designacao . ' — NIF ' . $fornecedor->nif;
        }
    }

    return '';
}

function texto_localizacao($idLocalizacao, $localizacoes)
{
    foreach ($localizacoes as $localizacao) {
        if ((string) $localizacao->idLocalizacao === (string) $idLocalizacao) {
            return $localizacao->edificio . ' — Piso ' . $localizacao->piso . ' — ' . $localizacao->servico . ' — Sala ' . $localizacao->sala;
        }
    }

    return '';
}

function texto_tipo_documento($idTipoDocumento, $tiposDocumento)
{
    foreach ($tiposDocumento as $tipoDocumento) {
        if ((string) $tipoDocumento->idTipoDocumento === (string) $idTipoDocumento) {
            return $tipoDocumento->descricao;
        }
    }

    return '';
}

function data_valida($data)
{
    $objetoData = DateTime::createFromFormat('Y-m-d', $data);
    return $objetoData && $objetoData->format('Y-m-d') === $data;
}

function obter_ficheiro_documento($token)
{
    if ($token === '') {
        return null;
    }

    if (!isset($_FILES['documentosFicheiros'])) {
        return null;
    }

    if (!isset($_FILES['documentosFicheiros']['name'][$token])) {
        return null;
    }

    return [
        'name' => $_FILES['documentosFicheiros']['name'][$token],
        'type' => $_FILES['documentosFicheiros']['type'][$token],
        'tmp_name' => $_FILES['documentosFicheiros']['tmp_name'][$token],
        'error' => $_FILES['documentosFicheiros']['error'][$token],
        'size' => $_FILES['documentosFicheiros']['size'][$token]
    ];
}

function validar_pdf_documento($ficheiro, $nomeDocumento, &$erros)
{
    if (!$ficheiro || $ficheiro['error'] === UPLOAD_ERR_NO_FILE) {
        $erros[] = 'O documento "' . $nomeDocumento . '" deve ter um ficheiro PDF associado.';
        return;
    }

    if ($ficheiro['error'] !== UPLOAD_ERR_OK) {
        $erros[] = 'Não foi possível receber o ficheiro do documento "' . $nomeDocumento . '".';
        return;
    }

    if ($ficheiro['size'] > 5 * 1024 * 1024) {
        $erros[] = 'O ficheiro do documento "' . $nomeDocumento . '" não pode ter mais de 5 MB.';
    }

    $extensao = strtolower(pathinfo($ficheiro['name'], PATHINFO_EXTENSION));

    if ($extensao !== 'pdf') {
        $erros[] = 'O ficheiro do documento "' . $nomeDocumento . '" deve estar em formato PDF.';
    }

    if (function_exists('finfo_open') && is_uploaded_file($ficheiro['tmp_name'])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $ficheiro['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['application/pdf', 'application/x-pdf'], true)) {
            $erros[] = 'O ficheiro do documento "' . $nomeDocumento . '" deve ser um PDF válido.';
        }
    }
}

function guardar_pdf_documento($ficheiro, $idEquipamento, $indice)
{
    $pastaDocumentos = __DIR__ . '/../../../assets/uploads/documentos/';

    if (!is_dir($pastaDocumentos)) {
        mkdir($pastaDocumentos, 0775, true);
    }

    $nomeOriginal = basename($ficheiro['name']);
    $nomeOriginal = mb_substr($nomeOriginal, 0, 150);

    if ($nomeOriginal === '') {
        $nomeOriginal = 'documento.pdf';
    }

    $nomeFicheiro = 'equipamento_' . $idEquipamento . '_' . date('YmdHis') . '_' . $indice . '_' . uniqid() . '.pdf';
    $caminhoCompleto = $pastaDocumentos . $nomeFicheiro;

    if (!move_uploaded_file($ficheiro['tmp_name'], $caminhoCompleto)) {
        throw new Exception('Erro ao guardar o ficheiro PDF.');
    }

    return [
        'nomeFicheiro' => $nomeOriginal,
        'caminhoFicheiro' => 'assets/uploads/documentos/' . $nomeFicheiro
    ];
}


try {
    $ligacao = db_connect();

    $categorias = $ligacao->query("
        SELECT idCategoriaEquipamento, descricao
        FROM CategoriaEquipamento
        ORDER BY descricao
    ")->fetchAll();

    $estados = $ligacao->query("
        SELECT idEstadoEquipamento, descricao
        FROM EstadoEquipamento
        WHERE descricao <> 'Abatido'
        ORDER BY descricao
    ")->fetchAll();

    $criticidades = $ligacao->query("
        SELECT idCriticidadeEquipamento, descricao
        FROM CriticidadeEquipamento
        ORDER BY idCriticidadeEquipamento
    ")->fetchAll();

    $tiposEntrada = $ligacao->query("
        SELECT idTipoEntrada, descricao
        FROM TipoEntrada
        ORDER BY descricao
    ")->fetchAll();

    $localizacoes = $ligacao->query("
        SELECT idLocalizacao, categoria, edificio, piso, servico, sala
        FROM Localizacao
        WHERE ativo = true
        ORDER BY edificio, piso, servico, sala
    ")->fetchAll();

    $fornecedores = $ligacao->query("
        SELECT idFornecedor, designacao, nif
        FROM Fornecedor
        WHERE ativo = true
        ORDER BY designacao
    ")->fetchAll();

    $tiposDocumento = $ligacao->query("
        SELECT idTipoDocumento, descricao
        FROM TipoDocumento
        ORDER BY descricao
    ")->fetchAll();

    $tecnicos = $ligacao->query("
        SELECT idUtilizador, nome
        FROM Utilizador
        WHERE ativo = true
          AND LOWER(perfil) = 'tecnico'
        ORDER BY nome
    ")->fetchAll();
} catch (Exception $e) {
    $erroSistema = 'Erro ao carregar os dados necessários para o formulário.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigoInterno = trim($_POST['codigoInterno'] ?? '');
    $numeroSerie = trim($_POST['numeroSerie'] ?? '');
    $idCategoriaEquipamento = trim($_POST['idCategoriaEquipamento'] ?? '');
    $idEstadoEquipamento = trim($_POST['idEstadoEquipamento'] ?? '');
    $idCriticidadeEquipamento = trim($_POST['idCriticidadeEquipamento'] ?? '');
    $idTipoEntrada = trim($_POST['idTipoEntrada'] ?? '');

    $designacao = trim($_POST['designacao'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $fabricante = trim($_POST['fabricante'] ?? '');
    $dataAquisicao = trim($_POST['dataAquisicao'] ?? '');
    $anoFabrico = trim($_POST['anoFabrico'] ?? '');
    $custoAquisicao = trim($_POST['custoAquisicao'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    $fornecedoresAssociados = $_POST['fornecedoresAssociados'] ?? [];
    $localizacoesAssociadas = $_POST['localizacoesAssociadas'] ?? [];
    $documentosAdicionados = $_POST['documentosAdicionados'] ?? [];

    $tipoGarantiaContrato = trim($_POST['tipoGarantiaContrato'] ?? '');
    $numeroContrato = trim($_POST['numeroContrato'] ?? '');
    $dataInicioGarantia = trim($_POST['dataInicioGarantia'] ?? '');
    $dataFimGarantia = trim($_POST['dataFimGarantia'] ?? '');
    $idFornecedorResponsavel = trim($_POST['idFornecedorResponsavel'] ?? '');
    $periodicidade = trim($_POST['periodicidade'] ?? '');
    $observacoesGarantia = trim($_POST['observacoesGarantia'] ?? '');

    $codigoInterno = preg_replace('/\s+/', '', $codigoInterno);
    $numeroSerie = preg_replace('/\s+/', '', $numeroSerie);
    $designacao = preg_replace('/\s+/', ' ', $designacao);
    $marca = preg_replace('/\s+/', ' ', $marca);
    $modelo = preg_replace('/\s+/', ' ', $modelo);
    $fabricante = preg_replace('/\s+/', ' ', $fabricante);
    $custoAquisicao = str_replace(',', '.', $custoAquisicao);

    $anoAtual = (int) date('Y');

    if ($codigoInterno === '') {
        $erros[] = 'O código interno é obrigatório.';
    } elseif (!preg_match('/^[0-9]{3}\.[0-9]{3}\.[0-9]{2}$/', $codigoInterno)) {
        $erros[] = 'O código interno deve estar no formato 000.000.00.';
    }

    if ($numeroSerie === '') {
        $erros[] = 'O número de série é obrigatório.';
    } elseif (mb_strlen($numeroSerie) > 80) {
        $erros[] = 'O número de série não pode ter mais de 80 caracteres.';
    } elseif (!preg_match('/^[A-Za-z0-9][A-Za-z0-9\-\/.]{2,79}$/', $numeroSerie)) {
        $erros[] = 'O número de série contém caracteres inválidos.';
    }

    if ($designacao === '') {
        $erros[] = 'A designação é obrigatória.';
    } elseif (mb_strlen($designacao) > 150) {
        $erros[] = 'A designação não pode ter mais de 150 caracteres.';
    }

    if ($idCategoriaEquipamento === '') {
        $erros[] = 'A categoria é obrigatória.';
    } elseif (!existe_id_lista($idCategoriaEquipamento, $categorias, 'idCategoriaEquipamento')) {
        $erros[] = 'A categoria selecionada não é válida.';
    }

    if ($idEstadoEquipamento === '') {
        $erros[] = 'O estado atual é obrigatório.';
    } elseif (!existe_id_lista($idEstadoEquipamento, $estados, 'idEstadoEquipamento')) {
        $erros[] = 'O estado selecionado não é válido.';
    }

    if ($idCriticidadeEquipamento === '') {
        $erros[] = 'A criticidade é obrigatória.';
    } elseif (!existe_id_lista($idCriticidadeEquipamento, $criticidades, 'idCriticidadeEquipamento')) {
        $erros[] = 'A criticidade selecionada não é válida.';
    }

    if ($idTipoEntrada === '') {
        $erros[] = 'O tipo de entrada é obrigatório.';
    } elseif (!existe_id_lista($idTipoEntrada, $tiposEntrada, 'idTipoEntrada')) {
        $erros[] = 'O tipo de entrada selecionado não é válido.';
    }

    if ($marca === '') {
        $erros[] = 'A marca é obrigatória.';
    } elseif (mb_strlen($marca) > 100) {
        $erros[] = 'A marca não pode ter mais de 100 caracteres.';
    }

    if ($modelo === '') {
        $erros[] = 'O modelo é obrigatório.';
    } elseif (mb_strlen($modelo) > 100) {
        $erros[] = 'O modelo não pode ter mais de 100 caracteres.';
    }

    if ($fabricante === '') {
        $erros[] = 'O fabricante é obrigatório.';
    } elseif (mb_strlen($fabricante) > 120) {
        $erros[] = 'O fabricante não pode ter mais de 120 caracteres.';
    }

    if ($dataAquisicao === '') {
        $erros[] = 'A data de aquisição é obrigatória.';
    } elseif (!data_valida($dataAquisicao)) {
        $erros[] = 'A data de aquisição não é válida.';
    } elseif ($dataAquisicao > date('Y-m-d')) {
        $erros[] = 'A data de aquisição não pode ser futura.';
    }

    if ($anoFabrico === '') {
        $erros[] = 'O ano de fabrico é obrigatório.';
    } elseif (!preg_match('/^[0-9]{4}$/', $anoFabrico)) {
        $erros[] = 'O ano de fabrico deve ter 4 dígitos.';
    } elseif ((int) $anoFabrico < 1800 || (int) $anoFabrico > $anoAtual) {
        $erros[] = 'O ano de fabrico deve estar entre 1800 e o ano atual.';
    }

    if ($custoAquisicao === '') {
        $erros[] = 'O custo de aquisição é obrigatório.';
    } elseif (!preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $custoAquisicao)) {
        $erros[] = 'O custo de aquisição deve ser um valor numérico válido, por exemplo 3500.00.';
    } elseif ((float) $custoAquisicao < 0) {
        $erros[] = 'O custo de aquisição não pode ser negativo.';
    }

    if ($observacoes !== '' && mb_strlen($observacoes) > 500) {
        $erros[] = 'As observações do equipamento não podem ter mais de 500 caracteres.';
    }

    $paresFornecedores = [];

    foreach ($fornecedoresAssociados as $indice => $fornecedorAssociado) {
        $idFornecedorAssociado = trim($fornecedorAssociado['idFornecedor'] ?? '');
        $tipoRelacaoAssociado = trim($fornecedorAssociado['tipoRelacao'] ?? '');
        $observacoesFornecedor = trim($fornecedorAssociado['observacoes'] ?? '');

        if ($idFornecedorAssociado === '' || $tipoRelacaoAssociado === '') {
            $erros[] = 'Existem fornecedores associados incompletos.';
            continue;
        }

        if (!existe_id_lista($idFornecedorAssociado, $fornecedores, 'idFornecedor')) {
            $erros[] = 'Existe um fornecedor associado inválido.';
        }

        if (!in_array($tipoRelacaoAssociado, $tiposRelacaoPermitidos, true)) {
            $erros[] = 'Existe um tipo de associação de fornecedor inválido.';
        }

        if ($observacoesFornecedor !== '' && mb_strlen($observacoesFornecedor) > 500) {
            $erros[] = 'As observações dos fornecedores associados não podem ter mais de 500 caracteres.';
        }

        $chavePar = $idFornecedorAssociado . '|' . $tipoRelacaoAssociado;

        if (in_array($chavePar, $paresFornecedores, true)) {
            $erros[] = 'Não pode associar o mesmo fornecedor duas vezes com o mesmo tipo.';
        }

        $paresFornecedores[] = $chavePar;
    }

    if (empty($localizacoesAssociadas)) {
        $erros[] = 'Deve associar pelo menos uma localização.';
    }

    $ultimaLocalizacaoId = '';
    $ultimaLocalizacaoAnterior = '';

    foreach ($localizacoesAssociadas as $localizacaoAssociada) {
        $idLocalizacaoAssociada = trim($localizacaoAssociada['idLocalizacao'] ?? '');
        $dataLocalizacao = trim($localizacaoAssociada['dataLocalizacao'] ?? '');
        $responsavel = trim($localizacaoAssociada['responsavel'] ?? '');
        $motivo = trim($localizacaoAssociada['motivo'] ?? '');

        if ($idLocalizacaoAssociada === '' || $dataLocalizacao === '' || $responsavel === '' || $motivo === '') {
            $erros[] = 'Existem localizações associadas incompletas.';
            continue;
        }

        if (!existe_id_lista($idLocalizacaoAssociada, $localizacoes, 'idLocalizacao')) {
            $erros[] = 'Existe uma localização associada inválida.';
        }

        if (!data_valida($dataLocalizacao)) {
            $erros[] = 'Existe uma data de localização inválida.';
        } elseif ($dataLocalizacao > date('Y-m-d')) {
            $erros[] = 'A data da localização não pode ser futura.';
        }

        if (mb_strlen($responsavel) > 120) {
            $erros[] = 'O responsável da localização não pode ter mais de 120 caracteres.';
        }

        if (mb_strlen($motivo) > 200) {
            $erros[] = 'O motivo/observação da localização não pode ter mais de 200 caracteres.';
        }

        if ($ultimaLocalizacaoAnterior !== '' && $ultimaLocalizacaoAnterior === $idLocalizacaoAssociada) {
            $erros[] = 'A localização não pode ser igual à última localização adicionada.';
        }

        $ultimaLocalizacaoAnterior = $idLocalizacaoAssociada;
        $ultimaLocalizacaoId = $idLocalizacaoAssociada;
    }

    $documentosUnicos = [];

    foreach ($documentosAdicionados as $documentoAdicionado) {
        $idTipoDocumento = trim($documentoAdicionado['idTipoDocumento'] ?? '');
        $nomeDocumento = trim($documentoAdicionado['nomeDocumento'] ?? '');
        $dataDocumento = trim($documentoAdicionado['dataDocumento'] ?? '');
        $dataValidade = trim($documentoAdicionado['dataValidade'] ?? '');
        $idFornecedorDocumento = trim($documentoAdicionado['idFornecedor'] ?? '');
        $ficheiroToken = trim($documentoAdicionado['ficheiroToken'] ?? '');
        $caminhoFicheiroExistente = trim($documentoAdicionado['caminhoFicheiro'] ?? '');

        if ($idTipoDocumento === '' || $nomeDocumento === '' || $dataDocumento === '') {
            $erros[] = 'Existem documentos adicionados incompletos.';
            continue;
        }

        if (!existe_id_lista($idTipoDocumento, $tiposDocumento, 'idTipoDocumento')) {
            $erros[] = 'Existe um tipo de documento inválido.';
        }

        if (mb_strlen($nomeDocumento) > 150) {
            $erros[] = 'O nome do documento não pode ter mais de 150 caracteres.';
        }

        if (!data_valida($dataDocumento)) {
            $erros[] = 'Existe uma data de documento inválida.';
        } elseif ($dataDocumento > date('Y-m-d')) {
            $erros[] = 'A data do documento não pode ser futura.';
        }

        if ($dataValidade !== '' && !data_valida($dataValidade)) {
            $erros[] = 'Existe uma data de validade de documento inválida.';
        }

        if ($dataValidade !== '' && $dataDocumento !== '' && $dataValidade < $dataDocumento) {
            $erros[] = 'A validade do documento não pode ser anterior à data do documento.';
        }

        if ($idFornecedorDocumento !== '' && !existe_id_lista($idFornecedorDocumento, $fornecedores, 'idFornecedor')) {
            $erros[] = 'Existe um fornecedor de documento inválido.';
        }

        $ficheiroDocumento = obter_ficheiro_documento($ficheiroToken);

        if ($ficheiroToken === '' && $caminhoFicheiroExistente === '') {
            $erros[] = 'O documento "' . $nomeDocumento . '" deve ter um ficheiro PDF associado.';
        } elseif ($ficheiroToken !== '' && !$ficheiroDocumento) {
            $erros[] = 'Volte a selecionar o ficheiro PDF do documento "' . $nomeDocumento . '".';
        } elseif ($ficheiroDocumento) {
            validar_pdf_documento($ficheiroDocumento, $nomeDocumento, $erros);
        }

        $chaveDocumento = mb_strtolower($nomeDocumento);

        if (in_array($chaveDocumento, $documentosUnicos, true)) {
            $erros[] = 'Não pode adicionar documentos com o mesmo nome.';
        }

        $documentosUnicos[] = $chaveDocumento;
    }

    if ($tipoGarantiaContrato === '') {
        $erros[] = 'O tipo de garantia/contrato é obrigatório.';
    } elseif (!in_array($tipoGarantiaContrato, $tiposGarantiaPermitidos, true)) {
        $erros[] = 'O tipo de garantia/contrato selecionado não é válido.';
    }

    if ($numeroContrato === '') {
        $erros[] = 'O número da garantia/contrato é obrigatório.';
    } elseif (mb_strlen($numeroContrato) > 80) {
        $erros[] = 'O número da garantia/contrato não pode ter mais de 80 caracteres.';
    }

    if ($dataInicioGarantia === '') {
        $erros[] = 'A data de início da garantia/contrato é obrigatória.';
    } elseif (!data_valida($dataInicioGarantia)) {
        $erros[] = 'A data de início da garantia/contrato não é válida.';
    }

    if ($dataFimGarantia === '') {
        $erros[] = 'A data de fim da garantia/contrato é obrigatória.';
    } elseif (!data_valida($dataFimGarantia)) {
        $erros[] = 'A data de fim da garantia/contrato não é válida.';
    }

    if ($dataInicioGarantia !== '' && $dataFimGarantia !== '' && data_valida($dataInicioGarantia) && data_valida($dataFimGarantia) && $dataFimGarantia < $dataInicioGarantia) {
        $erros[] = 'A data de fim não pode ser anterior à data de início.';
    }

    if ($idFornecedorResponsavel === '') {
        $erros[] = 'A entidade responsável pela garantia/contrato é obrigatória.';
    } elseif (!existe_id_lista($idFornecedorResponsavel, $fornecedores, 'idFornecedor')) {
        $erros[] = 'A entidade responsável selecionada não é válida.';
    }

    if ($periodicidade === '') {
        $erros[] = 'A periodicidade é obrigatória.';
    } elseif (!in_array($periodicidade, $periodicidadesPermitidas, true)) {
        $erros[] = 'A periodicidade selecionada não é válida.';
    }

    if ($observacoesGarantia === '') {
        $erros[] = 'As observações da garantia/contrato são obrigatórias.';
    } elseif (mb_strlen($observacoesGarantia) > 500) {
        $erros[] = 'As observações da garantia/contrato não podem ter mais de 500 caracteres.';
    }

    if (empty($erros)) {
        try {
            $ligacao = db_connect();

            $stmtDuplicado = $ligacao->prepare("
                SELECT COUNT(*) AS total
                FROM Equipamento
                WHERE codigoInterno = :codigoInterno
                   OR numeroSerie = :numeroSerie
            ");

            $stmtDuplicado->execute([
                ':codigoInterno' => $codigoInterno,
                ':numeroSerie' => $numeroSerie
            ]);

            $existe = (int) $stmtDuplicado->fetch()->total;

            if ($existe > 0) {
                $erros[] = 'Já existe um equipamento com esse código interno ou número de série.';
            } else {
                $ligacao->beginTransaction();

                $stmt = $ligacao->prepare("
                    INSERT INTO Equipamento (
                        codigoInterno,
                        numeroSerie,
                        idCategoriaEquipamento,
                        idEstadoEquipamento,
                        idCriticidadeEquipamento,
                        idTipoEntrada,
                        idLocalizacao,
                        designacao,
                        marca,
                        modelo,
                        fabricante,
                        dataAquisicao,
                        anoFabrico,
                        custoAquisicao,
                        observacoes,
                        ativo
                    ) VALUES (
                        :codigoInterno,
                        :numeroSerie,
                        :idCategoriaEquipamento,
                        :idEstadoEquipamento,
                        :idCriticidadeEquipamento,
                        :idTipoEntrada,
                        :idLocalizacao,
                        :designacao,
                        :marca,
                        :modelo,
                        :fabricante,
                        :dataAquisicao,
                        :anoFabrico,
                        :custoAquisicao,
                        :observacoes,
                        true
                    )
                ");

                $stmt->execute([
                    ':codigoInterno' => $codigoInterno,
                    ':numeroSerie' => $numeroSerie,
                    ':idCategoriaEquipamento' => $idCategoriaEquipamento,
                    ':idEstadoEquipamento' => $idEstadoEquipamento,
                    ':idCriticidadeEquipamento' => $idCriticidadeEquipamento,
                    ':idTipoEntrada' => $idTipoEntrada,
                    ':idLocalizacao' => $ultimaLocalizacaoId,
                    ':designacao' => $designacao,
                    ':marca' => $marca,
                    ':modelo' => $modelo,
                    ':fabricante' => $fabricante,
                    ':dataAquisicao' => $dataAquisicao,
                    ':anoFabrico' => $anoFabrico,
                    ':custoAquisicao' => $custoAquisicao,
                    ':observacoes' => $observacoes !== '' ? $observacoes : null
                ]);

                $idEquipamentoCriado = (int) $ligacao->lastInsertId();


                foreach ($localizacoesAssociadas as $localizacaoAssociada) {
                    $stmtMovimentacao = $ligacao->prepare("
                        INSERT INTO MovimentacaoEquipamento (
                            idEquipamento,
                            idLocalizacao,
                            dataLocalizacao,
                            responsavel,
                            motivo,
                            ativo
                        ) VALUES (
                            :idEquipamento,
                            :idLocalizacao,
                            :dataLocalizacao,
                            :responsavel,
                            :motivo,
                            true
                        )
                    ");

                    $stmtMovimentacao->execute([
                        ':idEquipamento' => $idEquipamentoCriado,
                        ':idLocalizacao' => trim($localizacaoAssociada['idLocalizacao']),
                        ':dataLocalizacao' => trim($localizacaoAssociada['dataLocalizacao']),
                        ':responsavel' => trim($localizacaoAssociada['responsavel']),
                        ':motivo' => trim($localizacaoAssociada['motivo'])
                    ]);
                }

                foreach ($fornecedoresAssociados as $fornecedorAssociado) {
                    $stmtFornecedor = $ligacao->prepare("
                        INSERT INTO EquipamentoFornecedor (
                            idEquipamento,
                            idFornecedor,
                            tipoRelacao,
                            dataInicio,
                            dataFim,
                            observacoes
                        ) VALUES (
                            :idEquipamento,
                            :idFornecedor,
                            :tipoRelacao,
                            :dataInicio,
                            NULL,
                            :observacoes
                        )
                    ");

                    $stmtFornecedor->execute([
                        ':idEquipamento' => $idEquipamentoCriado,
                        ':idFornecedor' => trim($fornecedorAssociado['idFornecedor']),
                        ':tipoRelacao' => trim($fornecedorAssociado['tipoRelacao']),
                        ':dataInicio' => $dataAquisicao,
                        ':observacoes' => trim($fornecedorAssociado['observacoes'] ?? '') !== '' ? trim($fornecedorAssociado['observacoes']) : null
                    ]);
                }

                foreach ($documentosAdicionados as $indiceDocumento => $documentoAdicionado) {
                    $ficheiroToken = trim($documentoAdicionado['ficheiroToken'] ?? '');
                    $ficheiroDocumento = obter_ficheiro_documento($ficheiroToken);
                    $dadosFicheiro = guardar_pdf_documento($ficheiroDocumento, $idEquipamentoCriado, $indiceDocumento);

                    $stmtDocumento = $ligacao->prepare("
                        INSERT INTO Documento (
                            idEquipamento,
                            idTipoDocumento,
                            idFornecedor,
                            nomeDocumento,
                            dataDocumento,
                            dataValidade,
                            nomeFicheiro,
                            caminhoFicheiro,
                            observacoes,
                            ativo
                        ) VALUES (
                            :idEquipamento,
                            :idTipoDocumento,
                            :idFornecedor,
                            :nomeDocumento,
                            :dataDocumento,
                            :dataValidade,
                            :nomeFicheiro,
                            :caminhoFicheiro,
                            NULL,
                            true
                        )
                    ");

                    $stmtDocumento->execute([
                        ':idEquipamento' => $idEquipamentoCriado,
                        ':idTipoDocumento' => trim($documentoAdicionado['idTipoDocumento']),
                        ':idFornecedor' => trim($documentoAdicionado['idFornecedor'] ?? '') !== '' ? trim($documentoAdicionado['idFornecedor']) : null,
                        ':nomeDocumento' => trim($documentoAdicionado['nomeDocumento']),
                        ':dataDocumento' => trim($documentoAdicionado['dataDocumento']),
                        ':dataValidade' => trim($documentoAdicionado['dataValidade'] ?? '') !== '' ? trim($documentoAdicionado['dataValidade']) : null,
                        ':nomeFicheiro' => $dadosFicheiro['nomeFicheiro'],
                        ':caminhoFicheiro' => $dadosFicheiro['caminhoFicheiro']
                    ]);
                }

                $stmtGarantia = $ligacao->prepare("
                    INSERT INTO GarantiaContrato (
                        idEquipamento,
                        idFornecedorResponsavel,
                        tipo,
                        numeroContrato,
                        dataInicio,
                        dataFim,
                        periodicidade,
                        observacoes,
                        ativo
                    ) VALUES (
                        :idEquipamento,
                        :idFornecedorResponsavel,
                        :tipo,
                        :numeroContrato,
                        :dataInicio,
                        :dataFim,
                        :periodicidade,
                        :observacoes,
                        true
                    )
                ");

                $stmtGarantia->execute([
                    ':idEquipamento' => $idEquipamentoCriado,
                    ':idFornecedorResponsavel' => $idFornecedorResponsavel,
                    ':tipo' => $tipoGarantiaContrato,
                    ':numeroContrato' => $numeroContrato,
                    ':dataInicio' => $dataInicioGarantia,
                    ':dataFim' => $dataFimGarantia,
                    ':periodicidade' => $periodicidade,
                    ':observacoes' => $observacoesGarantia
                ]);

                $ligacao->commit();

                registar_log('EQUIPAMENTO_CRIADO', 'ID equipamento: ' . $idEquipamentoCriado . ' | Código interno: ' . $codigoInterno . ' | Designação: ' . $designacao);

                $sucesso = 'Equipamento registado com sucesso.';

                $codigoInterno = '';
                $numeroSerie = '';
                $idCategoriaEquipamento = '';
                $idEstadoEquipamento = '';
                $idCriticidadeEquipamento = '';
                $idTipoEntrada = '';
                $designacao = '';
                $marca = '';
                $modelo = '';
                $fabricante = '';
                $dataAquisicao = '';
                $anoFabrico = '';
                $custoAquisicao = '';
                $observacoes = '';
                $fornecedoresAssociados = [];
                $localizacoesAssociadas = [];
                $documentosAdicionados = [];
                $tipoGarantiaContrato = '';
                $numeroContrato = '';
                $dataInicioGarantia = '';
                $dataFimGarantia = '';
                $idFornecedorResponsavel = '';
                $periodicidade = '';
                $observacoesGarantia = '';
            }
        } catch (Exception $e) {
            if (isset($ligacao) && $ligacao->inTransaction()) {
                $ligacao->rollBack();
            }

            registar_log('ERRO_BD', 'Erro ao guardar equipamento. Código interno: ' . $codigoInterno);

            $erroSistema = 'Erro ao guardar o equipamento.';
        }
    }
}

$fornecedoresAssociadosJs = [];

foreach ($fornecedoresAssociados as $fornecedorAssociado) {
    $idFornecedorAssociado = trim($fornecedorAssociado['idFornecedor'] ?? '');

    if ($idFornecedorAssociado === '') {
        continue;
    }

    $fornecedoresAssociadosJs[] = [
        'idFornecedor' => $idFornecedorAssociado,
        'fornecedorTexto' => texto_fornecedor($idFornecedorAssociado, $fornecedores),
        'tipoRelacao' => trim($fornecedorAssociado['tipoRelacao'] ?? ''),
        'observacoes' => trim($fornecedorAssociado['observacoes'] ?? '')
    ];
}

$localizacoesAssociadasJs = [];

foreach ($localizacoesAssociadas as $localizacaoAssociada) {
    $idLocalizacaoAssociada = trim($localizacaoAssociada['idLocalizacao'] ?? '');

    if ($idLocalizacaoAssociada === '') {
        continue;
    }

    $localizacoesAssociadasJs[] = [
        'idLocalizacao' => $idLocalizacaoAssociada,
        'localizacaoTexto' => texto_localizacao($idLocalizacaoAssociada, $localizacoes),
        'dataLocalizacao' => trim($localizacaoAssociada['dataLocalizacao'] ?? ''),
        'responsavel' => trim($localizacaoAssociada['responsavel'] ?? ''),
        'motivo' => trim($localizacaoAssociada['motivo'] ?? '')
    ];
}

$documentosAdicionadosJs = [];

foreach ($documentosAdicionados as $documentoAdicionado) {
    $idTipoDocumento = trim($documentoAdicionado['idTipoDocumento'] ?? '');

    if ($idTipoDocumento === '') {
        continue;
    }

    $idFornecedorDocumento = trim($documentoAdicionado['idFornecedor'] ?? '');

    $documentosAdicionadosJs[] = [
        'idTipoDocumento' => $idTipoDocumento,
        'tipoDocumento' => texto_tipo_documento($idTipoDocumento, $tiposDocumento),
        'nomeDocumento' => trim($documentoAdicionado['nomeDocumento'] ?? ''),
        'dataDocumento' => trim($documentoAdicionado['dataDocumento'] ?? ''),
        'dataValidade' => trim($documentoAdicionado['dataValidade'] ?? ''),
        'idFornecedor' => $idFornecedorDocumento,
        'fornecedorTexto' => $idFornecedorDocumento !== '' ? texto_fornecedor($idFornecedorDocumento, $fornecedores) : '',
        'nomeFicheiro' => trim($documentoAdicionado['nomeFicheiro'] ?? ''),
        'caminhoFicheiro' => trim($documentoAdicionado['caminhoFicheiro'] ?? ''),
        'ficheiroToken' => trim($documentoAdicionado['ficheiroToken'] ?? '')
    ];
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
                    <i class="fas fa-plus"></i> Adicionar Equipamento
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

        <form action="novo.php" method="post" class="formulario-equipamento" enctype="multipart/form-data" novalidate>

            <ul class="nav nav-tabs mb-4" id="separadoresNovoEquipamento" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="geral-tab" data-bs-toggle="tab"
                        data-bs-target="#geral" type="button" role="tab">
                        Dados gerais
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fornecedores-tab" data-bs-toggle="tab"
                        data-bs-target="#fornecedores" type="button" role="tab">
                        Fornecedores associados
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="localizacao-tab" data-bs-toggle="tab"
                        data-bs-target="#localizacao" type="button" role="tab">
                        Localização
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documentacao-tab" data-bs-toggle="tab"
                        data-bs-target="#documentacao" type="button" role="tab">
                        Documentação associada
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="garantias-tab" data-bs-toggle="tab"
                        data-bs-target="#garantias" type="button" role="tab">
                        Garantias e contratos
                    </button>
                </li>

            </ul>

            <div id="inputs_fornecedores_associados"></div>
            <div id="inputs_localizacoes_associadas"></div>
            <div id="inputs_documentos_adicionados"></div>
            <div id="inputs_ficheiros_documentos"></div>

            <div class="tab-content" id="conteudoSeparadoresNovoEquipamento">

                <!-- Separador: Dados gerais -->
                <div class="tab-pane fade show active" id="geral" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-laptop-medical"></i> Dados gerais do equipamento
                            </h3>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="codigoInterno" class="form-label">Código interno</label>
                                    <input type="text" class="form-control" id="codigoInterno" name="codigoInterno"
                                        placeholder="Ex.: 004.002.00" value="<?= e($codigoInterno) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="designacao" class="form-label">Designação</label>
                                    <input type="text" class="form-control" id="designacao" name="designacao"
                                        placeholder="Ex.: Monitor Multiparamétrico" value="<?= e($designacao) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="numeroSerie" class="form-label">Número de série</label>
                                    <input type="text" class="form-control" id="numeroSerie" name="numeroSerie"
                                        placeholder="Ex.: MP5-2022-45873" value="<?= e($numeroSerie) ?>">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="idCategoriaEquipamento" class="form-label">Categoria / Grupo</label>

                                    <select class="form-select" id="idCategoriaEquipamento" name="idCategoriaEquipamento">
                                        <option value="">Selecione</option>

                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= e($categoria->idCategoriaEquipamento) ?>"
                                                <?= (string) $idCategoriaEquipamento === (string) $categoria->idCategoriaEquipamento ? 'selected' : '' ?>>
                                                <?= e($categoria->descricao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="marca" class="form-label">Marca</label>
                                    <input type="text" class="form-control" id="marca" name="marca"
                                        placeholder="Ex.: Philips" value="<?= e($marca) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="modelo" class="form-label">Modelo</label>
                                    <input type="text" class="form-control" id="modelo" name="modelo"
                                        placeholder="Ex.: IntelliVue MP5" value="<?= e($modelo) ?>">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="fabricante" class="form-label">Fabricante</label>
                                    <input type="text" class="form-control" id="fabricante" name="fabricante"
                                        placeholder="Ex.: Philips Medical Systems" value="<?= e($fabricante) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="idEstadoEquipamento" class="form-label">Estado atual</label>
                                    <select class="form-select" id="idEstadoEquipamento" name="idEstadoEquipamento">
                                        <option value="">Selecione</option>

                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= e($estado->idEstadoEquipamento) ?>"
                                                <?= (string) $idEstadoEquipamento === (string) $estado->idEstadoEquipamento ? 'selected' : '' ?>>
                                                <?= e($estado->descricao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="idCriticidadeEquipamento" class="form-label">Criticidade</label>
                                    <select class="form-select" id="idCriticidadeEquipamento" name="idCriticidadeEquipamento">
                                        <option value="">Selecione</option>

                                        <?php foreach ($criticidades as $criticidade): ?>
                                            <option value="<?= e($criticidade->idCriticidadeEquipamento) ?>"
                                                <?= (string) $idCriticidadeEquipamento === (string) $criticidade->idCriticidadeEquipamento ? 'selected' : '' ?>>
                                                <?= e($criticidade->descricao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="anoFabrico" class="form-label">Ano de fabrico</label>
                                    <input type="number" class="form-control" id="anoFabrico" name="anoFabrico"
                                        placeholder="Ex.: 2021" value="<?= e($anoFabrico) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="dataAquisicao" class="form-label">Data de aquisição</label>
                                    <input type="date" class="form-control" id="dataAquisicao" name="dataAquisicao"
                                        value="<?= e($dataAquisicao) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="custoAquisicao" class="form-label">Custo de aquisição (€)</label>
                                    <input type="text" class="form-control" id="custoAquisicao" name="custoAquisicao"
                                        placeholder="Ex.: 3500.00" value="<?= e($custoAquisicao) ?>">
                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="idTipoEntrada" class="form-label">Tipo de entrada</label>
                                    <select class="form-select" id="idTipoEntrada" name="idTipoEntrada">
                                        <option value="">Selecione</option>

                                        <?php foreach ($tiposEntrada as $tipoEntrada): ?>
                                            <option value="<?= e($tipoEntrada->idTipoEntrada) ?>"
                                                <?= (string) $idTipoEntrada === (string) $tipoEntrada->idTipoEntrada ? 'selected' : '' ?>>
                                                <?= e($tipoEntrada->descricao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações / utilização</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="4"
                                    placeholder="Indique para que é utilizado o equipamento ou outra informação relevante."><?= e($observacoes) ?></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Separador: Fornecedores associados -->
                <div class="tab-pane fade" id="fornecedores" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-truck-medical"></i> Associar fornecedor existente
                            </h3>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="idFornecedor" class="form-label">Fornecedor existente</label>
                                    <select class="form-select" id="idFornecedor">
                                        <option value="">Selecione um fornecedor</option>

                                        <?php foreach ($fornecedores as $fornecedor): ?>
                                            <option value="<?= e($fornecedor->idFornecedor) ?>">
                                                <?= e($fornecedor->designacao) ?> — NIF <?= e($fornecedor->nif) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="tipoRelacao" class="form-label">
                                        Tipo de associação ao equipamento
                                    </label>
                                    <select class="form-select" id="tipoRelacao">
                                        <option value="">Selecione</option>

                                        <?php foreach ($tiposRelacaoPermitidos as $tipoRelacaoPermitido): ?>
                                            <option value="<?= e($tipoRelacaoPermitido) ?>">
                                                <?= e($tipoRelacaoPermitido) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="observacoesAssociacao" class="form-label">
                                    Observações da associação
                                </label>
                                <textarea class="form-control" id="observacoesAssociacao" rows="3"
                                    placeholder="Ex.: entidade responsável pela manutenção preventiva deste equipamento."></textarea>
                            </div>

                            <button type="button" class="btn btn-primary" id="btn-associar-fornecedor">
                                Associar fornecedor
                            </button>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-list"></i> Fornecedores associados
                            </h3>

                            <div class="table-responsive tabela-lista-container">
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario tabela-paginada-dashboard"
                                    data-linhas-pagina="5">
                                    <thead>
                                        <tr>
                                            <th>Fornecedor</th>
                                            <th>Tipo de associação</th>
                                            <th>Observações</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tabela_fornecedores_associados"></tbody>
                                </table>
                            </div>

                            <div id="paginacao_fornecedores_associados"></div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Localização -->
                <div class="tab-pane fade" id="localizacao" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-location-dot"></i> Selecionar localização
                            </h3>

                            <div class="row mb-3">

                                <div class="col-12 col-md-8">
                                    <label for="idLocalizacao" class="form-label">
                                        Localização existente
                                    </label>
                                    <select class="form-select" id="idLocalizacao">
                                        <option value="">Selecione uma localização</option>

                                        <?php foreach ($localizacoes as $localizacao): ?>
                                            <option value="<?= e($localizacao->idLocalizacao) ?>">
                                                <?= e($localizacao->edificio) ?>
                                                — Piso <?= e($localizacao->piso) ?>
                                                — <?= e($localizacao->servico) ?>
                                                — Sala <?= e($localizacao->sala) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="dataLocalizacao" class="form-label">Data da localização</label>
                                    <input type="date" class="form-control" id="dataLocalizacao">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="responsavelLocalizacao" class="form-label">Responsável</label>
                                    <select class="form-select" id="responsavelLocalizacao">
                                        <option value="">Selecione o técnico responsável</option>

                                        <?php foreach ($tecnicos as $tecnico): ?>
                                            <option value="<?= e($tecnico->nome) ?>">
                                                <?= e($tecnico->nome) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="motivoLocalizacao" class="form-label">Motivo / observação</label>
                                    <input type="text" class="form-control" id="motivoLocalizacao"
                                        placeholder="Ex.: instalação inicial">
                                </div>

                            </div>

                            <button type="button" class="btn btn-primary" id="btn-associar-localizacao">
                                Associar localização
                            </button>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-list"></i> Histórico de movimentações
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
                                            <th>Ações</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tabela_localizacoes_associadas"></tbody>
                                </table>
                            </div>

                            <div id="paginacao_localizacoes_associadas"></div>
                        </div>
                    </div>

                </div>

                <!-- Separador: Documentação associada -->
                <div class="tab-pane fade" id="documentacao" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-file-medical"></i> Adicionar documentação
                            </h3>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="tipo_documento" class="form-label">Tipo de documento</label>
                                    <select class="form-select" id="tipo_documento">
                                        <option value="">Selecione</option>

                                        <?php foreach ($tiposDocumento as $tipoDocumento): ?>
                                            <option value="<?= e($tipoDocumento->idTipoDocumento) ?>">
                                                <?= e($tipoDocumento->descricao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="nome_documento" class="form-label">Nome do documento</label>
                                    <input type="text" class="form-control" id="nome_documento"
                                        placeholder="Ex.: Manual técnico do equipamento">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="data_documento" class="form-label">Data do documento</label>
                                    <input type="date" class="form-control" id="data_documento">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="data_validade_documento" class="form-label">
                                        Validade / expiração
                                    </label>
                                    <input type="date" class="form-control" id="data_validade_documento">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="fornecedor_documento" class="form-label">Fornecedor associado</label>
                                    <select class="form-select" id="fornecedor_documento">
                                        <option value="">Sem fornecedor associado</option>

                                        <?php foreach ($fornecedores as $fornecedor): ?>
                                            <option value="<?= e($fornecedor->idFornecedor) ?>">
                                                <?= e($fornecedor->designacao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="mb-3" id="campo_ficheiro_documento">
                                <label for="ficheiro_documento" class="form-label">Ficheiro PDF</label>
                                <input type="file" class="form-control" id="ficheiro_documento" accept="application/pdf">
                            </div>

                            <button type="button" class="btn btn-primary" id="btn-adicionar-documento">
                                Adicionar documento
                            </button>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-list"></i> Documentos adicionados
                            </h3>

                            <div class="table-responsive tabela-lista-container">
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario tabela-paginada-dashboard"
                                    data-linhas-pagina="5">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Nome do documento</th>
                                            <th>Data</th>
                                            <th>Validade</th>
                                            <th>Fornecedor</th>
                                            <th>Ficheiro</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tabela_documentos_adicionados"></tbody>
                                </table>
                            </div>

                            <div id="paginacao_documentos_adicionados"></div>
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

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="tipoGarantiaContrato" class="form-label">Tipo</label>
                                    <select class="form-select" id="tipoGarantiaContrato" name="tipoGarantiaContrato">
                                        <option value="">Selecione</option>

                                        <?php foreach ($tiposGarantiaPermitidos as $tipoGarantiaPermitido): ?>
                                            <option value="<?= e($tipoGarantiaPermitido) ?>"
                                                <?= $tipoGarantiaContrato === $tipoGarantiaPermitido ? 'selected' : '' ?>>
                                                <?= e($tipoGarantiaPermitido) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="numeroContrato" class="form-label">Número da garantia/contrato</label>
                                    <input type="text" class="form-control" id="numeroContrato" name="numeroContrato"
                                        placeholder="Ex.: CM-2025-001" value="<?= e($numeroContrato) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="periodicidade" class="form-label">Periodicidade</label>
                                    <select class="form-select" id="periodicidade" name="periodicidade">
                                        <option value="">Selecione</option>

                                        <?php foreach ($periodicidadesPermitidas as $periodicidadePermitida): ?>
                                            <option value="<?= e($periodicidadePermitida) ?>"
                                                <?= $periodicidade === $periodicidadePermitida ? 'selected' : '' ?>>
                                                <?= e($periodicidadePermitida) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-4">
                                    <label for="dataInicioGarantia" class="form-label">
                                        Data de início
                                    </label>
                                    <input type="date" class="form-control" id="dataInicioGarantia"
                                        name="dataInicioGarantia" value="<?= e($dataInicioGarantia) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="dataFimGarantia" class="form-label">
                                        Data de fim
                                    </label>
                                    <input type="date" class="form-control" id="dataFimGarantia"
                                        name="dataFimGarantia" value="<?= e($dataFimGarantia) ?>">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="idFornecedorResponsavel" class="form-label">
                                        Entidade responsável
                                    </label>
                                    <select class="form-select" id="idFornecedorResponsavel" name="idFornecedorResponsavel">
                                        <option value="">Selecione</option>

                                        <?php foreach ($fornecedores as $fornecedor): ?>
                                            <option value="<?= e($fornecedor->idFornecedor) ?>"
                                                <?= (string) $idFornecedorResponsavel === (string) $fornecedor->idFornecedor ? 'selected' : '' ?>>
                                                <?= e($fornecedor->designacao) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="observacoesGarantia" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoesGarantia" name="observacoesGarantia"
                                    rows="4" placeholder="Observações sobre garantia ou contrato."><?= e($observacoesGarantia) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="lista.php" class="btn btn-outline-secondary botao-anterior">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Guardar equipamento
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

<input type="hidden" id="base_url" value="<?= BASE_URL ?>">

<input type="hidden" id="dados_fornecedores_associados"
    value='<?= e(json_encode($fornecedoresAssociadosJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>'>

<input type="hidden" id="dados_localizacoes_associadas"
    value='<?= e(json_encode($localizacoesAssociadasJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>'>

<input type="hidden" id="dados_documentos_adicionados"
    value='<?= e(json_encode($documentosAdicionadosJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>'>
<?php include __DIR__ . '/../../includes/footer.php'; ?>