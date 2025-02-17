document.addEventListener("DOMContentLoaded", function () {
    const ccInput = document.getElementById("cartao_cidadao");
    const erroSpan = document.getElementById("cc-erro");
    const btnSubmeter = document.getElementById("btn-submeter");

    ccInput.addEventListener("input", function () {
        const numero = ccInput.value.replace(/\D/g, ""); // Remover caracteres não numéricos

        if (numero.length === 8) {
            erroSpan.style.display = "none";
            btnSubmeter.disabled = false;
        } else {
            erroSpan.style.display = "inline";
            btnSubmeter.disabled = true;
        }
    });
});
