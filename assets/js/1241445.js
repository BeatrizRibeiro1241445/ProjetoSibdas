function mostrarErroEtapa(texto) {
    const mensagem = document.getElementById("mensagem-formulario");

    if (mensagem) {
        mensagem.textContent = texto;
        mensagem.className = "alert alert-danger mt-3";
    }
}

function limparMensagemEtapa() {
    const mensagem = document.getElementById("mensagem-formulario");

    if (mensagem) {
        mensagem.textContent = "";
        mensagem.className = "";
    }
}

function limparHtml(valor) {
    return String(valor ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function obterBaseUrl() {
    const campoBaseUrl = document.getElementById("base_url");

    if (campoBaseUrl) {
        return campoBaseUrl.value.replace(/\/+$/, "");
    }

    return "";
}

function criarLigacaoFicheiroDocumento(item) {
    const nome = limparHtml(item.nomeFicheiro || "Abrir PDF");
    let caminho = "";

    if (item.ficheiroUrl) {
        caminho = item.ficheiroUrl;
    } else if (item.caminhoFicheiro) {
        const baseUrl = obterBaseUrl();
        caminho = baseUrl + "/" + String(item.caminhoFicheiro).replace(/^\/+/, "");
    }

    if (caminho === "") {
        return nome;
    }

    return "<a href='" + limparHtml(caminho) + "' class='btn btn-sm btn-outline-secondary' target='_blank' rel='noopener'>" +
        "<i class='fas fa-file-pdf'></i> " + nome +
        "</a>";
}

// =====================================================
// Dashboard
// =====================================================

function abrirSecaoDashboard(idSecao, idCollapse) {
    const secao = document.getElementById(idSecao);
    const caixa = document.getElementById(idCollapse);

    if (caixa) {
        caixa.classList.add("show");
    }

    if (secao) {
        secao.scrollIntoView();
    }
}

function prepararDashboard() {
    const cartoes = document.getElementsByClassName("dashboard-link");

    for (let i = 0; i < cartoes.length; i++) {
        cartoes[i].addEventListener("click", function () {
            const idSecao = this.getAttribute("data-secao");
            const idCollapse = this.getAttribute("data-collapse");

            abrirSecaoDashboard(idSecao, idCollapse);
        });
    }
}


// =====================================================
// Novo equipamento - estrutura visual com tabelas
// =====================================================

const registosPorPaginaNovoEquipamento = 5;

let fornecedoresAssociadosNovo = [];
let localizacoesAssociadasNovo = [];
let documentosAdicionadosNovo = [];

let paginaFornecedoresNovo = 1;
let paginaLocalizacoesNovo = 1;
let paginaDocumentosNovo = 1;

function dataFormularioValida(data) {
    if (data === "") {
        return false;
    }

    const partes = data.split("-");

    if (partes.length !== 3) {
        return false;
    }

    const ano = Number(partes[0]);
    const mes = Number(partes[1]);
    const dia = Number(partes[2]);
    const dataObjeto = new Date(ano, mes - 1, dia);

    return dataObjeto.getFullYear() === ano &&
        dataObjeto.getMonth() === mes - 1 &&
        dataObjeto.getDate() === dia;
}

function criarPaginacaoInterna(totalRegistos, paginaAtual, idContainer, callback) {
    const container = document.getElementById(idContainer);

    if (!container) {
        return;
    }

    const totalPaginas = Math.max(1, Math.ceil(totalRegistos / registosPorPaginaNovoEquipamento));

    if (totalPaginas <= 1) {
        container.innerHTML = "";
        return;
    }

    let html = "";

    html += "<nav class='mt-3' aria-label='Paginação interna'>";
    html += "<ul class='pagination pagination-sm justify-content-center paginacao-equipamentos'>";

    html += "<li class='page-item " + (paginaAtual <= 1 ? "disabled" : "") + "'>";
    html += "<button type='button' class='page-link' data-pagina='" + (paginaAtual - 1) + "'>&laquo;</button>";
    html += "</li>";

    for (let pagina = 1; pagina <= totalPaginas; pagina++) {
        html += "<li class='page-item " + (pagina === paginaAtual ? "active" : "") + "'>";
        html += "<button type='button' class='page-link' data-pagina='" + pagina + "'>" + pagina + "</button>";
        html += "</li>";
    }

    html += "<li class='page-item " + (paginaAtual >= totalPaginas ? "disabled" : "") + "'>";
    html += "<button type='button' class='page-link' data-pagina='" + (paginaAtual + 1) + "'>&raquo;</button>";
    html += "</li>";

    html += "</ul>";
    html += "</nav>";

    container.innerHTML = html;

    const botoes = container.querySelectorAll("button[data-pagina]");

    for (let i = 0; i < botoes.length; i++) {
        botoes[i].addEventListener("click", function () {
            const novaPagina = Number(this.getAttribute("data-pagina"));

            if (novaPagina >= 1 && novaPagina <= totalPaginas) {
                callback(novaPagina);
            }
        });
    }
}

// =====================================================
// Fornecedores associados no novo equipamento
// =====================================================

function atualizarInputsFornecedoresNovo() {
    const container = document.getElementById("inputs_fornecedores_associados");

    if (!container) {
        return;
    }

    let html = "";

    for (let i = 0; i < fornecedoresAssociadosNovo.length; i++) {
        const item = fornecedoresAssociadosNovo[i];

        html += "<input type='hidden' name='fornecedoresAssociados[" + i + "][idFornecedor]' value='" + limparHtml(item.idFornecedor) + "'>";
        html += "<input type='hidden' name='fornecedoresAssociados[" + i + "][tipoRelacao]' value='" + limparHtml(item.tipoRelacao) + "'>";
        html += "<input type='hidden' name='fornecedoresAssociados[" + i + "][observacoes]' value='" + limparHtml(item.observacoes) + "'>";
    }

    container.innerHTML = html;
}

function renderFornecedoresNovo() {
    const tabela = document.getElementById("tabela_fornecedores_associados");

    if (!tabela) {
        return;
    }

    atualizarInputsFornecedoresNovo();

    const totalPaginas = Math.max(
        1,
        Math.ceil(fornecedoresAssociadosNovo.length / registosPorPaginaNovoEquipamento)
    );

    if (paginaFornecedoresNovo > totalPaginas) {
        paginaFornecedoresNovo = totalPaginas;
    }

    const inicio = (paginaFornecedoresNovo - 1) * registosPorPaginaNovoEquipamento;
    const fim = inicio + registosPorPaginaNovoEquipamento;
    const registos = fornecedoresAssociadosNovo.slice(inicio, fim);

    if (registos.length === 0) {
        tabela.innerHTML = "<tr><td colspan='4' class='text-center'>Ainda não existem fornecedores associados.</td></tr>";
    } else {
        tabela.innerHTML = registos.map(function (item, index) {
            const indexReal = inicio + index;

            return "<tr>" +
                "<td>" + limparHtml(item.fornecedorTexto) + "</td>" +
                "<td>" + limparHtml(item.tipoRelacao) + "</td>" +
                "<td>" + limparHtml(item.observacoes || "-") + "</td>" +
                "<td>" +
                "<button type='button' class='btn btn-sm btn-acao btn-arquivar' title='Eliminar' data-acao='remover-fornecedor-equipamento' data-index='" + indexReal + "'>" +
                "<i class='fas fa-trash'></i>" +
                "</button>" +
                "</td>" +
                "</tr>";
        }).join("");
    }

    criarPaginacaoInterna(
        fornecedoresAssociadosNovo.length,
        paginaFornecedoresNovo,
        "paginacao_fornecedores_associados",
        function (pagina) {
            paginaFornecedoresNovo = pagina;
            renderFornecedoresNovo();
        }
    );
}

function associarFornecedor() {
    const fornecedor = document.getElementById("idFornecedor");
    const tipoRelacao = document.getElementById("tipoRelacao");
    const observacoes = document.getElementById("observacoesAssociacao");

    if (!fornecedor || !tipoRelacao || !observacoes) {
        mostrarErroEtapa("Não foi possível encontrar os campos do fornecedor.");
        return;
    }

    const idFornecedor = fornecedor.value;
    const tipo = tipoRelacao.value;
    const textoFornecedor = fornecedor.options[fornecedor.selectedIndex]
        ? fornecedor.options[fornecedor.selectedIndex].text
        : "";

    if (idFornecedor === "" || tipo === "") {
        mostrarErroEtapa("Selecione o fornecedor e o tipo de associação antes de associar.");
        return;
    }

    const existe = fornecedoresAssociadosNovo.some(function (item) {
        return item.idFornecedor === idFornecedor && item.tipoRelacao === tipo;
    });

    if (existe) {
        mostrarErroEtapa("Esse fornecedor já está associado com esse tipo.");
        return;
    }

    fornecedoresAssociadosNovo.push({
        idFornecedor: idFornecedor,
        fornecedorTexto: textoFornecedor,
        tipoRelacao: tipo,
        observacoes: observacoes.value.trim()
    });

    fornecedor.value = "";
    tipoRelacao.value = "";
    observacoes.value = "";

    paginaFornecedoresNovo = Math.ceil(
        fornecedoresAssociadosNovo.length / registosPorPaginaNovoEquipamento
    );

    limparMensagemEtapa();
    renderFornecedoresNovo();
}

function removerFornecedorEquipamento(index) {
    fornecedoresAssociadosNovo.splice(index, 1);
    renderFornecedoresNovo();
}

// =====================================================
// Localizações associadas no novo equipamento
// =====================================================

function atualizarInputsLocalizacoesNovo() {
    const container = document.getElementById("inputs_localizacoes_associadas");

    if (!container) {
        return;
    }

    let html = "";

    for (let i = 0; i < localizacoesAssociadasNovo.length; i++) {
        const item = localizacoesAssociadasNovo[i];

        html += "<input type='hidden' name='localizacoesAssociadas[" + i + "][idLocalizacao]' value='" + limparHtml(item.idLocalizacao) + "'>";
        html += "<input type='hidden' name='localizacoesAssociadas[" + i + "][dataLocalizacao]' value='" + limparHtml(item.dataLocalizacao) + "'>";
        html += "<input type='hidden' name='localizacoesAssociadas[" + i + "][responsavel]' value='" + limparHtml(item.responsavel) + "'>";
        html += "<input type='hidden' name='localizacoesAssociadas[" + i + "][motivo]' value='" + limparHtml(item.motivo) + "'>";
    }

    container.innerHTML = html;
}
function renderLocalizacoesNovo() {
    const tabela = document.getElementById("tabela_localizacoes_associadas");

    if (!tabela) {
        return;
    }

    atualizarInputsLocalizacoesNovo();

    const totalPaginas = Math.max(
        1,
        Math.ceil(localizacoesAssociadasNovo.length / registosPorPaginaNovoEquipamento)
    );

    if (paginaLocalizacoesNovo > totalPaginas) {
        paginaLocalizacoesNovo = totalPaginas;
    }

    const inicio = (paginaLocalizacoesNovo - 1) * registosPorPaginaNovoEquipamento;
    const fim = inicio + registosPorPaginaNovoEquipamento;
    const registos = localizacoesAssociadasNovo.slice(inicio, fim);

    if (registos.length === 0) {
        tabela.innerHTML = "<tr><td colspan='5' class='text-center'>Ainda não existe localização associada.</td></tr>";
    } else {
        tabela.innerHTML = registos.map(function (item, index) {
            const indexReal = inicio + index;

            return "<tr>" +
                "<td>" + limparHtml(item.localizacaoTexto) + "</td>" +
                "<td>" + limparHtml(item.dataLocalizacao) + "</td>" +
                "<td>" + limparHtml(item.responsavel) + "</td>" +
                "<td>" + limparHtml(item.motivo) + "</td>" +
                "<td>" +
                "<button type='button' class='btn btn-sm btn-acao btn-arquivar' title='Eliminar' data-acao='remover-localizacao-equipamento' data-index='" + indexReal + "'>" +
                "<i class='fas fa-trash'></i>" +
                "</button>" +
                "</td>" +
                "</tr>";
        }).join("");
    }

    criarPaginacaoInterna(
        localizacoesAssociadasNovo.length,
        paginaLocalizacoesNovo,
        "paginacao_localizacoes_associadas",
        function (pagina) {
            paginaLocalizacoesNovo = pagina;
            renderLocalizacoesNovo();
        }
    );
}

function associarLocalizacao() {
    const localizacao = document.getElementById("idLocalizacao");
    const data = document.getElementById("dataLocalizacao") || document.getElementById("data_localizacao");
    const responsavel = document.getElementById("responsavelLocalizacao") || document.getElementById("responsavel_localizacao");
    const motivo = document.getElementById("motivoLocalizacao") || document.getElementById("motivo_localizacao");

    if (!localizacao || !data || !responsavel || !motivo) {
        mostrarErroEtapa("Não foi possível encontrar os campos da localização.");
        return;
    }

    const idLocalizacao = localizacao.value;
    const dataLocalizacao = data.value;
    const textoLocalizacao = localizacao.options[localizacao.selectedIndex]
        ? localizacao.options[localizacao.selectedIndex].text
        : "";
    const responsavelTexto = responsavel.value.trim();
    const motivoTexto = motivo.value.trim();

    if (
        idLocalizacao === "" ||
        dataLocalizacao === "" ||
        responsavelTexto === "" ||
        motivoTexto === ""
    ) {
        mostrarErroEtapa("Preencha todos os dados da localização antes de associar.");
        return;
    }

    if (!dataFormularioValida(dataLocalizacao)) {
        mostrarErroEtapa("A data da localização não é válida.");
        return;
    }

    const ultimaLocalizacao = localizacoesAssociadasNovo[localizacoesAssociadasNovo.length - 1];

    if (ultimaLocalizacao && ultimaLocalizacao.idLocalizacao === idLocalizacao) {
        mostrarErroEtapa("A localização não pode ser igual à última localização adicionada.");
        return;
    }

    localizacoesAssociadasNovo.push({
        idLocalizacao: idLocalizacao,
        localizacaoTexto: textoLocalizacao,
        dataLocalizacao: dataLocalizacao,
        responsavel: responsavelTexto,
        motivo: motivoTexto
    });

    localizacao.value = "";
    data.value = "";
    responsavel.value = "";
    motivo.value = "";

    paginaLocalizacoesNovo = Math.ceil(
        localizacoesAssociadasNovo.length / registosPorPaginaNovoEquipamento
    );

    limparMensagemEtapa();
    renderLocalizacoesNovo();
}

function removerLocalizacaoEquipamento(index) {
    localizacoesAssociadasNovo.splice(index, 1);
    renderLocalizacoesNovo();
}

// =====================================================
// Documentos adicionados visualmente no novo equipamento
// =====================================================


function criarTokenDocumento() {
    return "doc_" + Date.now() + "_" + Math.floor(Math.random() * 100000);
}

function criarNovoInputFicheiroDocumento() {
    const campo = document.getElementById("campo_ficheiro_documento");

    if (!campo) {
        return;
    }

    campo.innerHTML = "<label for='ficheiro_documento' class='form-label'>Ficheiro PDF</label>" +
        "<input type='file' class='form-control' id='ficheiro_documento' accept='application/pdf'>";
}

function guardarInputFicheiroDocumento(token) {
    const ficheiroDocumento = document.getElementById("ficheiro_documento");
    const container = document.getElementById("inputs_ficheiros_documentos");

    if (!ficheiroDocumento || !container || ficheiroDocumento.files.length === 0) {
        return false;
    }

    ficheiroDocumento.removeAttribute("id");
    ficheiroDocumento.setAttribute("name", "documentosFicheiros[" + token + "]");
    ficheiroDocumento.setAttribute("data-token", token);
    ficheiroDocumento.classList.add("d-none");

    container.appendChild(ficheiroDocumento);
    criarNovoInputFicheiroDocumento();

    return true;
}

function removerInputFicheiroDocumento(token) {
    if (!token) {
        return;
    }

    const ficheiro = document.querySelector("input[type='file'][data-token='" + token + "']");

    if (ficheiro) {
        ficheiro.remove();
    }
}

function atualizarInputsDocumentosNovo() {
    const container = document.getElementById("inputs_documentos_adicionados");

    if (!container) {
        return;
    }

    let html = "";

    for (let i = 0; i < documentosAdicionadosNovo.length; i++) {
        const item = documentosAdicionadosNovo[i];
        const idTipoDocumento = item.idTipoDocumento || "";
        const idFornecedor = item.idFornecedor || "";

        html += "<input type='hidden' name='documentosAdicionados[" + i + "][idTipoDocumento]' value='" + limparHtml(idTipoDocumento) + "'>";
        html += "<input type='hidden' name='documentosAdicionados[" + i + "][nomeDocumento]' value='" + limparHtml(item.nomeDocumento || "") + "'>";
        html += "<input type='hidden' name='documentosAdicionados[" + i + "][dataDocumento]' value='" + limparHtml(item.dataDocumento || "") + "'>";
        html += "<input type='hidden' name='documentosAdicionados[" + i + "][dataValidade]' value='" + limparHtml(item.dataValidade || "") + "'>";
        html += "<input type='hidden' name='documentosAdicionados[" + i + "][idFornecedor]' value='" + limparHtml(idFornecedor) + "'>";
        html += "<input type='hidden' name='documentosAdicionados[" + i + "][nomeFicheiro]' value='" + limparHtml(item.nomeFicheiro || "") + "'>";
        html += "<input type='hidden' name='documentosAdicionados[" + i + "][caminhoFicheiro]' value='" + limparHtml(item.caminhoFicheiro || "") + "'>";
        html += "<input type='hidden' name='documentosAdicionados[" + i + "][ficheiroToken]' value='" + limparHtml(item.ficheiroToken || "") + "'>";
    }

    container.innerHTML = html;
}

function renderDocumentosNovo() {
    const tabela = document.getElementById("tabela_documentos_adicionados");

    if (!tabela) {
        return;
    }

    atualizarInputsDocumentosNovo();

    const totalPaginas = Math.max(
        1,
        Math.ceil(documentosAdicionadosNovo.length / registosPorPaginaNovoEquipamento)
    );

    if (paginaDocumentosNovo > totalPaginas) {
        paginaDocumentosNovo = totalPaginas;
    }

    const inicio = (paginaDocumentosNovo - 1) * registosPorPaginaNovoEquipamento;
    const fim = inicio + registosPorPaginaNovoEquipamento;
    const registos = documentosAdicionadosNovo.slice(inicio, fim);

    if (registos.length === 0) {
        tabela.innerHTML = "<tr><td colspan='7' class='text-center'>Ainda não existem documentos adicionados.</td></tr>";
    } else {
        tabela.innerHTML = registos.map(function (item, index) {
            const indexReal = inicio + index;

            return "<tr>" +
                "<td>" + limparHtml(item.tipoDocumento || "-") + "</td>" +
                "<td>" + limparHtml(item.nomeDocumento || "-") + "</td>" +
                "<td>" + limparHtml(item.dataDocumento || "-") + "</td>" +
                "<td>" + limparHtml(item.dataValidade || "-") + "</td>" +
                "<td>" + limparHtml(item.fornecedorTexto || "-") + "</td>" +
                "<td>" + criarLigacaoFicheiroDocumento(item) + "</td>" +
                "<td>" +
                "<button type='button' class='btn btn-sm btn-acao btn-arquivar' title='Eliminar' data-acao='remover-documento-novo' data-index='" + indexReal + "'>" +
                "<i class='fas fa-trash'></i>" +
                "</button>" +
                "</td>" +
                "</tr>";
        }).join("");
    }

    criarPaginacaoInterna(
        documentosAdicionadosNovo.length,
        paginaDocumentosNovo,
        "paginacao_documentos_adicionados",
        function (pagina) {
            paginaDocumentosNovo = pagina;
            renderDocumentosNovo();
        }
    );
}

function adicionarDocumentoNovo() {
    const tipoDocumento = document.getElementById("tipo_documento");
    const nomeDocumento = document.getElementById("nome_documento");
    const dataDocumento = document.getElementById("data_documento");
    const dataValidade = document.getElementById("data_validade_documento");
    const fornecedorDocumento = document.getElementById("fornecedor_documento");
    const ficheiroDocumento = document.getElementById("ficheiro_documento");

    if (
        !tipoDocumento ||
        !nomeDocumento ||
        !dataDocumento ||
        !dataValidade ||
        !fornecedorDocumento ||
        !ficheiroDocumento
    ) {
        mostrarErroEtapa("Não foi possível encontrar os campos do documento.");
        return;
    }

    const idTipoDocumento = tipoDocumento.value;
    const tipoTexto = tipoDocumento.options[tipoDocumento.selectedIndex]
        ? tipoDocumento.options[tipoDocumento.selectedIndex].text
        : "";

    const nome = nomeDocumento.value.trim();
    const data = dataDocumento.value;
    const validade = dataValidade.value;
    const idFornecedor = fornecedorDocumento.value;

    const fornecedorTexto = fornecedorDocumento.options[fornecedorDocumento.selectedIndex]
        ? fornecedorDocumento.options[fornecedorDocumento.selectedIndex].text
        : "";

    if (idTipoDocumento === "" || nome === "" || data === "") {
        mostrarErroEtapa("Preencha o tipo, o nome e a data do documento antes de adicionar.");
        return;
    }

    if (!dataFormularioValida(data)) {
        mostrarErroEtapa("A data do documento não é válida.");
        return;
    }

    if (validade !== "" && !dataFormularioValida(validade)) {
        mostrarErroEtapa("A data de validade do documento não é válida.");
        return;
    }

    if (validade !== "" && validade < data) {
        mostrarErroEtapa("A validade do documento não pode ser anterior à data do documento.");
        return;
    }

    if (ficheiroDocumento.files.length === 0) {
        mostrarErroEtapa("Selecione o ficheiro PDF antes de adicionar o documento.");
        return;
    }

    const ficheiro = ficheiroDocumento.files[0];
    const nomeFicheiro = ficheiro.name;
    const ficheiroUrl = URL.createObjectURL(ficheiro);

    if (nomeFicheiro.toLowerCase().slice(-4) !== ".pdf") {
        URL.revokeObjectURL(ficheiroUrl);
        mostrarErroEtapa("O ficheiro do documento deve estar em formato PDF.");
        return;
    }

    const duplicado = documentosAdicionadosNovo.some(function (item) {
        return String(item.nomeDocumento || "").toLowerCase() === nome.toLowerCase();
    });

    if (duplicado) {
        URL.revokeObjectURL(ficheiroUrl);
        mostrarErroEtapa("Já existe um documento com esse nome.");
        return;
    }

    const token = criarTokenDocumento();

    if (!guardarInputFicheiroDocumento(token)) {
        URL.revokeObjectURL(ficheiroUrl);
        mostrarErroEtapa("Não foi possível preparar o ficheiro para envio.");
        return;
    }

    documentosAdicionadosNovo.push({
        idTipoDocumento: idTipoDocumento,
        tipoDocumento: tipoTexto,
        nomeDocumento: nome,
        dataDocumento: data,
        dataValidade: validade,
        idFornecedor: idFornecedor,
        fornecedorTexto: idFornecedor === "" ? "" : fornecedorTexto,
        nomeFicheiro: nomeFicheiro,
        caminhoFicheiro: "",
        ficheiroUrl: ficheiroUrl,
        ficheiroToken: token
    });

    tipoDocumento.value = "";
    nomeDocumento.value = "";
    dataDocumento.value = "";
    dataValidade.value = "";
    fornecedorDocumento.value = "";

    paginaDocumentosNovo = Math.ceil(
        documentosAdicionadosNovo.length / registosPorPaginaNovoEquipamento
    );

    limparMensagemEtapa();
    renderDocumentosNovo();
}

function removerDocumentoNovo(index) {
    const documento = documentosAdicionadosNovo[index];

    if (documento && documento.ficheiroToken) {
        removerInputFicheiroDocumento(documento.ficheiroToken);
    }

    if (documento && documento.ficheiroUrl) {
        URL.revokeObjectURL(documento.ficheiroUrl);
    }

    documentosAdicionadosNovo.splice(index, 1);
    renderDocumentosNovo();
}


// =====================================================
// Botões ligados por JavaScript
// =====================================================

function ligarClique(idBotao, funcao) {
    const botao = document.getElementById(idBotao);

    if (botao) {
        botao.addEventListener("click", funcao);
    }
}

function prepararBotoesEquipamento() {
    ligarClique("btn-associar-fornecedor", associarFornecedor);
    ligarClique("btn-associar-localizacao", associarLocalizacao);
    ligarClique("btn-adicionar-documento", adicionarDocumentoNovo);
}

function prepararBotoesDinamicosEquipamento() {
    document.addEventListener("click", function (evento) {
        const botao = evento.target.closest("button[data-acao]");

        if (!botao) {
            return;
        }

        const acao = botao.getAttribute("data-acao");
        const index = parseInt(botao.getAttribute("data-index"), 10);

        if (Number.isNaN(index)) {
            return;
        }

        if (acao === "remover-fornecedor-equipamento") {
            removerFornecedorEquipamento(index);
        }

        if (acao === "remover-localizacao-equipamento") {
            removerLocalizacaoEquipamento(index);
        }

        if (acao === "remover-documento-novo") {
            removerDocumentoNovo(index);
        }
    });
}

function lerJsonInput(idCampo) {
    const campo = document.getElementById(idCampo);

    if (!campo || campo.value.trim() === "") {
        return [];
    }

    try {
        const dados = JSON.parse(campo.value);

        if (Array.isArray(dados)) {
            return dados;
        }

        return [];
    } catch (erro) {
        return [];
    }
}

// =====================================================
// Paginação das tabelas da dashboard
// =====================================================

function prepararPaginacaoDashboard() {
    const tabelas = document.querySelectorAll(".tabela-paginada-dashboard");

    for (let i = 0; i < tabelas.length; i++) {
        paginarTabelaDashboard(tabelas[i]);
    }
}

function paginarTabelaDashboard(tabela) {
    const tbody = tabela.querySelector("tbody");

    if (!tbody) {
        return;
    }

    const linhas = Array.from(tbody.querySelectorAll("tr"));
    const linhasPorPagina = parseInt(tabela.getAttribute("data-linhas-pagina") || "5", 10);
    const totalPaginas = Math.ceil(linhas.length / linhasPorPagina);

    if (totalPaginas <= 1) {
        return;
    }

    let paginaAtual = 1;

    const paginacao = document.createElement("nav");
    paginacao.className = "mt-3";
    paginacao.setAttribute("aria-label", "Paginação da tabela");

    tabela.insertAdjacentElement("afterend", paginacao);

    function mostrarPagina(pagina) {
        paginaAtual = pagina;

        const inicio = (paginaAtual - 1) * linhasPorPagina;
        const fim = inicio + linhasPorPagina;

        for (let i = 0; i < linhas.length; i++) {
            linhas[i].style.display = i >= inicio && i < fim ? "" : "none";
        }

        desenharBotoes();
    }

    function desenharBotoes() {
        let html = "";

        html += "<ul class='pagination pagination-sm justify-content-center paginacao-equipamentos'>";

        html += "<li class='page-item " + (paginaAtual === 1 ? "disabled" : "") + "'>";
        html += "<button type='button' class='page-link' data-pagina='" + (paginaAtual - 1) + "'>";
        html += "<i class='fas fa-chevron-left'></i>";
        html += "</button>";
        html += "</li>";

        for (let pagina = 1; pagina <= totalPaginas; pagina++) {
            html += "<li class='page-item " + (pagina === paginaAtual ? "active" : "") + "'>";
            html += "<button type='button' class='page-link' data-pagina='" + pagina + "'>" + pagina + "</button>";
            html += "</li>";
        }

        html += "<li class='page-item " + (paginaAtual === totalPaginas ? "disabled" : "") + "'>";
        html += "<button type='button' class='page-link' data-pagina='" + (paginaAtual + 1) + "'>";
        html += "<i class='fas fa-chevron-right'></i>";
        html += "</button>";
        html += "</li>";

        html += "</ul>";

        paginacao.innerHTML = html;

        const botoes = paginacao.querySelectorAll("button[data-pagina]");

        for (let i = 0; i < botoes.length; i++) {
            botoes[i].addEventListener("click", function () {
                const pagina = parseInt(this.getAttribute("data-pagina"), 10);

                if (pagina >= 1 && pagina <= totalPaginas) {
                    mostrarPagina(pagina);
                }
            });
        }
    }

    mostrarPagina(1);
}

// =====================================================
// Inicialização do novo equipamento
// =====================================================

function prepararNovoEquipamento() {
    if (!document.getElementById("separadoresNovoEquipamento")) {
        return;
    }

    fornecedoresAssociadosNovo = lerJsonInput("dados_fornecedores_associados");
    localizacoesAssociadasNovo = lerJsonInput("dados_localizacoes_associadas");
    documentosAdicionadosNovo = lerJsonInput("dados_documentos_adicionados");

    paginaFornecedoresNovo = 1;
    paginaLocalizacoesNovo = 1;
    paginaDocumentosNovo = 1;

    renderFornecedoresNovo();
    renderLocalizacoesNovo();
    renderDocumentosNovo();

    const formulario = document.querySelector("form.formulario-equipamento");

    if (formulario) {
        formulario.addEventListener("submit", function () {
            atualizarInputsFornecedoresNovo();
            atualizarInputsLocalizacoesNovo();
            atualizarInputsDocumentosNovo();
        });
    }
}

// =====================================================
// Inicialização geral
// =====================================================

window.addEventListener("load", function () {
    prepararDashboard();
    prepararBotoesEquipamento();
    prepararBotoesDinamicosEquipamento();
    prepararNovoEquipamento();
    prepararPaginacaoDashboard();
});