// Mostra uma mensagem simples nos formulários do backend
function mostrarMensagemFormulario() {
    const mensagem = document.getElementById("mensagem-formulario");

    if (mensagem) {
        mensagem.textContent = "Dados registados localmente para teste.";
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