import ApexCharts from 'apexcharts';

// Initialize Charts using ApexCharts
const initAdminDashboard = (retryCount = 0) => {
    // Check if we're on the dashboard page
    const revenueEl = document.getElementById('revenueTrendChart');
    const postViewsEl = document.getElementById('postViewsChart');
    const categoriesEl = document.getElementById('categoriesChart');

    const isDashboardPage = revenueEl || postViewsEl || categoriesEl;

    if (!isDashboardPage) {
        return; // Not on dashboard, skip initialization
    }

    const data = window.__adminDashboardData;

    // If data is not ready yet, retry after a short delay (max 20 retries for Livewire v4)
    if (!data && retryCount < 20) {
        setTimeout(() => {
            initAdminDashboard(retryCount + 1);
        }, 150);
        return;
    }

    if (!data) {
        console.warn('No admin dashboard data found after retries. Data:', window.__adminDashboardData);
        return;
    }

    // Validate data structure
    if (!data.revenue || !data.postViews || !data.categories) {
        console.warn('Admin dashboard data structure incomplete:', data);
        if (retryCount < 10) {
            setTimeout(() => {
                initAdminDashboard(retryCount + 1);
            }, 200);
            return;
        }
    }

    // Common options
    const commonOptions = {
        chart: {
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } }
        },
        xaxis: {
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#64748b', fontSize: '12px' }
            }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '12px' }
            }
        },
        legend: { show: false },
        tooltip: {
            theme: 'dark',
            x: { show: false }
        }
    };

    // 1. Revenue Trend Chart
    // 1. Revenue Trend Chart
    // reused revenueEl from top of function
    if (revenueEl) {
        console.log('Initializing Revenue Trend Chart...', {
            element: revenueEl,
            data: data.revenue,
            dataLength: data.revenue?.data?.length || 0
        });

        if (window.revenueChart instanceof ApexCharts) {
            window.revenueChart.destroy();
        }

        // Ensure data is array
        const revenueData = Array.isArray(data.revenue.data) ? data.revenue.data : [];
        const revenueLabels = Array.isArray(data.revenue.labels) ? data.revenue.labels : [];

        const options = {
            ...commonOptions,
            series: [{
                name: 'Pendapatan',
                data: revenueData
            }],
            chart: {
                ...commonOptions.chart,
                type: 'area', // Area chart looks better for revenue
                height: 300
            },
            colors: ['#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            xaxis: {
                ...commonOptions.xaxis,
                categories: revenueLabels
            },
            yaxis: {
                ...commonOptions.yaxis,
                labels: {
                    formatter: (value) => {
                        if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt';
                        if (value >= 1000) return 'Rp ' + (value / 1000) + 'rb';
                        return 'Rp ' + value;
                    }
                }
            },
            tooltip: {
                ...commonOptions.tooltip,
                y: {
                    formatter: function (value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        };

        try {
            window.revenueChart = new ApexCharts(revenueEl, options);
            window.revenueChart.render().then(() => {
                console.log('Revenue Trend Chart rendered successfully');
            }).catch(err => {
                console.error('Error rendering Revenue Trend Chart:', err);
            });
        } catch (error) {
            console.error('Error creating Revenue Trend Chart:', error);
        }
    } else {
        console.warn('Revenue Trend Chart element not found');
    }

    // 2. Post Views Chart
    if (postViewsEl) {
        console.log('Initializing Post Views Chart...', {
            element: postViewsEl,
            data: data.postViews,
            dataLength: data.postViews?.data?.length || 0
        });

        if (window.postViewsChart instanceof ApexCharts) {
            window.postViewsChart.destroy();
        }

        // Ensure data is array
        const postViewsData = Array.isArray(data.postViews.data) ? data.postViews.data : [];
        const postViewsLabels = Array.isArray(data.postViews.labels) ? data.postViews.labels : [];

        const options = {
            ...commonOptions,
            series: [{
                name: 'Pembaca',
                data: postViewsData
            }],
            chart: {
                ...commonOptions.chart,
                type: 'area',
                height: 300
            },
            colors: ['#8b5cf6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            xaxis: {
                ...commonOptions.xaxis,
                categories: postViewsLabels
            },
            yaxis: {
                ...commonOptions.yaxis,
                labels: {
                    formatter: (value) => value.toFixed(0)
                }
            }
        };

        try {
            window.postViewsChart = new ApexCharts(postViewsEl, options);
            window.postViewsChart.render().then(() => {
                console.log('Post Views Chart rendered successfully');
            }).catch(err => {
                console.error('Error rendering Post Views Chart:', err);
            });
        } catch (error) {
            console.error('Error creating Post Views Chart:', error);
        }
    } else {
        console.warn('Post Views Chart element not found');
    }

    // 3. Categories Distribution Chart
    if (categoriesEl && data.categories) {
        console.log('Initializing Categories Chart...', {
            element: categoriesEl,
            data: data.categories,
            countsLength: data.categories?.counts?.length || 0
        });

        if (window.categoriesChart instanceof ApexCharts) {
            window.categoriesChart.destroy();
        }

        const colors = Array.isArray(data.categories.colors) ? data.categories.colors : ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
        const categoryCounts = Array.isArray(data.categories.counts) ? data.categories.counts : [];
        const categoryNames = Array.isArray(data.categories.names) ? data.categories.names : [];

        const options = {
            series: categoryCounts,
            chart: {
                type: 'donut',
                height: 250,
                fontFamily: 'Inter, sans-serif',
                animations: { enabled: false } // Disable animation for donut to avoid resize issues
            },
            labels: categoryNames,
            colors: colors,
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: false, // We have a custom center label in HTML
                        }
                    }
                }
            },
            dataLabels: { enabled: false }, // Clean look
            stroke: { width: 0 },
            legend: { show: false }, // Custom legend in HTML
            tooltip: {
                enabled: true,
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val + ' Destinasi';
                    }
                }
            }
        };

        try {
            window.categoriesChart = new ApexCharts(categoriesEl, options);
            window.categoriesChart.render().then(() => {
                console.log('Categories Chart rendered successfully');
            }).catch(err => {
                console.error('Error rendering Categories Chart:', err);
            });
        } catch (error) {
            console.error('Error creating Categories Chart:', error);
        }
    } else {
        console.warn('Categories Chart element not found or data missing');
    }

    console.log('Admin Dashboard Charts initialization completed');
};

const destroyAdminDashboard = () => {
    if (window.revenueChart instanceof ApexCharts) {
        window.revenueChart.destroy();
        window.revenueChart = null;
    }
    if (window.postViewsChart instanceof ApexCharts) {
        window.postViewsChart.destroy();
        window.postViewsChart = null;
    }
    if (window.categoriesChart instanceof ApexCharts) {
        window.categoriesChart.destroy();
        window.categoriesChart = null;
    }
};

// Global variables to hold chart instances
window.revenueChart = null;
window.postViewsChart = null;
window.categoriesChart = null;

// Helper function to check if we're on dashboard and initialize
const tryInitDashboard = () => {
    // Small delay to ensure DOM is ready and scripts have executed
    setTimeout(() => {
        initAdminDashboard();
    }, 100);
};

// MutationObserver untuk memastikan elemen chart sudah ada di DOM (Livewire v4 compatible)
let chartObserver = null;

const observeForCharts = () => {
    // Hentikan observer lama jika ada
    if (chartObserver) {
        chartObserver.disconnect();
    }

    // Cek apakah elemen chart sudah ada
    const revenueEl = document.getElementById('revenueTrendChart');
    const postViewsEl = document.getElementById('postViewsChart');
    const categoriesEl = document.getElementById('categoriesChart');

    if (revenueEl || postViewsEl || categoriesEl) {
        // Elemen sudah ada, coba init
        tryInitDashboard();
    } else {
        // Gunakan MutationObserver untuk menunggu elemen muncul
        chartObserver = new MutationObserver((mutations, observer) => {
            const hasRevenue = document.getElementById('revenueTrendChart');
            const hasPostViews = document.getElementById('postViewsChart');
            const hasCategories = document.getElementById('categoriesChart');

            if (hasRevenue || hasPostViews || hasCategories) {
                observer.disconnect();
                chartObserver = null;
                // Tunggu sedikit untuk memastikan script data sudah dieksekusi
                setTimeout(() => {
                    tryInitDashboard();
                }, 200);
            }
        });

        // Observe perubahan pada body
        chartObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
};

// Event Listeners untuk berbagai skenario
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        observeForCharts();
    });
} else {
    observeForCharts();
}

// Handle Livewire v4 SPA navigation - multiple event listeners untuk kompatibilitas
document.addEventListener('livewire:navigated', () => {
    // Destroy any existing charts first
    destroyAdminDashboard();
    // Reset observer dan tunggu DOM baru
    setTimeout(() => {
        observeForCharts();
    }, 150);
});

// Event untuk Livewire v4 (alternatif)
document.addEventListener('livewire:load', () => {
    setTimeout(() => {
        observeForCharts();
    }, 100);
});

document.addEventListener('livewire:navigating', () => {
    // Cleanup charts before navigating away
    destroyAdminDashboard();
    if (chartObserver) {
        chartObserver.disconnect();
        chartObserver = null;
    }
});

// Listen for data-ready event (dispatched from dashboard blade)
document.addEventListener('admin-dashboard-data-ready', () => {
    setTimeout(() => {
        tryInitDashboard();
    }, 50);
});

// Fallback: Check periodically if we're on dashboard page (untuk kasus edge case)
let checkInterval = null;
const startPeriodicCheck = () => {
    if (checkInterval) return;

    checkInterval = setInterval(() => {
        const isDashboard = document.getElementById('revenueTrendChart') ||
            document.getElementById('postViewsChart') ||
            document.getElementById('categoriesChart');

        if (isDashboard && window.__adminDashboardData) {
            const hasCharts = window.revenueChart || window.postViewsChart || window.categoriesChart;
            if (!hasCharts) {
                console.log('Periodic check: Initializing charts...');
                tryInitDashboard();
            }
        }
    }, 2000);
};

// Start periodic check setelah delay
setTimeout(() => {
    startPeriodicCheck();
}, 1000);
