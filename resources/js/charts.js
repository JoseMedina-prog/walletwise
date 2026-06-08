/* WalletWise – charts helper
 * Centraliza paleta, opciones y responsive para los gráficos Chart.js.
 * Cargado solo en las páginas que lo necesitan.
 */

const PALETTE = {
    income:  { solid: '#10B981', soft: 'rgba(16, 185, 129, 0.85)' },
    expense: { solid: '#F43F5E', soft: 'rgba(244, 63, 94, 0.85)' },
    brand:   { solid: '#4F46E5', soft: 'rgba(79, 70, 229, 0.85)' },
    accent:  { solid: '#F59E0B', soft: 'rgba(245, 158, 11, 0.85)' },
    doughnut: [
        '#4F46E5', '#10B981', '#F59E0B', '#F43F5E', '#06B6D4',
        '#8B5CF6', '#EC4899', '#14B8A6', '#FBBF24', '#A855F7',
        '#84CC16', '#0EA5E9', '#22C55E', '#FB7185', '#3B82F6',
    ],
};

function isDark() {
    return document.documentElement.classList.contains('dark');
}

function getTheme() {
    const dark = isDark();
    return {
        text:        dark ? '#cbd5e1' : '#475569',
        textMuted:   dark ? '#94a3b8' : '#64748b',
        textStrong:  dark ? '#f1f5f9' : '#0f172a',
        grid:        dark ? 'rgba(255,255,255,0.06)' : 'rgba(15,23,42,0.06)',
        tooltipBg:   dark ? '#0f172a' : '#ffffff',
        tooltipText: dark ? '#f1f5f9' : '#0f172a',
        tooltipBorder: dark ? '#1e293b' : '#e2e8f0',
        surface:     dark ? '#0f172a' : '#ffffff',
    };
}

function commonOptions(theme) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 700, easing: 'easeOutQuart' },
        plugins: {
            legend: {
                labels: {
                    color: theme.text,
                    font: { size: 12, weight: '600', family: 'Inter, sans-serif' },
                    usePointStyle: true,
                    pointStyle: 'circle',
                    boxWidth: 8,
                    boxHeight: 8,
                    padding: 14,
                },
            },
            tooltip: {
                backgroundColor: theme.tooltipBg,
                titleColor: theme.tooltipText,
                bodyColor: theme.tooltipText,
                borderColor: theme.tooltipBorder,
                borderWidth: 1,
                cornerRadius: 10,
                padding: 12,
                displayColors: true,
                boxPadding: 4,
                titleFont: { size: 12, weight: '600', family: 'Inter, sans-serif' },
                bodyFont: { size: 12, family: 'Inter, sans-serif' },
            },
        },
    };
}

export function renderMonthlyBar(canvas, labels, income, expense) {
    const theme = getTheme();
    return new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Ingresos',
                    data: income,
                    backgroundColor: PALETTE.income.soft,
                    hoverBackgroundColor: PALETTE.income.solid,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 28,
                },
                {
                    label: 'Gastos',
                    data: expense,
                    backgroundColor: PALETTE.expense.soft,
                    hoverBackgroundColor: PALETTE.expense.solid,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 28,
                },
            ],
        },
        options: {
            ...commonOptions(theme),
            plugins: {
                ...commonOptions(theme).plugins,
                legend: { ...commonOptions(theme).plugins.legend, position: 'top', align: 'end' },
                tooltip: {
                    ...commonOptions(theme).plugins.tooltip,
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (ctx) => ` ${ctx.dataset.label}: $${ctx.parsed.y.toFixed(2)}`,
                    },
                },
            },
            scales: {
                x: {
                    ticks: { color: theme.textMuted, font: { size: 11, family: 'Inter, sans-serif' } },
                    grid: { display: false },
                    border: { display: false },
                },
                y: {
                    ticks: {
                        color: theme.textMuted,
                        font: { size: 11, family: 'Inter, sans-serif' },
                        callback: (v) => '$' + v,
                    },
                    grid: { color: theme.grid, drawBorder: false },
                    beginAtZero: true,
                },
            },
        },
    });
}

export function renderCategoryDoughnut(canvas, labels, values) {
    const theme = getTheme();
    return new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: labels.map((_, i) => PALETTE.doughnut[i % PALETTE.doughnut.length]),
                borderColor: theme.surface,
                borderWidth: 3,
                hoverOffset: 8,
            }],
        },
        options: {
            ...commonOptions(theme),
            cutout: '68%',
            plugins: {
                ...commonOptions(theme).plugins,
                legend: {
                    ...commonOptions(theme).plugins.legend,
                    position: 'right',
                    labels: {
                        ...commonOptions(theme).plugins.legend.labels,
                        font: { size: 11, family: 'Inter, sans-serif' },
                        padding: 10,
                    },
                },
                tooltip: {
                    ...commonOptions(theme).plugins.tooltip,
                    callbacks: {
                        label: (ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ` ${ctx.label}: $${ctx.parsed.toFixed(2)} (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
}

/* === Dark mode persistence === */
const Theme = {
    STORAGE_KEY: 'ww-theme',
    init() {
        const stored = localStorage.getItem(this.STORAGE_KEY);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = stored ? stored === 'dark' : prefersDark;
        this.apply(dark);

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem(this.STORAGE_KEY)) {
                this.apply(e.matches);
            }
        });
    },
    apply(dark) {
        document.documentElement.classList.toggle('dark', dark);
        document.dispatchEvent(new CustomEvent('ww:theme-changed', { detail: { dark } }));
    },
    toggle() {
        const next = !document.documentElement.classList.contains('dark');
        this.apply(next);
        localStorage.setItem(this.STORAGE_KEY, next ? 'dark' : 'light');
    },
    current() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    },
};

window.WWTheme = Theme;
window.WWCharts = { renderMonthlyBar, renderCategoryDoughnut, PALETTE };

document.addEventListener('DOMContentLoaded', () => {
    Theme.init();
});

// Notify listeners that Chart helpers are ready
document.dispatchEvent(new CustomEvent('ww:charts-ready'));

/* === Chart redraw on theme change === */
document.addEventListener('ww:theme-changed', () => {
    // Charts listen for this and re-instantiate via a custom event
    document.dispatchEvent(new CustomEvent('ww:charts-redraw'));
});
