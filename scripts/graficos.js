// Gráfico de Utilizadores
new Chart(document.getElementById('usersChart'), {
    type: 'doughnut',
    data: {
        labels: ['Utilizadores', 'Condutores', 'Veículos', 'Reservas'],
        datasets: [{
            data: [usersData, driversData, vehiclesData, bookingsData],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#00FF00'],
        }]
    }
});

// Gráfico Gauge (com dados reais)
new Chart(document.getElementById('gaugeChart'), {
    type: 'doughnut',
    data: {
        labels: ['Ocupado', 'Disponível'],
        datasets: [{
            data: [ocupados, disponiveis],
            backgroundColor: ['#FF6384', '#36A2EB'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        cutout: '70%',
        plugins: {
            tooltip: {
                enabled: true
            },
            legend: {
                position: 'bottom',
            }
        }
    }
});


// Configuração do Gráfico
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'bar', // Gráfico de barras
    data: {
        labels: mesesSemestre,
        datasets: [{
            label: 'Faturamento (€)',
            data: Object.values(faturamentoSemestre),
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
});
