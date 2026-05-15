// Aguarda o carregamento completo da página
document.addEventListener("DOMContentLoaded", function () {

    // Seleciona o primeiro formulário da página
    const formulario = document.querySelector("form");

    // Seleciona a zona onde será apresentada a mensagem
    const mensagem = document.getElementById("mensagem-formulario");

    // Verifica se existe formulário e zona de mensagem
    if (formulario && mensagem) {

        // Evento de submissão do formulário
        formulario.addEventListener("submit", function (event) {

            // Impede o envio real do formulário nesta fase
            event.preventDefault();

            // Mostra mensagem ao utilizador
            mensagem.innerHTML = "Dados registados localmente para teste.";
        });
    }

    // Seleciona o botão do utilizador
    const botaoUtilizador = document.getElementById("btn-utilizador");

    // Verifica se o botão existe na página
    if (botaoUtilizador) {
        botaoUtilizador.addEventListener("click", function () {
            alert("Área reservada do MedInventário");
        });
    }
});

