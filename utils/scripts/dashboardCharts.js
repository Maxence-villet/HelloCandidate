// Application Status Chart
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('applicationStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: window.dashboardData.months, // Access data passed from PHP
            datasets: [
                {
                    label: 'Pending',
                    data: window.dashboardData.statuses.pending, // Access statuses
                    backgroundColor: '#2563eb',
                },
                {
                    label: 'Interview',
                    data: window.dashboardData.statuses.interview,
                    backgroundColor: '#60a5fa',
                },
                {
                    label: 'Rejected',
                    data: window.dashboardData.statuses.rejected,
                    backgroundColor: '#f87171',
                },
                {
                    label: 'Accepted',
                    data: window.dashboardData.statuses.accepted,
                    backgroundColor: '#34d399',
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
});

// Application Sources Chart
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('applicationSourcesChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: window.dashboardData.sources, // Access data passed from PHP
            datasets: [
                {
                    data: window.dashboardData.source_counts,
                    backgroundColor: ['#2563eb', '#60a5fa', '#34d399', '#f87171'],
                },
            ],
        },
        options: {
            responsive: true,
        },
    });
});