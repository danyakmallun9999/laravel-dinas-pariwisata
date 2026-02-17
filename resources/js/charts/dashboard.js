import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts; // Expose globally for peace of mind

// ── Shared Palette ──
// ... (rest of the file constants, formatters, etc.)

// ── Shared Palette ──
const COLORS = {
    blue: '#3b82f6',
    emerald: '#10b981',
    amber: '#f59e0b',
    red: '#ef4444',
    violet: '#8b5cf6',
    pink: '#ec4899',
    teal: '#14b8a6',
};
const CHART_PALETTE = [COLORS.blue, COLORS.emerald, COLORS.amber, COLORS.red, COLORS.violet, COLORS.pink, COLORS.teal];

// ── Currency Formatters ──
function formatRupiah(val) {
    if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'M';
    if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'jt';
    if (val >= 1_000) return 'Rp ' + (val / 1_000).toFixed(0) + 'rb';
    return 'Rp ' + val;
}

function fullRupiah(val) {
    return 'Rp ' + Number(val).toLocaleString('id-ID');
}

// ── Period days mapping ──
const PERIOD_DAYS = { '1H': 1, '1M': 7, '1B': 30, '1T': 365 };
const PERIOD_LABELS = {
    '1H': 'Hari ini',
    '1M': '7 hari terakhir',
    '1B': '30 hari terakhir',
    '1T': '1 tahun terakhir',
};

// ── Chart instances ──
let revenueChart = null;
let ticketChart = null;
let ticketTypeChart = null;
let isBooting = false;

// ── Full data (365 days) ──
let fullLabels = [];
let fullRevenue = [];
let fullTickets = [];

// ── Monthly data (12 months) ──
let monthlyLabels = [];
let monthlyRevenue = [];
let monthlyTickets = [];

/**
 * Slice the last N days from full arrays.
 */
function sliceData(days) {
    const len = fullLabels.length;
    const start = Math.max(0, len - days);
    return {
        labels: fullLabels.slice(start),
        revenue: fullRevenue.slice(start),
        tickets: fullTickets.slice(start),
    };
}

/**
 * Build common chart options for an area chart.
 */
function buildAreaOptions({ seriesName, data, labels, color, yFormatter, tooltipFormatter, height }) {
    return {
        series: [{ name: seriesName, data }],
        chart: {
            type: 'area',
            height: height || 260,
            fontFamily: "'Inter', 'Segoe UI', sans-serif",
            toolbar: {
                show: true,
                tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false },
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 700,
                animateGradually: { enabled: true, delay: 80 },
                dynamicAnimation: { enabled: true, speed: 350 },
            },
            dropShadow: {
                enabled: true,
                top: 2,
                left: 0,
                blur: 5,
                color: color,
                opacity: 0.1,
            },
        },
        colors: [color],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 85, 100],
            },
        },
        stroke: {
            curve: 'smooth',
            width: 2.5,
        },
        markers: {
            size: 0,
            hover: { size: 5, sizeOffset: 3 },
            strokeWidth: 3,
            strokeColors: '#fff',
        },
        xaxis: {
            categories: labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            tickAmount: 15,
            labels: {
                style: { colors: '#9ca3af', fontSize: '10px' },
                rotate: -90,
                hideOverlappingLabels: true,
            },
        },
        yaxis: {
            title: { text: '' },
            labels: {
                style: { colors: '#9ca3af', fontSize: '10px' },
                formatter: yFormatter,
            },
            min: 0,
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { top: -8, right: 0, bottom: 0, left: 6 },
        },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '12px' },
            y: { formatter: tooltipFormatter },
        },
        legend: { show: false },
        dataLabels: { enabled: false },
    };
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  1. Revenue Chart
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function safeRender(chart, el) {
    if (!chart || !el) return;
    if (el.offsetWidth > 0 || el.offsetHeight > 0) {
        chart.render().catch(() => {
            setTimeout(() => safeRender(chart, el), 200);
        });
    } else {
        setTimeout(() => safeRender(chart, el), 200);
    }
}

function initRevenueChart() {
    const el = document.getElementById('ticketRevenueChart');
    if (!el) return;

    el.innerHTML = '';
    const slice = sliceData(30);
    const opts = buildAreaOptions({
        seriesName: 'Pendapatan',
        data: slice.revenue,
        labels: slice.labels,
        color: COLORS.blue,
        yFormatter: formatRupiah,
        tooltipFormatter: (val) => fullRupiah(val),
    });

    revenueChart = new ApexCharts(el, opts);
    safeRender(revenueChart, el);
    updateRevenueStats(slice);
}

function updateRevenueStats(slice, isMonthly = false) {
    const total = slice.revenue.reduce((a, b) => a + b, 0);
    const count = slice.labels.length || 1;
    const txCount = slice.tickets.reduce((a, b) => a + b, 0);
    const max = Math.max(...slice.revenue);

    const el = (id) => document.getElementById(id);
    if (el('revTotal')) el('revTotal').textContent = formatRupiah(total);
    if (el('revAvg')) {
        el('revAvg').textContent = formatRupiah(Math.round(total / count));
        const avgLabel = el('revAvg').nextElementSibling;
        if (avgLabel) avgLabel.textContent = isMonthly ? 'Rata-rata/Bulan' : 'Rata-rata/Hari';
    }
    if (el('revTx')) el('revTx').textContent = txCount.toLocaleString('id-ID');
    if (el('revMax')) {
        el('revMax').textContent = formatRupiah(max);
        const maxLabel = el('revMax').nextElementSibling;
        if (maxLabel) maxLabel.textContent = isMonthly ? 'Bulan Tertinggi' : 'Hari Tertinggi';
    }
}

window.filterRevenueChart = function (period) {
    if (!revenueChart) return;
    let slice;
    const isMonthly = period === '1T';
    if (isMonthly) {
        slice = { labels: monthlyLabels, revenue: monthlyRevenue, tickets: monthlyTickets };
    } else {
        slice = sliceData(PERIOD_DAYS[period] || 30);
    }
    revenueChart.updateOptions({
        series: [{ name: 'Pendapatan', data: slice.revenue }],
        xaxis: { categories: slice.labels },
    }, true, true);
    updateRevenueStats(slice, isMonthly);
    const label = document.getElementById('revenuePeriodLabel');
    if (label) label.textContent = PERIOD_LABELS[period] || '';
};

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  2. Ticket (Transaction) Chart
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function initTicketChart() {
    const el = document.getElementById('ticketTransactionChart');
    if (!el) return;

    el.innerHTML = '';
    const slice = sliceData(30);
    const opts = buildAreaOptions({
        seriesName: 'Tiket Terjual',
        data: slice.tickets,
        labels: slice.labels,
        color: COLORS.emerald,
        yFormatter: (v) => Math.round(v).toString(),
        tooltipFormatter: (val) => val.toLocaleString('id-ID') + ' tiket',
    });

    ticketChart = new ApexCharts(el, opts);
    safeRender(ticketChart, el);
    updateTicketStats(slice);
}

function updateTicketStats(slice, isMonthly = false) {
    const total = slice.tickets.reduce((a, b) => a + b, 0);
    const count = slice.labels.length || 1;
    const activeCount = slice.tickets.filter(v => v > 0).length;
    const max = Math.max(...slice.tickets);

    const el = (id) => document.getElementById(id);
    if (el('txTotal')) el('txTotal').textContent = total.toLocaleString('id-ID');
    if (el('txAvg')) {
        el('txAvg').textContent = Math.round(total / count).toLocaleString('id-ID');
        const avgLabel = el('txAvg').nextElementSibling;
        if (avgLabel) avgLabel.textContent = isMonthly ? 'Rata-rata/Bulan' : 'Rata-rata/Hari';
    }
    if (el('txDays')) {
        el('txDays').textContent = activeCount + '/' + count;
        const activeLabel = el('txDays').nextElementSibling;
        if (activeLabel) activeLabel.textContent = isMonthly ? 'Bulan Aktif' : 'Hari Aktif';
    }
    if (el('txMax')) {
        el('txMax').textContent = max.toLocaleString('id-ID');
        const maxLabel = el('txMax').nextElementSibling;
        if (maxLabel) maxLabel.textContent = isMonthly ? 'Bulan Tertinggi' : 'Hari Tertinggi';
    }
}

window.filterTicketChart = function (period) {
    if (!ticketChart) return;
    let slice;
    const isMonthly = period === '1T';
    if (isMonthly) {
        slice = { labels: monthlyLabels, tickets: monthlyTickets, revenue: monthlyRevenue };
    } else {
        slice = sliceData(PERIOD_DAYS[period] || 30);
    }
    ticketChart.updateOptions({
        series: [{ name: 'Tiket Terjual', data: slice.tickets }],
        xaxis: { categories: slice.labels },
    }, true, true);
    updateTicketStats(slice, isMonthly);
    const label = document.getElementById('ticketPeriodLabel');
    if (label) label.textContent = PERIOD_LABELS[period] || '';
};

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  3. Ticket Type — Donut Chart
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function initTicketTypeChart(data) {
    const el = document.getElementById('ticketTypeDonutChart');
    if (!el || !data.ticketTypeLabels || data.ticketTypeLabels.length === 0) return;

    el.innerHTML = '';
    const seriesData = (data.ticketTypeData || []).map(Number);
    const options = {
        series: seriesData,
        labels: data.ticketTypeLabels,
        chart: {
            type: 'donut',
            height: 220,
            fontFamily: "'Inter', 'Segoe UI', sans-serif",
            animations: { enabled: true, easing: 'easeinout', speed: 800 },
        },
        colors: CHART_PALETTE.slice(0, seriesData.length),
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '12px', fontWeight: 600, color: '#6b7280', offsetY: -4 },
                        value: { show: true, fontSize: '20px', fontWeight: 800, color: '#1f2937', offsetY: 4, formatter: (val) => Number(val).toLocaleString('id-ID') },
                        total: { show: true, showAlways: true, label: 'Total', fontSize: '12px', fontWeight: 600, color: '#6b7280', formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('id-ID') }
                    }
                }
            }
        },
        stroke: { width: 2, colors: ['#fff'] },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark', style: { fontSize: '12px' }, y: { formatter: (val) => val.toLocaleString('id-ID') + ' tiket' } },
        legend: { show: false },
    };

    ticketTypeChart = new ApexCharts(el, options);
    safeRender(ticketTypeChart, el);
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  Init / Boot
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

export function destroyDashboardCharts() {
    [revenueChart, ticketChart, ticketTypeChart].forEach(c => {
        if (c && typeof c.destroy === 'function') {
            try { c.destroy(); } catch (e) { }
        }
    });
    revenueChart = ticketChart = ticketTypeChart = null;
}

export function initDashboardCharts() {
    const data = window.__dashboardData;
    if (!data) return;

    try {
        destroyDashboardCharts();
        fullLabels = data.labels || [];
        fullRevenue = (data.revenue || []).map(Number);
        fullTickets = (data.tickets || []).map(Number);
        monthlyLabels = data.monthlyLabels || [];
        monthlyRevenue = (data.monthlyRevenue || []).map(Number);
        monthlyTickets = (data.monthlyTickets || []).map(Number);

        initRevenueChart();
        initTicketChart();
        initTicketTypeChart(data);
    } catch (e) {
        console.error('Failed to initialize dashboard charts:', e);
    }
}

let pollInterval = null;

function boot(retryCount = 0) {
    const elCheck = document.getElementById('ticketRevenueChart');
    if (!elCheck) {
        if (retryCount < 5) setTimeout(() => boot(retryCount + 1), 200);
        return;
    }

    if (window.__dashboardData) {
        initDashboardCharts();
        startPolling();
    } else if (retryCount < 15) {
        setTimeout(() => boot(retryCount + 1), 200);
    }
}

function startPolling() {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(() => {
        if (document.getElementById('ticketRevenueChart')) {
            initDashboardCharts();
        } else {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }, 60000);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => boot());
} else {
    boot();
}

document.addEventListener('livewire:navigated', () => {
    destroyDashboardCharts();
    setTimeout(() => boot(), 150);
});

document.addEventListener('ticket-dashboard-data-ready', () => boot());

document.addEventListener('livewire:navigating', () => {
    destroyDashboardCharts();
    if (pollInterval) clearInterval(pollInterval);
});



