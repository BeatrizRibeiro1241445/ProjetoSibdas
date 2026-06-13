<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Equipamentos';
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
                        <i class="fas fa-plus"></i> Adicionar Equipamento
                    </strong>
                </h2>

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <hr>

            <form action="#" method="post" class="formulario-equipamento" enctype="multipart/form-data">

                <ul class="nav nav-tabs mb-4" id="separadoresNovoEquipamento" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="geral-tab" type="button">
                            Dados gerais
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fornecedores-tab" type="button">
                            Fornecedores associados
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="localizacao-tab" type="button">
                            Localização atual
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documentacao-tab" type="button">
                            Documentação associada
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="garantias-tab" type="button">
                            Garantias e contratos
                        </button>
                    </li>

                </ul>

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
                                        <label for="codigo" class="form-label">Código interno</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo"
                                            placeholder="Ex.: 111.111.11">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="designacao" class="form-label">Designação</label>
                                        <input type="text" class="form-control" id="designacao" name="designacao"
                                            placeholder="Ex.: Monitor Multiparamétrico">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="numero_serie" class="form-label">Número de série</label>
                                        <input type="text" class="form-control" id="numero_serie" name="numero_serie"
                                            placeholder="Ex.: MP5-2022-45873">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-4">
                                        <label for="categoria" class="form-label">Categoria / Grupo</label>
                                        <input type="text" class="form-control" id="categoria" name="categoria"
                                            placeholder="Ex.: Monitorização, suporte de vida">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="marca" class="form-label">Marca</label>
                                        <input type="text" class="form-control" id="marca" name="marca"
                                            placeholder="Ex.: Philips">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="modelo" class="form-label">Modelo</label>
                                        <input type="text" class="form-control" id="modelo" name="modelo"
                                            placeholder="Ex.: IntelliVue MP5">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-4">
                                        <label for="estado" class="form-label">Estado atual</label>
                                        <select class="form-select" id="estado" name="estado">
                                            <option value="">Selecione</option>
                                            <option value="ativo">Ativo</option>
                                            <option value="em_manutencao">Em manutenção</option>
                                            <option value="inativo">Inativo</option>
                                            <option value="avariado">Avariado</option>
                                            <option value="fora_servico">Fora de serviço</option>
                                            <option value="abatido">Abatido</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="criticidade" class="form-label">Criticidade</label>
                                        <select class="form-select" id="criticidade" name="criticidade">
                                            <option value="">Selecione</option>
                                            <option value="baixa">Baixa</option>
                                            <option value="media">Média</option>
                                            <option value="alta">Alta</option>
                                            <option value="suporte_vida">Suporte de vida</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="ano_fabrico" class="form-label">Ano de fabrico</label>
                                        <input type="number" class="form-control" id="ano_fabrico" name="ano_fabrico"
                                            placeholder="Ex.: 2021">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-4">
                                        <label for="data_aquisicao" class="form-label">Data de aquisição</label>
                                        <input type="date" class="form-control" id="data_aquisicao"
                                            name="data_aquisicao">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="custo_aquisicao" class="form-label">Custo de aquisição</label>
                                        <input type="text" class="form-control" id="custo_aquisicao"
                                            name="custo_aquisicao" placeholder="Ex.: 3500 €">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="tipo_entrada" class="form-label">Tipo de entrada</label>
                                        <select class="form-select" id="tipo_entrada" name="tipo_entrada">
                                            <option value="">Selecione</option>
                                            <option value="compra">Compra</option>
                                            <option value="aluguer">Aluguer</option>
                                            <option value="doacao">Doação</option>
                                            <option value="emprestimo">Empréstimo</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações / utilização</label>
                                    <textarea class="form-control" id="observacoes" name="observacoes" rows="4"
                                        placeholder="Indique para que é utilizado o equipamento ou outra informação relevante."></textarea>
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
                                        <label for="fornecedor_existente" class="form-label">Fornecedor
                                            existente</label>
                                        <select class="form-select" id="fornecedor_existente"
                                            name="fornecedor_existente">
                                            <option value="">Selecione um fornecedor</option>
                                            <option value="MedTech Portugal">MedTech Portugal</option>
                                            <option value="Philips Medical Systems">Philips Medical Systems</option>
                                            <option value="Hospital Devices S.A.">Hospital Devices S.A.</option>
                                            <option value="Consumíveis Clínicos Norte">Consumíveis Clínicos Norte
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="tipo_associacao" class="form-label">
                                            Tipo de associação ao equipamento
                                        </label>
                                        <select class="form-select" id="tipo_associacao" name="tipo_associacao">
                                            <option value="">Selecione</option>
                                            <option value="Fabricante">Fabricante</option>
                                            <option value="Fornecedor comercial">Fornecedor comercial</option>
                                            <option value="Empresa de assistência técnica">
                                                Empresa de assistência técnica
                                            </option>
                                            <option value="Fornecedor de consumíveis/acessórios">
                                                Fornecedor de consumíveis/acessórios
                                            </option>
                                        </select>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="observacoes_associacao" class="form-label">
                                        Observações da associação
                                    </label>
                                    <textarea class="form-control" id="observacoes_associacao"
                                        name="observacoes_associacao" rows="3"
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

                                <table
                                    class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
                                    <thead>
                                        <tr>
                                            <th>Fornecedor</th>
                                            <th>Tipo de associação</th>
                                            <th>Observações</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tabela_fornecedores_associados">
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary botao-anterior"
                                        onclick="voltarParaGeral()">
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
                                        <label for="localizacao_existente" class="form-label">
                                            Localização existente
                                        </label>
                                        <select class="form-select" id="localizacao_existente"
                                            name="localizacao_existente">
                                            <option value="">Selecione uma localização</option>
                                            <option value="Hospital Central - Piso 2 - UCI - Sala 1">
                                                Hospital Central - Piso 2 - UCI - Sala 1
                                            </option>
                                            <option value="Hospital Central - Piso 1 - Bloco Operatório - Sala 2">
                                                Hospital Central - Piso 1 - Bloco Operatório - Sala 2
                                            </option>
                                            <option value="Hospital Central - Piso 0 - Urgência - Sala 3">
                                                Hospital Central - Piso 0 - Urgência - Sala 3
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="data_localizacao" class="form-label">Data da localização</label>
                                        <input type="date" class="form-control" id="data_localizacao"
                                            name="data_localizacao">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-6">
                                        <label for="responsavel_localizacao" class="form-label">Responsável</label>
                                        <input type="text" class="form-control" id="responsavel_localizacao"
                                            name="responsavel_localizacao" placeholder="Ex.: Técnico responsável">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="motivo_localizacao" class="form-label">Motivo / observação</label>
                                        <input type="text" class="form-control" id="motivo_localizacao"
                                            name="motivo_localizacao" placeholder="Ex.: instalação inicial">
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

                                <table
                                    class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
                                    <thead>
                                        <tr>
                                            <th>Localização</th>
                                            <th>Data</th>
                                            <th>Responsável</th>
                                            <th>Motivo / observação</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tabela_localizacoes_associadas">
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary botao-anterior"
                                        onclick="voltarParaFornecedores()">
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
                                        <select class="form-select" id="tipo_documento" name="tipo_documento">
                                            <option value="">Selecione</option>
                                            <option value="Manual técnico">Manual técnico</option>
                                            <option value="Certificado de calibração">Certificado de calibração</option>
                                            <option value="Contrato de manutenção">Contrato de manutenção</option>
                                            <option value="Relatório técnico">Relatório técnico</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="nome_documento" class="form-label">Nome do documento</label>
                                        <input type="text" class="form-control" id="nome_documento"
                                            name="nome_documento" placeholder="Ex.: Manual Técnico do Equipamento">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-4">
                                        <label for="data_documento" class="form-label">Data do documento</label>
                                        <input type="date" class="form-control" id="data_documento"
                                            name="data_documento">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="data_validade_documento" class="form-label">
                                            Validade / expiração
                                        </label>
                                        <input type="date" class="form-control" id="data_validade_documento"
                                            name="data_validade_documento">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="fornecedor_documento" class="form-label">Fornecedor
                                            associado</label>
                                        <select class="form-select" id="fornecedor_documento"
                                            name="fornecedor_documento">
                                            <option value="">Selecione</option>
                                            <option value="MedTech Portugal">MedTech Portugal</option>
                                            <option value="Philips Medical Systems">Philips Medical Systems</option>
                                            <option value="Hospital Devices S.A.">Hospital Devices S.A.</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="ficheiro_documento" class="form-label">Ficheiro</label>
                                    <input type="file" class="form-control" id="ficheiro_documento"
                                        name="ficheiro_documento">
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

                                <table
                                    class="table table-bordered table-hover align-middle text-center tabela-lista tabela-formulario">
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

                                    <tbody id="tabela_documentos_adicionados">
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary botao-anterior"
                                        onclick="voltarParaLocalizacao()">
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

                                    <div class="col-12 col-md-4">
                                        <label for="data_inicio_garantia" class="form-label">
                                            Data de início da garantia
                                        </label>
                                        <input type="date" class="form-control" id="data_inicio_garantia"
                                            name="data_inicio_garantia">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="data_fim_garantia" class="form-label">
                                            Data de fim da garantia
                                        </label>
                                        <input type="date" class="form-control" id="data_fim_garantia"
                                            name="data_fim_garantia">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="entidade_garantia" class="form-label">
                                            Entidade responsável
                                        </label>
                                        <input type="text" class="form-control" id="entidade_garantia"
                                            name="entidade_garantia" placeholder="Ex.: MedTech Portugal">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-4">
                                        <label for="existe_contrato" class="form-label">
                                            Existe contrato de manutenção?
                                        </label>
                                        <select class="form-select" id="existe_contrato" name="existe_contrato">
                                            <option value="">Selecione</option>
                                            <option value="Sim">Sim</option>
                                            <option value="Não">Não</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="tipo_contrato" class="form-label">Tipo de contrato</label>
                                        <input type="text" class="form-control" id="tipo_contrato" name="tipo_contrato"
                                            placeholder="Ex.: manutenção preventiva">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label for="periodicidade" class="form-label">Periodicidade</label>
                                        <input type="text" class="form-control" id="periodicidade" name="periodicidade"
                                            placeholder="Ex.: anual">
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="entidade_contrato" class="form-label">
                                        Entidade responsável pelo contrato
                                    </label>
                                    <input type="text" class="form-control" id="entidade_contrato"
                                        name="entidade_contrato" placeholder="Ex.: MedTech Portugal">
                                </div>

                                <div class="mb-3">
                                    <label for="observacoes_garantia" class="form-label">Observações</label>
                                    <textarea class="form-control" id="observacoes_garantia" name="observacoes_garantia"
                                        rows="4" placeholder="Observações sobre garantia ou contrato."></textarea>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary botao-anterior"
                                        onclick="voltarParaDocumentacao()">
                                        Página anterior
                                    </button>

                                    <button type="button" class="btn btn-primary" onclick="validarEquipamento()">
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

<?php include __DIR__ . '/../../includes/footer.php'; ?>