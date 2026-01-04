/**
 * 📊 MultiTienda Pro - Sistema de Analytics Enterprise
 * Gráficas interactivas y métricas profesionales
 */

class AnalyticsEngine {
    constructor() {
        this.charts = new Map();
        this.colors = {
            primary: ['#0ea5e9', '#0284c7', '#0369a1'],
            success: ['#22c55e', '#16a34a', '#15803d'],
            warning: ['#f59e0b', '#d97706', '#b45309'],
            purple: ['#a855f7', '#9333ea', '#7c3aed'],
            gradient: {
                primary: 'linear-gradient(135deg, #0ea5e9 0%, #a855f7 100%)',
                success: 'linear-gradient(135deg, #22c55e 0%, #0ea5e9 100%)',
                warning: 'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)'
            }
        };
        this.animations = {
            duration: 2000,
            easing: 'easeOutQuart'
        };
    }

    // Crear gráfica de ventas mensuales
    createSalesChart(canvasId, salesData) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(14, 165, 233, 0.8)');
        gradient.addColorStop(1, 'rgba(14, 165, 233, 0.1)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.labels || ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Ventas ($)',
                    data: salesData.values || [1200, 1900, 3000, 5000, 2000, 3000],
                    backgroundColor: gradient,
                    borderColor: this.colors.primary[0],
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: this.colors.primary[0],
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: this.colors.primary[0],
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: this.getBaseChartOptions({
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Ventas: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            })
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Crear gráfica de productos por categoría
    createCategoryChart(canvasId, categoryData) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categoryData.labels || ['Electrónicos', 'Ropa', 'Hogar', 'Libros', 'Deportes'],
                datasets: [{
                    data: categoryData.values || [30, 25, 20, 15, 10],
                    backgroundColor: [
                        this.colors.primary[0],
                        this.colors.success[0],
                        this.colors.warning[0],
                        this.colors.purple[0],
                        '#64748b'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    hoverBorderWidth: 6,
                    hoverOffset: 15
                }]
            },
            options: this.getBaseChartOptions({
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            })
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Crear gráfica de rendimiento de tiendas
    createStorePerformanceChart(canvasId, storeData) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: storeData.labels || ['TechStore', 'FashionHub', 'HomeDecor', 'BookCorner'],
                datasets: [{
                    label: 'Ventas',
                    data: storeData.sales || [12000, 8500, 6000, 4500],
                    backgroundColor: this.colors.primary[0],
                    borderRadius: 8,
                    borderSkipped: false,
                }, {
                    label: 'Pedidos',
                    data: storeData.orders || [150, 120, 80, 65],
                    backgroundColor: this.colors.success[0],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: this.getBaseChartOptions({
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end'
                    }
                }
            })
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Crear gráfica de actividad de usuarios
    createUserActivityChart(canvasId, activityData) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, 'rgba(34, 197, 94, 0.8)');
        gradient1.addColorStop(1, 'rgba(34, 197, 94, 0.1)');

        const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(168, 85, 247, 0.8)');
        gradient2.addColorStop(1, 'rgba(168, 85, 247, 0.1)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: activityData.labels || ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                datasets: [{
                    label: 'Usuarios Activos',
                    data: activityData.activeUsers || [20, 5, 15, 45, 60, 80, 35],
                    backgroundColor: gradient1,
                    borderColor: this.colors.success[0],
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Nuevos Registros',
                    data: activityData.newUsers || [2, 1, 3, 8, 12, 15, 7],
                    backgroundColor: gradient2,
                    borderColor: this.colors.purple[0],
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: this.getBaseChartOptions({
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            })
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Configuraciones base para todas las gráficas
    getBaseChartOptions(customOptions = {}) {
        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: this.animations,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Inter, sans-serif',
                            size: 13,
                            weight: '500'
                        },
                        color: '#374151',
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 15,
                    titleFont: {
                        family: 'Inter, sans-serif',
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        family: 'Inter, sans-serif',
                        size: 13
                    },
                    displayColors: true,
                    boxWidth: 12,
                    boxHeight: 12,
                    usePointStyle: true
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        font: {
                            family: 'Inter, sans-serif',
                            size: 12,
                            weight: '500'
                        },
                        color: '#6b7280'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        font: {
                            family: 'Inter, sans-serif',
                            size: 12,
                            weight: '500'
                        },
                        color: '#6b7280'
                    }
                }
            }
        };

        return this.deepMerge(baseOptions, customOptions);
    }

    // Función auxiliar para combinar objetos profundamente
    deepMerge(target, source) {
        const output = Object.assign({}, target);
        if (this.isObject(target) && this.isObject(source)) {
            Object.keys(source).forEach(key => {
                if (this.isObject(source[key])) {
                    if (!(key in target))
                        Object.assign(output, { [key]: source[key] });
                    else
                        output[key] = this.deepMerge(target[key], source[key]);
                } else {
                    Object.assign(output, { [key]: source[key] });
                }
            });
        }
        return output;
    }

    isObject(item) {
        return (item && typeof item === 'object' && !Array.isArray(item));
    }

    // Actualizar datos de gráfica existente
    updateChartData(chartId, newData) {
        const chart = this.charts.get(chartId);
        if (chart) {
            chart.data = newData;
            chart.update('active');
        }
    }

    // Destruir gráfica
    destroyChart(chartId) {
        const chart = this.charts.get(chartId);
        if (chart) {
            chart.destroy();
            this.charts.delete(chartId);
        }
    }

    // Destruir todas las gráficas
    destroyAllCharts() {
        this.charts.forEach((chart, id) => {
            chart.destroy();
        });
        this.charts.clear();
    }

    // Generar datos de prueba para desarrollo
    generateMockData(type) {
        switch (type) {
            case 'sales':
                return {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                    values: Array.from({length: 6}, () => Math.floor(Math.random() * 5000) + 1000)
                };
            case 'categories':
                return {
                    labels: ['Electrónicos', 'Ropa', 'Hogar', 'Libros', 'Deportes'],
                    values: Array.from({length: 5}, () => Math.floor(Math.random() * 40) + 10)
                };
            case 'stores':
                return {
                    labels: ['TechStore', 'FashionHub', 'HomeDecor', 'BookCorner'],
                    sales: Array.from({length: 4}, () => Math.floor(Math.random() * 10000) + 2000),
                    orders: Array.from({length: 4}, () => Math.floor(Math.random() * 100) + 20)
                };
            case 'activity':
                return {
                    labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                    activeUsers: Array.from({length: 7}, () => Math.floor(Math.random() * 80) + 10),
                    newUsers: Array.from({length: 7}, () => Math.floor(Math.random() * 15) + 1)
                };
            default:
                return {};
        }
    }
}

// Clase para métricas en tiempo real
class RealTimeMetrics {
    constructor() {
        this.updateInterval = 30000; // 30 segundos
        this.counters = new Map();
        this.isRunning = false;
    }

    // Iniciar contador animado
    animateCounter(elementId, endValue, duration = 2000) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const startValue = 0;
        const increment = endValue / (duration / 16);
        let currentValue = startValue;

        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= endValue) {
                currentValue = endValue;
                clearInterval(timer);
            }
            element.textContent = Math.floor(currentValue).toLocaleString();
        }, 16);

        this.counters.set(elementId, timer);
    }

    // Animar porcentaje
    animatePercentage(elementId, endValue, duration = 2000) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const startValue = 0;
        const increment = endValue / (duration / 16);
        let currentValue = startValue;

        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= endValue) {
                currentValue = endValue;
                clearInterval(timer);
            }
            element.textContent = currentValue.toFixed(1) + '%';
        }, 16);

        this.counters.set(elementId, timer);
    }

    // Animar valor monetario
    animateCurrency(elementId, endValue, duration = 2000) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const startValue = 0;
        const increment = endValue / (duration / 16);
        let currentValue = startValue;

        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= endValue) {
                currentValue = endValue;
                clearInterval(timer);
            }
            element.textContent = '$' + Math.floor(currentValue).toLocaleString();
        }, 16);

        this.counters.set(elementId, timer);
    }

    // Detener todas las animaciones
    stopAllAnimations() {
        this.counters.forEach((timer, id) => {
            clearInterval(timer);
        });
        this.counters.clear();
    }

    // Simular actualizaciones en tiempo real
    startRealTimeUpdates() {
        if (this.isRunning) return;
        
        this.isRunning = true;
        this.updateTimer = setInterval(() => {
            // Actualizar métricas aleatorias para demo
            this.updateRandomMetrics();
        }, this.updateInterval);
    }

    stopRealTimeUpdates() {
        this.isRunning = false;
        if (this.updateTimer) {
            clearInterval(this.updateTimer);
        }
    }

    updateRandomMetrics() {
        // Simulación de actualización de métricas en tiempo real
        const metrics = document.querySelectorAll('[data-metric]');
        metrics.forEach(metric => {
            const currentValue = parseInt(metric.textContent.replace(/[^0-9]/g, '')) || 0;
            const variation = Math.floor(Math.random() * 10) - 5; // -5 a +5
            const newValue = Math.max(0, currentValue + variation);
            
            if (metric.textContent.includes('$')) {
                metric.textContent = '$' + newValue.toLocaleString();
            } else if (metric.textContent.includes('%')) {
                metric.textContent = newValue + '%';
            } else {
                metric.textContent = newValue.toLocaleString();
            }
        });
    }
}

// Inicializar sistema cuando el DOM esté listo
window.addEventListener('DOMContentLoaded', function() {
    // Verificar si Chart.js está disponible
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js no está cargado. Las gráficas no estarán disponibles.');
        return;
    }

    // Configuración global de Chart.js
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 13;
    Chart.defaults.color = '#374151';

    // Instanciar el motor de analytics
    window.analyticsEngine = new AnalyticsEngine();
    window.realTimeMetrics = new RealTimeMetrics();
});

// Exportar para uso global
window.AnalyticsEngine = AnalyticsEngine;
window.RealTimeMetrics = RealTimeMetrics;