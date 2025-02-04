document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('sums-seo-chart').getContext('2d');
    const chartData = JSON.parse(document.getElementById('sums-seo-chart-data').textContent);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Issues Found',
                data: chartData.issues_found,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Issues Solved',
                data: chartData.issues_solved,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});