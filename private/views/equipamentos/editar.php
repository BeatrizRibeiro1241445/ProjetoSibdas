<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

if (!in_array($_SESSION['perfil'] ?? '', ['administrador', 'tecnico'])) {
    header('Location: lista.php');
    exit;
}

$idEquipamentoEncrypted = $_GET['id_equipamento'] ?? null;
$idEquipamento = aes_decrypt($idEquipamentoEncrypted);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit;
}

$idEquipamento = (int) $idEquipamento;

$page_title = APP_NAME . ' - Editar Equipamento';
$body_class = 'pagina-novo-equipamento';

$erros = [];
$erroSistema = '';

$categorias = [];
$estados = [];
$criticidades = [];
$tiposEntrada = [];
$localizacoes = [];
$fornecedores = [];
$tiposDocumento = [];

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

function existe_id_lista_editar_equipamento($id, $lista, $campo)
{
    foreach ($lista as $item) {
        if ((string) $item->$campo === (string) $id) {
            return true;
        }
    }

    return false;
}

function texto_fornecedor_editar_equipamento($idFornecedor, $fornecedores)
{
    foreach ($fornecedores as $fornecedor) {
        if ((string) $fornecedor->idFornecedor === (string) $idFornecedor) {
            return $fornecedor->designacao . ' — NIF ' . $fornecedor->nif;
        }
    }

    return '';
}

function texto_localizacao_editar_equipamento($idLocalizacao, $localizacoes)
{
    foreach ($localizacoes as $localizacao) {
        if ((string) $localizacao->idLocalizacao === (string) $idLocalizacao) {
            return $localizacao->edificio . ' — Piso ' . $localizacao->piso . ' — ' . $localizacao->servico . ' — ' . $localizacao->sala;
        }
    }

    return '';
}

function texto_tipo_documento_editar_equipamento($idTipoDocumento, $tiposDocumento)
{
    foreach ($tiposDocumento as $tipoDocumento) {
        if ((string) $tipoDocumento->idTipoDocumento === (string) $idTipoDocumento) {
            return $tipoDocumento->descricao;
        }
    }

    return '';
}

function data_valida_editar_equipamento($data)
{
    if ($data === '') {
        return false;
    }

    $objetoData = DateTime::createFromFormat('Y-m-d', $data);

    return $objetoData && $objetoData->format('Y-m-d') === $data;
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

    $stmtEquipamento = $ligacao->prepare("
        SELECT *
        FROM Equipamento
        WHERE idEquipamento = :idEquipamento
          AND ativo = true
    ");

    $stmtEquipamento->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtEquipamento->execute();
    $equipamento = $stmtEquipamento->fetch();

    if (!$equipamento) {
        header('Location: lista.php');
        exit;
    }

    $codigoInterno = $equipamento->codigoInterno;
    $numeroSerie = $equipamento->numeroSerie;
    $idCategoriaEquipamento = $equipamento->idCategoriaEquipamento;
    $idEstadoEquipamento = $equipamento->idEstadoEquipamento;
    $idCriticidadeEquipamento = $equipamento->idCriticidadeEquipamento;
    $idTipoEntrada = $equipamento->idTipoEntrada;
    $designacao = $equipamento->designacao;
    $marca = $equipamento->marca;
    $modelo = $equipamento->modelo;
    $fabricante = $equipamento->fabricante;
    $dataAquisicao = $equipamento->dataAquisicao;
    $anoFabrico = $equipamento->anoFabrico;
    $custoAquisicao = $equipamento->custoAquisicao;
    $observacoes = $equipamento->observacoes;

    $stmtFornecedores = $ligacao->prepare("
        SELECT
            ef.idFornecedor,
            f.designacao,
            f.nif,
            ef.tipoRelacao,
            ef.observacoes
        FROM EquipamentoFornecedor ef
        INNER JOIN Fornecedor f
            ON ef.idFornecedor = f.idFornecedor
        WHERE ef.idEquipamento = :idEquipamento
        ORDER BY f.designacao, ef.tipoRelacao
    ");

    $stmtFornecedores->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtFornecedores->execute();
    $fornecedoresBaseDados = $stmtFornecedores->fetchAll();

    foreach ($fornecedoresBaseDados as $fornecedorAssociado) {
        $fornecedoresAssociados[] = [
            'idFornecedor' => (string) $fornecedorAssociado->idFornecedor,
            'tipoRelacao' => $fornecedorAssociado->tipoRelacao,
            'observacoes' => $fornecedorAssociado->observacoes ?? ''
        ];
    }

    if (!empty($equipamento->idLocalizacao)) {
        $localizacoesAssociadas[] = [
            'idLocalizacao' => (string) $equipamento->idLocalizacao,
            'dataLocalizacao' => $equipamento->dataAquisicao ?? date('Y-m-d'),
            'responsavel' => 'Registo existente',
            'motivo' => 'Localização atual'
        ];
    }

    $stmtDocumentos = $ligacao->prepare("
        SELECT
            idTipoDocumento,
            idFornecedor,
            nomeDocumento,
            dataDocumento,
            dataValidade,
            nomeFicheiro
        FROM Documento
        WHERE idEquipamento = :idEquipamento
          AND ativo = true
        ORDER BY dataDocumento DESC, nomeDocumento
    ");

    $stmtDocumentos->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtDocumentos->execute();
    $documentosBaseDados = $stmtDocumentos->fetchAll();

    foreach ($documentosBaseDados as $documento) {
        $documentosAdicionados[] = [
            'idTipoDocumento' => (string) $documento->idTipoDocumento,
            'nomeDocumento' => $documento->nomeDocumento,
            'dataDocumento' => $documento->dataDocumento,
            'dataValidade' => $documento->dataValidade ?? '',
            'idFornecedor' => $documento->idFornecedor !== null ? (string) $documento->idFornecedor : '',
            'nomeFicheiro' => $documento->nomeFicheiro ?? ''
        ];
    }

    $stmtGarantia = $ligacao->prepare("
        SELECT *
        FROM GarantiaContrato
        WHERE idEquipamento = :idEquipamento
          AND ativo = true
        ORDER BY dataFim DESC
        LIMIT 1
    ");

    $stmtGarantia->bindValue(':idEquipamento', $idEquipamento, PDO::PARAM_INT);
    $stmtGarantia->execute();
    $garantiaContrato = $stmtGarantia->fetch();

    if ($garantiaContrato) {
        $tipoGarantiaContrato = $garantiaContrato->tipo;
        $numeroContrato = $garantiaContrato->numeroContrato;
        $dataInicioGarantia = $garantiaContrato->dataInicio;
        $dataFimGarantia = $garantiaContrato->dataFim;
        $idFornecedorResponsavel = $garantiaContrato->idFornecedorResponsavel;
        $periodicidade = $garantiaContrato->periodicidade ?: 'Pontual';
        $observacoesGarantia = $garantiaContrato->observacoes;
    }
} catch (PDOException $e) {
    $erroSistema = 'Erro ao carregar os dados do equipamento.';
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
    } elseif (!existe_id_lista_editar_equipamento($idCategoriaEquipamento, $categorias, 'idCategoriaEquipamento')) {
        $erros[] = 'A categoria selecionada não é válida.';
    }

    if ($idEstadoEquipamento === '') {
        $erros[] = 'O estado atual é obrigatório.';
    } elseif (!existe_id_lista_editar_equipamento($idEstadoEquipamento, $estados, 'idEstadoEquipamento')) {
        $erros[] = 'O estado selecionado não é válido.';
    }

    if ($idCriticidadeEquipamento === '') {
        $erros[] = 'A criticidade é obrigatória.';
    } elseif (!existe_id_lista_editar_equipamento($idCriticidadeEquipamento, $criticidades, 'idCriticidadeEquipamento')) {
        $erros[] = 'A criticidade selecionada não é válida.';
    }

    if ($idTipoEntrada === '') {
        $erros[] = 'O tipo de entrada é obrigatório.';
    } elseif (!existe_id_lista_editar_equipamento($idTipoEntrada, $tiposEntrada, 'idTipoEntrada')) {
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
    } elseif (!data_valida_editar_equipamento($dataAquisicao)) {
        $erros[] = 'A data de aquisição não é válida.';
    } elseif ($dataAquisicao > date('Y-m-d')) {
        $erros[] = 'A data de aquisição não pode ser futura.';
    }

    if ($anoFabrico === '') {
        $erros[] = 'O ano de fabrico é obrigatório.';
    } elseif (!preg_match('/^[0-9]{4}$/', $anoFabrico)) {
        $erros[] = 'O ano de fabrico deve ter 4 dígitos.';
    } elseif ((int) $anoFabrico < 1900 || (int) $anoFabrico > $anoAtual) {
        $erros[] = 'O ano de fabrico deve estar entre 1900 e o ano atual.';
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

    foreach ($fornecedoresAssociados as $fornecedorAssociado) {
        $idFornecedorAssociado = trim($fornecedorAssociado['idFornecedor'] ?? '');
        $tipoRelacaoAssociado = trim($fornecedorAssociado['tipoRelacao'] ?? '');
        $observacoesFornecedor = trim($fornecedorAssociado['observacoes'] ?? '');

        if ($idFornecedorAssociado === '' || $tipoRelacaoAssociado === '') {
            $erros[] = 'Existem fornecedores associados incompletos.';
            continue;
        }

        if (!existe_id_lista_editar_equipamento($idFornecedorAssociado, $fornecedores, 'idFornecedor')) {
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

        if (!existe_id_lista_editar_equipamento($idLocalizacaoAssociada, $localizacoes, 'idLocalizacao')) {
            $erros[] = 'Existe uma localização associada inválida.';
        }

        if (!data_valida_editar_equipamento($dataLocalizacao)) {
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

        if ($idTipoDocumento === '' || $nomeDocumento === '' || $dataDocumento === '') {
            $erros[] = 'Existem documentos adicionados incompletos.';
            continue;
        }

        if (!existe_id_lista_editar_equipamento($idTipoDocumento, $tiposDocumento, 'idTipoDocumento')) {
            $erros[] = 'Existe um tipo de documento inválido.';
        }

        if (mb_strlen($nomeDocumento) > 150) {
            $erros[] = 'O nome do documento não pode ter mais de 150 caracteres.';
        }

        if (!data_valida_editar_equipamento($dataDocumento)) {
            $erros[] = 'Existe uma data de documento inválida.';
        }

        if ($dataValidade !== '' && !data_valida_editar_equipamento($dataValidade)) {
            $erros[] = 'Existe uma data de validade de documento inválida.';
        }

        if ($dataValidade !== '' && $dataDocumento !== '' && $dataValidade < $dataDocumento) {
            $erros[] = 'A validade do documento não pode ser anterior à data do documento.';
        }

        if ($idFornecedorDocumento !== '' && !existe_id_lista_editar_equipamento($idFornecedorDocumento, $fornecedores, 'idFornecedor')) {
            $erros[] = 'Existe um fornecedor de documento inválido.';
        }

        $chaveDocumento = $idTipoDocumento . '|' . mb_strtolower($nomeDocumento) . '|' . $dataDocumento;

        if (in_array($chaveDocumento, $documentosUnicos, true)) {
            $erros[] = 'Não pode adicionar documentos duplicados à tabela.';
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
    } elseif (!data_valida_editar_equipamento($dataInicioGarantia)) {
        $erros[] = 'A data de início da garantia/contrato não é válida.';
    }

    if ($dataFimGarantia === '') {
        $erros[] = 'A data de fim da garantia/contrato é obrigatória.';
    } elseif (!data_valida_editar_equipamento($dataFimGarantia)) {
        $erros[] = 'A data de fim da garantia/contrato não é válida.';
    }

    if ($dataInicioGarantia !== '' && $dataFimGarantia !== '' && data_valida_editar_equipamento($dataInicioGarantia) && data_valida_editar_equipamento($dataFimGarantia) && $dataFimGarantia < $dataInicioGarantia) {
        $erros[] = 'A data de fim não pode ser anterior à data de início.';
    }

    if ($idFornecedorResponsavel === '') {
        $erros[] = 'A entidade responsável pela garantia/contrato é obrigatória.';
    } elseif (!existe_id_lista_editar_equipamento($idFornecedorResponsavel, $fornecedores, 'idFornecedor')) {
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
            $ligacao->beginTransaction();

            $stmtDuplicado = $ligacao->prepare("
                SELECT COUNT(*) AS total
                FROM Equipamento
                WHERE ativo = true
                  AND idEquipamento <> :idEquipamento
                  AND (codigoInterno = :codigoInterno OR numeroSerie = :numeroSerie)
            ");

            $stmtDuplicado->execute([
                ':idEquipamento' => $idEquipamento,
                ':codigoInterno' => $codigoInterno,
                ':numeroSerie' => $numeroSerie
            ]);

            $existe = (int) $stmtDuplicado->fetch()->total;

            if ($existe > 0) {
                $erros[] = 'Já existe outro equipamento com esse código interno ou número de série.';
                $ligacao->rollBack();
            } else {
                $stmtUpdateEquipamento = $ligacao->prepare("
                    UPDATE Equipamento
                    SET
                        codigoInterno = :codigoInterno,
                        numeroSerie = :numeroSerie,
                        idCategoriaEquipamento = :idCategoriaEquipamento,
                        idEstadoEquipamento = :idEstadoEquipamento,
                        idCriticidadeEquipamento = :idCriticidadeEquipamento,
                        idTipoEntrada = :idTipoEntrada,
                        idLocalizacao = :idLocalizacao,
                        designacao = :designacao,
                        marca = :marca,
                        modelo = :modelo,
                        fabricante = :fabricante,
                        dataAquisicao = :dataAquisicao,
                        anoFabrico = :anoFabrico,
                        custoAquisicao = :custoAquisicao,
                        observacoes = :observacoes
                    WHERE idEquipamento = :idEquipamento
                ");

                $stmtUpdateEquipamento->execute([
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
                    ':observacoes' => $observacoes !== '' ? $observacoes : null,
                    ':idEquipamento' => $idEquipamento
                ]);

                $stmtApagarFornecedores = $ligacao->prepare("
                    DELETE FROM EquipamentoFornecedor
                    WHERE idEquipamento = :idEquipamento
                ");

                $stmtApagarFornecedores->execute([
                    ':idEquipamento' => $idEquipamento
                ]);

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
                        ':idEquipamento' => $idEquipamento,
                        ':idFornecedor' => trim($fornecedorAssociado['idFornecedor']),
                        ':tipoRelacao' => trim($fornecedorAssociado['tipoRelacao']),
                        ':dataInicio' => $dataAquisicao,
                        ':observacoes' => trim($fornecedorAssociado['observacoes'] ?? '') !== '' ? trim($fornecedorAssociado['observacoes']) : null
                    ]);
                }

                $stmtDesativarDocumentos = $ligacao->prepare("
                    UPDATE Documento
                    SET ativo = false
                    WHERE idEquipamento = :idEquipamento
                ");

                $stmtDesativarDocumentos->execute([
                    ':idEquipamento' => $idEquipamento
                ]);

                foreach ($documentosAdicionados as $documentoAdicionado) {
                    $nomeFicheiro = trim($documentoAdicionado['nomeFicheiro'] ?? '');

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
                        ':idEquipamento' => $idEquipamento,
                        ':idTipoDocumento' => trim($documentoAdicionado['idTipoDocumento']),
                        ':idFornecedor' => trim($documentoAdicionado['idFornecedor'] ?? '') !== '' ? trim($documentoAdicionado['idFornecedor']) : null,
                        ':nomeDocumento' => trim($documentoAdicionado['nomeDocumento']),
                        ':dataDocumento' => trim($documentoAdicionado['dataDocumento']),
                        ':dataValidade' => trim($documentoAdicionado['dataValidade'] ?? '') !== '' ? trim($documentoAdicionado['dataValidade']) : null,
                        ':nomeFicheiro' => $nomeFicheiro !== '' ? $nomeFicheiro : null,
                        ':caminhoFicheiro' => $nomeFicheiro !== '' ? 'uploads/documentos/' . $nomeFicheiro : null
                    ]);
                }

                $stmtGarantiaExistente = $ligacao->prepare("
                    SELECT idGarantiaContrato
                    FROM GarantiaContrato
                    WHERE idEquipamento = :idEquipamento
                      AND ativo = true
                    ORDER BY dataFim DESC
                    LIMIT 1
                ");

                $stmtGarantiaExistente->execute([
                    ':idEquipamento' => $idEquipamento
                ]);

                $garantiaExistente = $stmtGarantiaExistente->fetch();

                if ($garantiaExistente) {
                    $stmtGarantia = $ligacao->prepare("
                        UPDATE GarantiaContrato
                        SET
                            idFornecedorResponsavel = :idFornecedorResponsavel,
                            tipo = :tipo,
                            numeroContrato = :numeroContrato,
                            dataInicio = :dataInicio,
                            dataFim = :dataFim,
                            periodicidade = :periodicidade,
                            observacoes = :observacoes
                        WHERE idGarantiaContrato = :idGarantiaContrato
                    ");

                    $stmtGarantia->execute([
                        ':idFornecedorResponsavel' => $idFornecedorResponsavel,
                        ':tipo' => $tipoGarantiaContrato,
                        ':numeroContrato' => $numeroContrato,
                        ':dataInicio' => $dataInicioGarantia,
                        ':dataFim' => $dataFimGarantia,
                        ':periodicidade' => $periodicidade,
                        ':observacoes' => $observacoesGarantia,
                        ':idGarantiaContrato' => $garantiaExistente->idGarantiaContrato
                    ]);
                } else {
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
                        ':idEquipamento' => $idEquipamento,
                        ':idFornecedorResponsavel' => $idFornecedorResponsavel,
                        ':tipo' => $tipoGarantiaContrato,
                        ':numeroContrato' => $numeroContrato,
                        ':dataInicio' => $dataInicioGarantia,
                        ':dataFim' => $dataFimGarantia,
                        ':periodicidade' => $periodicidade,
                        ':observacoes' => $observacoesGarantia
                    ]);
                }

                $ligacao->commit();

                header('Location: lista.php');
                exit;
            }
        } catch (PDOException $e) {
            if (isset($ligacao) && $ligacao->inTransaction()) {
                $ligacao->rollBack();
            }

            $erroSistema = 'Erro ao atualizar o equipamento.';
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
        'fornecedorTexto' => texto_fornecedor_editar_equipamento($idFornecedorAssociado, $fornecedores),
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
        'localizacaoTexto' => texto_localizacao_editar_equipamento($idLocalizacaoAssociada, $localizacoes),
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
        'tipoDocumento' => texto_tipo_documento_editar_equipamento($idTipoDocumento, $tiposDocumento),
        'nomeDocumento' => trim($documentoAdicionado['nomeDocumento'] ?? ''),
        'dataDocumento' => trim($documentoAdicionado['dataDocumento'] ?? ''),
        'dataValidade' => trim($documentoAdicionado['dataValidade'] ?? ''),
        'idFornecedor' => $idFornecedorDocumento,
        'fornecedorTexto' => $idFornecedorDocumento !== '' ? texto_fornecedor_editar_equipamento($idFornecedorDocumento, $fornecedores) : '',
        'nomeFicheiro' => trim($documentoAdicionado['nomeFicheiro'] ?? '')
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
                    <i class="fas fa-pen"></i> Editar Equipamento
                </strong>
            </h2>

            <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <hr>

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

        <form action="editar.php?id_equipamento=<?= e($idEquipamentoEncrypted) ?>" method="post" class="formulario-equipamento" enctype="multipart/form-data" novalidate>

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
                        Localização atual
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

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-primary" onclick="avancarParaFornecedores()">
                                    Página seguinte
                                </button>
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

                            <button type="button" class="btn btn-primary" onclick="associarFornecedor()">
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
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
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

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary botao-anterior" onclick="voltarParaGeral()">
                                    Página anterior
                                </button>

                                <button type="button" class="btn btn-primary" onclick="avancarParaLocalizacao()">
                                    Página seguinte
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Separador: Localização atual -->
                <div class="tab-pane fade" id="localizacao" role="tabpanel">

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-location-dot"></i> Selecionar localização existente
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
                                                — <?= e($localizacao->sala) ?>
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
                                    <input type="text" class="form-control" id="responsavelLocalizacao"
                                        placeholder="Ex.: Técnico responsável">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="motivoLocalizacao" class="form-label">Motivo / observação</label>
                                    <input type="text" class="form-control" id="motivoLocalizacao"
                                        placeholder="Ex.: instalação inicial">
                                </div>

                            </div>

                            <button type="button" class="btn btn-primary" onclick="associarLocalizacao()">
                                Associar localização
                            </button>

                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">

                            <h3>
                                <i class="fas fa-list"></i> Localização associada
                            </h3>

                            <div class="table-responsive tabela-lista-container">
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
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

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary botao-anterior" onclick="voltarParaFornecedores()">
                                    Página anterior
                                </button>

                                <button type="button" class="btn btn-primary" onclick="avancarParaDocumentacao()">
                                    Página seguinte
                                </button>
                            </div>

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
                                        placeholder="Ex.: Manual Técnico do Equipamento">
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

                            <div class="mb-3">
                                <label for="ficheiro_documento" class="form-label">Ficheiro</label>
                                <input type="file" class="form-control" id="ficheiro_documento">
                            </div>

                            <button type="button" class="btn btn-primary" onclick="adicionarDocumentoNovo()">
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
                                <table class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
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

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary botao-anterior" onclick="voltarParaLocalizacao()">
                                    Página anterior
                                </button>

                                <button type="button" class="btn btn-primary" onclick="avancarParaGarantias()">
                                    Página seguinte
                                </button>
                            </div>

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

                                <div class="col-12 col-md-6">
                                    <label for="tipoGarantiaContrato" class="form-label">Tipo</label>
                                    <select class="form-select" id="tipoGarantiaContrato" name="tipoGarantiaContrato">
                                        <option value="">Selecione</option>

                                        <?php foreach ($tiposGarantiaPermitidos as $tipoPermitido): ?>
                                            <option value="<?= e($tipoPermitido) ?>"
                                                <?= (string) $tipoGarantiaContrato === (string) $tipoPermitido ? 'selected' : '' ?>>
                                                <?= e($tipoPermitido) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="numeroContrato" class="form-label">Número da garantia/contrato</label>
                                    <input type="text" class="form-control" id="numeroContrato"
                                        name="numeroContrato" value="<?= e($numeroContrato) ?>"
                                        placeholder="Ex.: GAR-2024-001 ou CM-2024-001">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <label for="dataInicioGarantia" class="form-label">Data de início</label>
                                    <input type="date" class="form-control" id="dataInicioGarantia"
                                        name="dataInicioGarantia" value="<?= e($dataInicioGarantia) ?>">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="dataFimGarantia" class="form-label">Data de fim</label>
                                    <input type="date" class="form-control" id="dataFimGarantia"
                                        name="dataFimGarantia" value="<?= e($dataFimGarantia) ?>">
                                </div>

                            </div>

                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
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

                                <div class="col-12 col-md-6">
                                    <label for="periodicidade" class="form-label">Periodicidade</label>
                                    <select class="form-select" id="periodicidade" name="periodicidade">
                                        <option value="">Selecione</option>

                                        <?php foreach ($periodicidadesPermitidas as $periodicidadePermitida): ?>
                                            <option value="<?= e($periodicidadePermitida) ?>"
                                                <?= (string) $periodicidade === (string) $periodicidadePermitida ? 'selected' : '' ?>>
                                                <?= e($periodicidadePermitida) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="observacoesGarantia" class="form-label">
                                    Observações da garantia/contrato
                                </label>
                                <textarea class="form-control" id="observacoesGarantia"
                                    name="observacoesGarantia" rows="4"><?= e($observacoesGarantia) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary botao-anterior" onclick="voltarParaDocumentacao()">
                                    Página anterior
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Guardar alterações
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

<script>
    window.medInventarioNovoEquipamento = {
        fornecedoresAssociados: <?= json_encode($fornecedoresAssociadosJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        localizacoesAssociadas: <?= json_encode($localizacoesAssociadasJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        documentosAdicionados: <?= json_encode($documentosAdicionadosJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
    };
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>