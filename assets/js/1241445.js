// Mostra uma mensagem simples nos formulários do backend
function mostrarMensagemFormulario() {
    const mensagem = document.getElementById("mensagem-formulario");

    if (mensagem) {
        mensagem.textContent = "Guardado com sucesso.";
        mensagem.className = "alert alert-info p-2 text-center resultado-ferramenta";
    }
}

// Mostra uma mensagem simples ao clicar no botão Utilizador
function mostrarAreaUtilizador() {
    alert("Área reservada do MedInventário");
}

// Validação simples de teste para o formulário de equipamentos
function validarEquipamento() {
    const codigo = document.getElementById("codigo").value;
    const designacao = document.getElementById("designacao").value;
    const numeroSerie = document.getElementById("numero_serie").value;
    const mensagem = document.getElementById("mensagem-formulario");

    if (codigo === "" || designacao === "" || numeroSerie === "") {
        mensagem.textContent = "Erro: preencha os campos obrigatórios.";
        mensagem.className = "alert alert-danger mt-3";
    } else if (
        codigo === "004.002.00" ||
        codigo === "007.001.00" ||
        codigo === "003.001.00" ||
        designacao === "Monitor Multiparamétrico" ||
        designacao === "Bomba de Infusão" ||
        designacao === "Ventilador Pulmonar" ||
        numeroSerie === "MP5-2022-45873" ||
        numeroSerie === "INF-2020-88321" ||
        numeroSerie === "EV500-2021-55210"
    ) {
        mensagem.textContent = "Erro: equipamento já existente.";
        mensagem.className = "alert alert-danger mt-3";
    } else {
        mensagem.textContent = "Equipamento guardado com sucesso.";
        mensagem.className = "alert alert-success mt-3";
    }
}

// Mensagem de teste para edição de garantias e contratos
function atualizarGarantiaContrato() {
    const mensagem = document.getElementById("mensagem-formulario");

    if (mensagem) {
        mensagem.textContent = "Garantia e contrato atualizados localmente para teste.";
        mensagem.className = "alert alert-success mt-3";
    }
}

// Abre secções da dashboard
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

function campoPreenchido(idCampo) {
    const campo = document.getElementById(idCampo);

    if (!campo) {
        return false;
    }

    return campo.value.trim() !== "";
}

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

function abrirSeparador(idConteudo, idBotao) {
    document.getElementById("geral").className = "tab-pane fade";
    document.getElementById("fornecedores").className = "tab-pane fade";
    document.getElementById("localizacao").className = "tab-pane fade";
    document.getElementById("documentacao").className = "tab-pane fade";
    document.getElementById("garantias").className = "tab-pane fade";

    document.getElementById("geral-tab").className = "nav-link";
    document.getElementById("fornecedores-tab").className = "nav-link";
    document.getElementById("localizacao-tab").className = "nav-link";
    document.getElementById("documentacao-tab").className = "nav-link";
    document.getElementById("garantias-tab").className = "nav-link";

    document.getElementById(idConteudo).className = "tab-pane fade show active";
    document.getElementById(idBotao).className = "nav-link active";

    limparMensagemEtapa();
}
function avancarParaFornecedores() {
    const camposDadosGerais = [
        "codigo",
        "designacao",
        "numero_serie",
        "categoria",
        "marca",
        "modelo",
        "estado",
        "criticidade",
        "ano_fabrico",
        "data_aquisicao",
        "custo_aquisicao",
        "tipo_entrada",
        "observacoes"
    ];

    for (let i = 0; i < camposDadosGerais.length; i++) {
        if (!campoPreenchido(camposDadosGerais[i])) {
            mostrarErroEtapa("Preencha todos os dados gerais antes de avançar.");
            return;
        }
    }

    abrirSeparador("fornecedores", "fornecedores-tab");
}

function associarFornecedor() {
    const fornecedor = document.getElementById("fornecedor_existente").value;
    const tipoAssociacao = document.getElementById("tipo_associacao").value;
    const observacoes = document.getElementById("observacoes_associacao").value;

    if (
        fornecedor === "" ||
        tipoAssociacao === "" ||
        observacoes === ""
    ) {
        mostrarErroEtapa("Preencha o fornecedor, o tipo de associação e as observações antes de associar.");
        return;
    }

    const tabela = document.getElementById("tabela_fornecedores_associados");

    tabela.innerHTML +=
        "<tr>" +
        "<td>" + fornecedor + "</td>" +
        "<td>" + tipoAssociacao + "</td>" +
        "<td>" + observacoes + "</td>" +
        "<td>" +
        "<button type='button' class='btn btn-sm btn-danger' title='Eliminar' onclick='removerLinha(this)'>" +
        "<i class='fas fa-trash'></i>" +
        "</button>" +
        "</td>" +
        "</tr>";

    limparMensagemEtapa();
}

function avancarParaLocalizacao() {
    const tabela = document.getElementById("tabela_fornecedores_associados");

    if (tabela.rows.length === 0) {
        mostrarErroEtapa("Associe pelo menos um fornecedor antes de avançar.");
        return;
    }

    abrirSeparador("localizacao", "localizacao-tab");
}

function associarLocalizacao() {
    const localizacao = document.getElementById("localizacao_existente").value;
    const data = document.getElementById("data_localizacao").value;
    const responsavel = document.getElementById("responsavel_localizacao").value;
    const motivo = document.getElementById("motivo_localizacao").value;

    if (
        localizacao === "" ||
        data === "" ||
        responsavel === "" ||
        motivo === ""
    ) {
        mostrarErroEtapa("Preencha todos os dados da localização antes de associar.");
        return;
    }

    const tabela = document.getElementById("tabela_localizacoes_associadas");

    tabela.innerHTML +=
        "<tr>" +
        "<td>" + localizacao + "</td>" +
        "<td>" + data + "</td>" +
        "<td>" + responsavel + "</td>" +
        "<td>" + motivo + "</td>" +
        "<td>" +
        "<button type='button' class='btn btn-sm btn-danger' title='Eliminar' onclick='removerLinha(this)'>" +
        "<i class='fas fa-trash'></i>" +
        "</button>" +
        "</td>" +
        "</tr>";

    limparMensagemEtapa();
}

function avancarParaDocumentacao() {
    const tabela = document.getElementById("tabela_localizacoes_associadas");

    if (tabela.rows.length === 0) {
        mostrarErroEtapa("Associe pelo menos uma localização antes de avançar.");
        return;
    }

    abrirSeparador("documentacao", "documentacao-tab");
}
function adicionarDocumentoNovo() {
    const tipo = document.getElementById("tipo_documento").value;
    const nome = document.getElementById("nome_documento").value;
    const fornecedor = document.getElementById("fornecedor_documento");
    const data = document.getElementById("data_documento").value;
    const validade = document.getElementById("data_validade_documento").value;
    const ficheiro = document.getElementById("ficheiro_documento").value;

    if (
        tipo === "" ||
        nome === "" ||
        fornecedor.value === "" ||
        data === "" ||
        validade === "" ||
        ficheiro === ""
    ) {
        mostrarErroEtapa("Preencha todos os dados do documento antes de adicionar.");
        return;
    }

    let nomeFicheiro = ficheiro;

    if (nomeFicheiro.lastIndexOf("\\") >= 0) {
        nomeFicheiro = nomeFicheiro.substring(nomeFicheiro.lastIndexOf("\\") + 1);
    }

    const nomeFornecedor = fornecedor.options[fornecedor.selectedIndex].text;
    const tabela = document.getElementById("tabela_documentos_adicionados");

    tabela.innerHTML +=
        "<tr>" +
        "<td>" + tipo + "</td>" +
        "<td>" + nome + "</td>" +
        "<td>" + data + "</td>" +
        "<td>" + validade + "</td>" +
        "<td>" + nomeFornecedor + "</td>" +
        "<td><a href='#' class='text-primary text-decoration-underline fw-semibold'>" + nomeFicheiro + "</a></td>" +
        "<td>" +
        "<button type='button' class='btn btn-sm btn-danger' title='Eliminar' onclick='removerLinha(this)'>" +
        "<i class='fas fa-trash'></i>" +
        "</button>" +
        "</td>" +
        "</tr>";

    limparMensagemEtapa();
}

function avancarParaGarantias() {
    abrirSeparador("garantias", "garantias-tab");
}

function voltarParaGeral() {
    abrirSeparador("geral", "geral-tab");
}

function voltarParaFornecedores() {
    abrirSeparador("fornecedores", "fornecedores-tab");
}

function voltarParaLocalizacao() {
    abrirSeparador("localizacao", "localizacao-tab");
}

function voltarParaDocumentacao() {
    abrirSeparador("documentacao", "documentacao-tab");
}


function removerLinha(botao) {
    const linha = botao.parentNode.parentNode;
    const tabela = linha.parentNode;

    tabela.removeChild(linha);
}

function prepararDashboard() {
    const cartoes = document.getElementsByClassName("dashboard-link");

    for (let i = 0; i < cartoes.length; i++) {
        cartoes[i].onclick = function () {
            const idSecao = this.getAttribute("data-secao");
            const idCollapse = this.getAttribute("data-collapse");

            abrirSecaoDashboard(idSecao, idCollapse);
        };
    }

    const botaoUtilizador = document.getElementById("btn-utilizador");

    if (botaoUtilizador && botaoUtilizador.getAttribute("onclick") === null) {
        botaoUtilizador.onclick = mostrarAreaUtilizador;
    }
}

window.addEventListener("load", prepararDashboard);