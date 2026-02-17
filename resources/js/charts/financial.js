/**
 * Financial Report Charts — ApexCharts
 *
 * Modified to support separate Revenue & Transaction charts with client-side 1H/1M/1B/1T filtering.
 * Data passed from Blade via window.__financialData.
 */

import ApexCharts from 'apexcharts';

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

// ── Currency Formatter ──
function formatRupiah(val) {
    if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'M';
    if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'jt';
    if (val >= 1_000) return 'Rp ' + (val / 1_000).toFixed(0) + 'rb';
    return 'Rp ' + val;
}

function fullRupiah(val) {
    return 'Rp ' + Number(val).toLocaleString('id-ID');
}

// ── Period Logic (Shared with Dashboard) ──
const PERIOD_DAYS = { '1H': 1, '1M': 7, '1B': 30, '1T': 365 };
const PERIOD_LABELS = {
    '1H': 'Hari ini',
    '1M': '7 hari terakhir',
    '1B': '30 hari terakhir',
    '1T': '1 tahun terakhir',
};

// ── Variables ──
let revenueChart = null;
let ticketChart = null; // New separate tx chart
let paymentChart = null;
let sparkGrossChart = null;
let sparkTicketsChart = null;
let isInitializing = false; // Flag to prevent multiple simultaneous initializations

// Full 365-day data for filtering
let fullLabels = [];
let fullRevenue = [];
let fullTickets = [];

// Monthly data
let monthlyLabels = [];
let monthlyRevenue = [];
let monthlyTickets = [];

/**
 * Destroy all chart instances.
 */
export function destroyFinancialCharts() {
    [revenueChart, ticketChart, paymentChart, sparkGrossChart, sparkTicketsChart].forEach(c => {
        if (c) c.destroy();
    });
    revenueChart = ticketChart = paymentChart = sparkGrossChart = sparkTicketsChart = null;
}

/**
 * Initialize all financial charts from window.__financialData.
 */
export function initFinancialCharts() {
    // Prevent multiple simultaneous initializations
    if (isInitializing) {
        console.log('Financial charts initialization already in progress, skipping...');
        return;
    }

    isInitializing = true;

    // Ensure any existing charts are destroyed before re-initializing
    destroyFinancialCharts();

    const data = window.__financialData;
    if (!data) {
        isInitializing = false;
        return;
    }

    // Load 365-day data if available (fallback to daily arrays if not present)
    fullLabels = data.chartLabels || data.dailyLabels || [];
    fullRevenue = (data.chartRevenue || data.dailyRevenue || []).map(Number);
    fullTickets = (data.chartTickets || data.dailyCounts || []).map(Number);

    // Load monthly data
    monthlyLabels = data.monthlyLabels || [];
    monthlyRevenue = (data.monthlyRevenue || []).map(Number);
    monthlyTickets = (data.monthlyTickets || []).map(Number);

    console.log('Financial Charts Init:', {
        labelsLen: fullLabels.length,
        revLen: fullRevenue.length,
        txLen: fullTickets.length,
        monthlyLen: monthlyLabels.length
    });

    try {
        // 1. Revenue & Transaction Charts (Interactive)
        initRevenueChart();
        initTicketChart();

        // 2. Payment Method Distribution (Static based on date range)
        initPaymentChart(data);

        // 3. Sparklines (Static based on date range)
        initSparklines(data);
        
        console.log('Financial charts initialized successfully');
    } catch (error) {
        console.error('Error initializing financial charts:', error);
    } finally {
        // Reset flag after initialization completes
        setTimeout(() => {
            isInitializing = false;
        }, 100);
    }
}

// ── Helper: Slice Data ──
function sliceData(days) {
    const len = fullLabels.length;
    const start = Math.max(0, len - days);
    return {
        labels: fullLabels.slice(start),
        revenue: fullRevenue.slice(start),
        tickets: fullTickets.slice(start),
    };
}

// ── Helper: Build Area Options ──
function buildAreaOptions({ seriesName, data, labels, color, yFormatter, tooltipFormatter, height }) {
    return {
        series: [{ name: seriesName, data }],
        chart: {
            type: 'area', // or bar if preferred for monthly
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
        stroke: { curve: 'smooth', width: 2.5 },
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
//  1. Revenue Chart (Interactive)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function initRevenueChart() {
    const el = document.getElementById('revenueChart');
    if (!el) return;

    // Destroy existing chart before creating new one
    if (revenueChart instanceof ApexCharts) {
        revenueChart.destroy();
        revenueChart = null;
    }

    const slice = sliceData(30);
    const opts = buildAreaOptions({
        seriesName: 'Pendapatan',
        data: slice.revenue,
        labels: slice.labels,
        color: COLORS.blue,
        yFormatter: formatRupiah,
        tooltipFormatter: fullRupiah,
    });

    revenueChart = new ApexCharts(el, opts);
    revenueChart.render();
    updateRevenueStats(slice);
}

function updateRevenueStats(slice, isMonthly = false) {
    const total = slice.revenue.reduce((a, b) => a + b, 0);
    const count = slice.labels.length || 1;
    // For monthly view, slice.tickets is monthly totals.
    const txCount = slice.tickets.reduce((a, b) => a + b, 0);
    const max = Math.max(...slice.revenue);

    const el = (id) => document.getElementById(id);
    if (el('revTotal')) el('revTotal').textContent = formatRupiah(total);
    if (el('revAvg')) {
        el('revAvg').textContent = formatRupiah(Math.round(total / count));
        const avgLabel = el('revAvg').nextElementSibling;
        if (avgLabel) avgLabel.textContent = isMonthly ? 'Avg/Bulan' : 'Avg/Hari';
    }
    if (el('revTx')) el('revTx').textContent = txCount.toLocaleString('id-ID');
    if (el('revMax')) {
        el('revMax').textContent = formatRupiah(max);
        const maxLabel = el('revMax').nextElementSibling;
        if (maxLabel) maxLabel.textContent = isMonthly ? 'Bulan Tertinggi' : 'Tertinggi'; // kept 'Tertinggi' mostly
    }
}

window.filterFinancialRevenue = function (period) {
    if (!revenueChart) return;

    let slice;
    const isMonthly = period === '1T';

    if (isMonthly) {
        slice = {
            labels: monthlyLabels,
            revenue: monthlyRevenue,
            tickets: monthlyTickets
        };
    } else {
        const days = PERIOD_DAYS[period] || 30;
        slice = sliceData(days);
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
//  2. Ticket Chart (Interactive)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function initTicketChart() {
    const el = document.getElementById('ticketChart');
    if (!el) return;

    // Destroy existing chart before creating new one
    if (ticketChart instanceof ApexCharts) {
        ticketChart.destroy();
        ticketChart = null;
    }

    const slice = sliceData(30);
    const opts = buildAreaOptions({
        seriesName: 'Transaksi',
        data: slice.tickets,
        labels: slice.labels,
        color: COLORS.emerald,
        yFormatter: (v) => Math.round(v).toString(),
        tooltipFormatter: (v) => v.toLocaleString('id-ID') + ' transaksi',
    });

    ticketChart = new ApexCharts(el, opts);
    ticketChart.render();
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
        if (avgLabel) avgLabel.textContent = isMonthly ? 'Avg/Bulan' : 'Avg/Hari';
    }
    if (el('txDays')) {
        el('txDays').textContent = activeCount + '/' + count;
        const activeLabel = el('txDays').nextElementSibling;
        if (activeLabel) activeLabel.textContent = isMonthly ? 'Bulan Aktif' : 'Hari Aktif';
    }
    if (el('txMax')) {
        el('txMax').textContent = max.toLocaleString('id-ID');
        const maxLabel = el('txMax').nextElementSibling;
        if (maxLabel) maxLabel.textContent = isMonthly ? 'Bulan Tertinggi' : 'Tertinggi';
    }
}

window.filterFinancialTickets = function (period) {
    if (!ticketChart) return;

    let slice;
    const isMonthly = period === '1T';

    if (isMonthly) {
        slice = {
            labels: monthlyLabels,
            tickets: monthlyTickets,
            revenue: monthlyRevenue // just for completeness
        };
    } else {
        const days = PERIOD_DAYS[period] || 30;
        slice = sliceData(days);
    }

    ticketChart.updateOptions({
        series: [{ name: 'Transaksi', data: slice.tickets }],
        xaxis: { categories: slice.labels },
    }, true, true);

    updateTicketStats(slice, isMonthly);
    const label = document.getElementById('ticketPeriodLabel');
    if (label) label.textContent = PERIOD_LABELS[period] || '';
};

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  3. Payment Method Donut (Static)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function initPaymentChart(data) {
    const el = document.getElementById('paymentMethodChart');
    if (!el || !data.paymentLabels || data.paymentLabels.length === 0) return;

    // Destroy existing chart before creating new one
    if (paymentChart instanceof ApexCharts) {
        paymentChart.destroy();
        paymentChart = null;
    }

    const paymentNumbers = (data.paymentData || []).map(Number);

    const options = {
        series: paymentNumbers,
        labels: data.paymentLabels,
        chart: {
            type: 'donut',
            height: 220,
            fontFamily: "'Inter', 'Segoe UI', sans-serif",
            animations: { enabled: true },
        },
        colors: CHART_PALETTE.slice(0, paymentNumbers.length),
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '12px', fontWeight: 600, color: '#6b7280', offsetY: -4 },
                        value: { show: true, fontSize: '18px', fontWeight: 800, color: '#1f2937', offsetY: 4, formatter: fullRupiah },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total',
                            fontSize: '12px',
                            fontWeight: 600,
                            color: '#6b7280',
                            formatter: (w) => {
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return fullRupiah(total);
                            }
                        }
                    }
                }
            }
        },
        stroke: { width: 2, colors: ['#fff'] },
        dataLabels: { enabled: false },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '12px' },
            y: { formatter: fullRupiah },
        },
        legend: { show: false },
    };

    paymentChart = new ApexCharts(el, options);
    paymentChart.render();
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  4. Sparklines (Static)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function createSparkline(elId, seriesData, color) {
    const el = document.getElementById(elId);
    if (!el || !seriesData || seriesData.length < 2) return null;

    const opts = {
        series: [{ data: seriesData }],
        chart: {
            type: 'area',
            height: 36,
            sparkline: { enabled: true },
            animations: { enabled: true },
        },
        stroke: { curve: 'smooth', width: 2 },
        colors: [color],
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] },
        },
        tooltip: { enabled: false },
    };

    const chart = new ApexCharts(el, opts);
    chart.render();
    return chart;
}

function initSparklines(data) {
    // Destroy existing sparkline charts before creating new ones
    if (sparkGrossChart instanceof ApexCharts) {
        sparkGrossChart.destroy();
        sparkGrossChart = null;
    }
    if (sparkTicketsChart instanceof ApexCharts) {
        sparkTicketsChart.destroy();
        sparkTicketsChart = null;
    }

    const sparkRev = (data.sparkRevenue || []).map(Number);
    const sparkTix = (data.sparkTickets || []).map(Number);
    sparkGrossChart = createSparkline('sparkGross', sparkRev, COLORS.blue);
    sparkTicketsChart = createSparkline('sparkTickets', sparkTix, COLORS.violet);
}

// ── Auto-init ──
function boot(retryCount = 0) {
    // Check if we're on the financial report page
    const isFinancialPage = document.getElementById('revenueChart') ||
        document.getElementById('ticketChart') ||
        document.getElementById('paymentMethodChart');

    if (!isFinancialPage) {
        return; // Not on financial page, skip initialization
    }

    if (window.__financialData) {
        initFinancialCharts();
    } else if (retryCount < 10) {
        // Retry if data not ready yet
        setTimeout(() => {
            boot(retryCount + 1);
        }, 100);
    } else {
        console.warn('Financial dashboard data not found after retries');
    }
}

// Helper function to try initialization
const tryInitFinancial = () => {
    setTimeout(() => {
        boot();
    }, 50);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', tryInitFinancial);
} else {
    tryInitFinancial();
}

// Livewire SPA Support
let navigationTimeout = null;
document.addEventListener('livewire:navigated', () => {
    // Clear any pending initialization
    if (navigationTimeout) {
        clearTimeout(navigationTimeout);
    }
    
    // Destroy any existing charts first
    destroyFinancialCharts();
    isInitializing = false; // Reset flag
    
    // Wait a bit for the new page's scripts to execute
    navigationTimeout = setTimeout(() => {
        boot();
        navigationTimeout = null;
    }, 150);
});

document.addEventListener('livewire:navigating', () => {
    // Clear any pending initialization
    if (navigationTimeout) {
        clearTimeout(navigationTimeout);
        navigationTimeout = null;
    }
    
    // Cleanup old charts before navigating away
    destroyFinancialCharts();
    isInitializing = false; // Reset flag
});

// Listen for data-ready event (dispatched from financial report blade)
let dataReadyTimeout = null;
document.addEventListener('financial-dashboard-data-ready', () => {
    // Clear any pending initialization
    if (dataReadyTimeout) {
        clearTimeout(dataReadyTimeout);
    }
    
    // Prevent duplicate calls
    dataReadyTimeout = setTimeout(() => {
        tryInitFinancial();
        dataReadyTimeout = null;
    }, 100);
});
