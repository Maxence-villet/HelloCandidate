// applicationsChart.js
document.addEventListener('DOMContentLoaded', function() {
    // Préparer les données pour les 30 derniers jours
    const today = new Date();
    const dates = [];
    const counts = [];
    const statsMap = new Map();

    // Remplir la map avec les données existantes
    dailyStats.forEach(stat => {
        statsMap.set(stat.date, parseInt(stat.count));
    });

    // Générer les 30 derniers jours
    for (let i = 29; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);
        const dateString = date.toISOString().split('T')[0];
        // Formater la date pour l'affichage (ex: "12/03" au lieu de "2025-03-12")
        const formattedDate = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}`;
        dates.push(formattedDate);
        counts.push(statsMap.get(dateString) || 0);
    }

    // Configuration du graphique
    const ctx = document.getElementById('applicationsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Nombre de candidatures',
                data: counts,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Permet au graphique de s'adapter à la hauteur du conteneur
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    ticks: {
                        maxTicksLimit: 5, // Réduit le nombre d'étiquettes sur mobile
                        maxRotation: 0, // Évite la rotation des étiquettes
                        minRotation: 0,
                        font: {
                            size: 10 // Réduit la taille de la police sur mobile
                        }
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Nombre de candidatures'
                    },
                    beginAtZero: true,
                    suggestedMax: Math.max(...counts) + 1,
                    ticks: {
                        font: {
                            size: 10 // Réduit la taille de la police sur mobile
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 12 // Réduit la taille de la légende sur mobile
                        }
                    }
                }
            }
        }
    });
});