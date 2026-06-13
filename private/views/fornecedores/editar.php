<?php
require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

$page_title = APP_NAME . ' - Fornecedores';
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

            <form action="#" method="post" class="formulario-equipamento">

                <ul class="nav nav-tabs mb-4" id="separadoresFornecedorEditar" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="fornecedor-geral-tab" data-bs-target="#fornecedor-geral"
                            type="button" role="tab">
                            Identificação e contactos
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fornecedor-contactos-tab" data-bs-target="#fornecedor-contactos"
                            type="button" role="tab">
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
                                        <label for="empresa" class="form-label">Nome da empresa</label>
                                        <input type="text" class="form-control campo-obrigatorio-fornecedor"
                                            id="empresa" name="empresa" value="MedTech Portugal">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="nif" class="form-label">NIF</label>
                                        <input type="text" class="form-control campo-obrigatorio-fornecedor" id="nif"
                                            name="nif" value="509000000">
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
                                            id="telefone" name="telefone" value="+351 220 000 000">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control campo-obrigatorio-fornecedor" id="email"
                                            name="email" value="geral@medtech.pt">
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="morada" class="form-label">Morada</label>
                                    <input type="text" class="form-control campo-obrigatorio-fornecedor" id="morada"
                                        name="morada" value="Rua da Saúde, Porto, Portugal">
                                </div>

                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="text" class="form-control campo-obrigatorio-fornecedor" id="website"
                                        name="website" value="https://www.medtech.pt">
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
                                        <label for="contacto1" class="form-label">Pessoa de contacto 1</label>
                                        <input type="text" class="form-control" id="contacto1" name="contacto1"
                                            value="Ana Martins">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="telefone_contacto1" class="form-label">
                                            Telefone da pessoa de contacto 1
                                        </label>
                                        <input type="text" class="form-control" id="telefone_contacto1"
                                            name="telefone_contacto1" value="+351 910 000 000">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-6">
                                        <label for="contacto2" class="form-label">Pessoa de contacto 2</label>
                                        <input type="text" class="form-control" id="contacto2" name="contacto2"
                                            value="Pedro Costa">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="telefone_contacto2" class="form-label">
                                            Telefone da pessoa de contacto 2
                                        </label>
                                        <input type="text" class="form-control" id="telefone_contacto2"
                                            name="telefone_contacto2" value="+351 911 111 111">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-6">
                                        <label for="contacto3" class="form-label">Pessoa de contacto 3</label>
                                        <input type="text" class="form-control" id="contacto3" name="contacto3">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="telefone_contacto3" class="form-label">
                                            Telefone da pessoa de contacto 3
                                        </label>
                                        <input type="text" class="form-control" id="telefone_contacto3"
                                            name="telefone_contacto3">
                                    </div>

                                </div>

                                <div class="row mb-3">

                                    <div class="col-12 col-md-6">
                                        <label for="contacto4" class="form-label">Pessoa de contacto 4</label>
                                        <input type="text" class="form-control" id="contacto4" name="contacto4">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="telefone_contacto4" class="form-label">
                                            Telefone da pessoa de contacto 4
                                        </label>
                                        <input type="text" class="form-control" id="telefone_contacto4"
                                            name="telefone_contacto4">
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
                                        rows="4">Fornecedor responsável por assistência técnica de equipamentos críticos.</textarea>
                                </div>

                                <div class="d-flex justify-content-end gap-2">

                                    <button type="button" class="btn btn-outline-secondary botao-anterior"
                                        onclick="voltarFornecedorGeral()">
                                        Página anterior
                                    </button>

                                    <button type="button" class="btn btn-primary" onclick="mostrarMensagemFormulario()">
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

<?php include __DIR__ . '/../../includes/footer.php'; ?>