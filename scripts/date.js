// Elementos de data de levantamento e devolução
const pickupDate = document.getElementById('data_retirada');
const returnDate = document.getElementById('data_devolucao');

// Obter a data atual
const today = new Date();
const year = today.getFullYear();
const month = String(today.getMonth() + 1).padStart(2, '0');
const day = String(today.getDate()).padStart(2, '0');
const minDate = `${year}-${month}-${day}`;

// Configurar data mínima para levantamento e devolução
pickupDate.min = minDate;
returnDate.min = minDate;

// Validar data de levantamento
pickupDate.addEventListener('change', function () {
  if (new Date(pickupDate.value) < new Date(minDate)) {
    alert("A data de levantamento não pode ser anterior à data atual.");
    pickupDate.value = minDate; // Ajustar para a data mínima
  }
  // Atualizar a data mínima do campo de devolução para ser no mínimo igual à de levantamento
  returnDate.min = pickupDate.value;
});

// Validar data de devolução
returnDate.addEventListener('change', function () {
  if (new Date(returnDate.value) < new Date(pickupDate.value)) {
    alert("A data de devolução não pode ser antes da data de levantamento.");
    returnDate.value = pickupDate.value; // Ajustar para a data mínima permitida
  } else if (new Date(returnDate.value) < new Date(minDate)) {
    alert("A data de devolução não pode ser anterior à data atual.");
    returnDate.value = minDate; // Ajustar para a data mínima geral
  }
});
  window.addEventListener('DOMContentLoaded', function () {
    const hoje = new Date().toISOString().split('T')[0];
    document.getElementById('data_retirada').value = hoje;
  });

