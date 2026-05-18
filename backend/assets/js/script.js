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