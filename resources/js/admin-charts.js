// Chart.js initialization for Admin Dashboard and Reports
window.initAdminCharts = function(salesData = [], statusData = []) {
  if (typeof Chart === 'undefined') return;

  // 1. Sales Trend Line Chart
  const salesCanvas = document.getElementById('salesTrendChart');
  if (salesCanvas) {
    const labels = salesData.map(d => d.sale_date);
    const amounts = salesData.map(d => parseFloat(d.total_amount));

    new Chart(salesCanvas, {
      type: 'line',
      data: {
        labels: labels.length ? labels : ['Today'],
        datasets: [{
          label: 'Revenue (₹)',
          data: amounts.length ? amounts : [0],
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22, 163, 74, 0.1)',
          fill: true,
          tension: 0.35,
          borderWidth: 3,
          pointBackgroundColor: '#15803d',
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: value => '₹' + value
            }
          }
        }
      }
    });
  }

  // 2. Order Status Doughnut Chart
  const statusCanvas = document.getElementById('orderStatusChart');
  if (statusCanvas && statusData.length) {
    const statusLabels = statusData.map(d => d.fulfillment_status.replace(/_/g, ' ').toUpperCase());
    const statusCounts = statusData.map(d => parseInt(d.count));

    new Chart(statusCanvas, {
      type: 'doughnut',
      data: {
        labels: statusLabels,
        datasets: [{
          data: statusCounts,
          backgroundColor: [
            '#f59e0b', // pending
            '#3b82f6', // confirmed
            '#8b5cf6', // preparing
            '#06b6d4', // out_for_delivery
            '#16a34a', // delivered
            '#ef4444'  // cancelled
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }
};
