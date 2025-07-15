// Gráfico de Utilizadores
new Chart(document.getElementById('usersChart'), {
    type: 'doughnut',
    data: {
        labels: ['Utilizadores', 'Condutores', 'Veículos', 'Reservas'],
        datasets: [{
            data: [usersData, driversData, vehiclesData, bookingsData],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#00FF00'],
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Distribuição Geral'
            },
            datalabels: {
                color: '#2c3e50',
                font: {
                    weight: 'bold'
                },
                formatter: (value) => value
            }
        }
    },
    plugins: [ChartDataLabels]
});

// Gráfico Gauge
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
            title: {
                display: true,
                text: 'Estado dos Veículos'
            },
            legend: {
                position: 'bottom'
            },
            tooltip: {
                enabled: true
            },
            datalabels: {
                color: '#2c3e50',
                font: {
                    weight: 'bold'
                },
                formatter: (value) => value
            }
        }
    },
    plugins: [ChartDataLabels]
});

// Gráfico de Faturamento
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'bar',
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
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Faturamento Semestral'
            },
            datalabels: {
                anchor: 'end',
                align: 'top',
                color: '#2c3e50',
                formatter: Math.round,
                font: {
                    weight: 'bold'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    },
    plugins: [ChartDataLabels]
});
