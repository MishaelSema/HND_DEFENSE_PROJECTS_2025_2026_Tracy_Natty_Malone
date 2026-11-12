$current_page = 'analytics';
include 'components/layout_start.php';
?>

<div class="dashboard-header">
    <h1>Analytics</h1>
    <p class="subtitle">Track your shop performance and insights</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-orange">
            <i data-lucide="trending-up"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Conversion Rate</span>
            <div class="stat-value">3.2% <small>vs last week</small></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-blue">
            <i data-lucide="users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Repeat Customers</span>
            <div class="stat-value">18% <small>vs last week</small></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-green">
            <i data-lucide="shopping-bag"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Avg. Order Value</span>
            <div class="stat-value">47,500 XAF <small>Average per transaction</small></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-red">
            <i data-lucide="eye"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Page Views</span>
            <div class="stat-value">3,847 <small>This month</small></div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-row">
    <div class="chart-container large">
        <div class="chart-header">
            <h3>Monthly Revenue</h3>
            <span>Insights over latest 6 months</span>
        </div>
        <canvas id="revenueChart"></canvas>
    </div>
    
    <div class="chart-container small">
        <div class="chart-header">
            <h3>Sales by Category</h3>
            <span>Most popular categories</span>
        </div>
        <div class="chart-wrapper">
            <canvas id="categoryChart"></canvas>
        </div>
        <div class="chart-legend" id="categoryLegend"></div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-orange { background: #FFF4E5; color: #D35400; }
.icon-blue { background: #EBF5FF; color: #0066CC; }
.icon-green { background: #E9F7EF; color: #188038; }
.icon-red { background: #FDECEC; color: #C62828; }

.stat-label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
}

.stat-value small {
    display: block;
    font-size: 0.75rem;
    font-weight: 400;
    color: var(--text-muted);
}

.charts-row {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.chart-container {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    flex: 1;
    min-width: 300px;
}

.chart-container.large { flex: 2; }

.chart-header { margin-bottom: 1.5rem; }
.chart-header h3 { font-size: 1.1rem; margin-bottom: 2px; }
.chart-header span { font-size: 0.85rem; color: var(--text-muted); }

.chart-wrapper {
    position: relative;
    max-width: 250px;
    margin: 0 auto;
}

#categoryLegend {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    font-size: 0.8rem;
}

.legend-item { display: flex; align-items: center; gap: 0.5rem; }
.legend-dot { width: 8px; height: 8px; border-radius: 50%; }

@media (max-width: 768px) {
    .charts-row { flex-direction: column; }
    .chart-container { width: 100%; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 19000, 15000, 25000, 32000, 48000],
                backgroundColor: '#D35400',
                borderRadius: 8,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Category Chart
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    const catData = {
        labels: ['Clothing', 'Jewelry', 'Home Decor', 'Art'],
        datasets: [{
            data: [45, 25, 20, 10],
            backgroundColor: ['#D35400', '#F1C40F', '#2D1F1A', '#E8E2DE'],
            borderWidth: 0,
            cutout: '70%'
        }]
    };
    new Chart(catCtx, {
        type: 'doughnut',
        data: catData,
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
    
    // Custom Legend
    const legendEl = document.getElementById('categoryLegend');
    catData.labels.forEach((label, i) => {
        const item = document.createElement('div');
        item.className = 'legend-item';
        item.innerHTML = `
            <span class="legend-dot" style="background: ${catData.datasets[0].backgroundColor[i]}"></span>
            <span>${label} (${catData.datasets[0].data[i]}%)</span>
        `;
        legendEl.appendChild(item);
    });
</script>

<?php include 'components/layout_end.php'; ?>
