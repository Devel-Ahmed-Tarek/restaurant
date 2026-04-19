import Chart from 'chart.js/auto';

const primary = 'rgba(249, 115, 22, 0.85)';
const primaryLight = 'rgba(249, 115, 22, 0.2)';
const grid = 'rgba(0, 0, 0, 0.06)';

function readData() {
    const el = document.getElementById('analytics-chart-data');
    if (!el) return null;
    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}

function hourLabels() {
    return Array.from({ length: 24 }, (_, i) => `${i}:00`);
}

document.addEventListener('DOMContentLoaded', () => {
    const data = readData();
    if (!data) return;

    const s = data.strings || {};
    const qty = s.qty || 'Qty';
    const orders = s.orders || 'Orders';
    const bundlesSold = s.bundlesSold || 'Bundles sold';

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
        },
        scales: {
            x: {
                grid: { color: grid },
                ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } },
            },
            y: {
                beginAtZero: true,
                grid: { color: grid },
                ticks: { precision: 0 },
            },
        },
    };

    const elTopP = document.getElementById('chart-top-products');
    if (elTopP && data.topProducts?.labels?.length) {
        new Chart(elTopP, {
            type: 'bar',
            data: {
                labels: data.topProducts.labels,
                datasets: [
                    {
                        label: qty,
                        data: data.topProducts.values,
                        backgroundColor: primary,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                plugins: {
                    ...commonOptions.plugins,
                    title: { display: true, text: s.titleTopProducts || 'Top products (quantity)' },
                },
            },
        });
    }

    const elTopO = document.getElementById('chart-top-offers');
    if (elTopO && data.topOffers?.labels?.length) {
        new Chart(elTopO, {
            type: 'bar',
            data: {
                labels: data.topOffers.labels,
                datasets: [
                    {
                        label: qty,
                        data: data.topOffers.values,
                        backgroundColor: primary,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                plugins: {
                    ...commonOptions.plugins,
                    title: { display: true, text: s.titleTopOffers || 'Top offers (quantity)' },
                },
            },
        });
    }

    const elHour = document.getElementById('chart-orders-hour');
    if (elHour) {
        new Chart(elHour, {
            type: 'line',
            data: {
                labels: hourLabels(),
                datasets: [
                    {
                        label: orders,
                        data: data.ordersByHour,
                        borderColor: primary,
                        backgroundColor: primaryLight,
                        fill: true,
                        tension: 0.25,
                    },
                ],
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: { display: true, text: s.titleOrdersByHour || 'Orders by hour (all)' },
                },
            },
        });
    }

    const elWd = document.getElementById('chart-orders-weekday');
    if (elWd) {
        new Chart(elWd, {
            type: 'bar',
            data: {
                labels: data.weekdayLabels,
                datasets: [
                    {
                        label: orders,
                        data: data.ordersByWeekday,
                        backgroundColor: primary,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: { display: true, text: s.titleOrdersByWeekday || 'Orders by weekday (all)' },
                },
            },
        });
    }

    if (data.product) {
        const elPh = document.getElementById('chart-product-hour');
        if (elPh) {
            new Chart(elPh, {
                type: 'bar',
                data: {
                    labels: hourLabels(),
                    datasets: [
                        {
                            label: qty,
                            data: data.product.byHour,
                            backgroundColor: primary,
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        title: {
                            display: true,
                            text: s.productHour || '',
                        },
                    },
                },
            });
        }

        const elPw = document.getElementById('chart-product-weekday');
        if (elPw) {
            new Chart(elPw, {
                type: 'bar',
                data: {
                    labels: data.weekdayLabels,
                    datasets: [
                        {
                            label: qty,
                            data: data.product.byWeekday,
                            backgroundColor: primary,
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        title: {
                            display: true,
                            text: s.productWeekday || '',
                        },
                    },
                },
            });
        }
    }

    if (data.offer) {
        const elOh = document.getElementById('chart-offer-hour');
        if (elOh) {
            new Chart(elOh, {
                type: 'bar',
                data: {
                    labels: hourLabels(),
                    datasets: [
                        {
                            label: bundlesSold,
                            data: data.offer.byHour,
                            backgroundColor: primary,
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        title: {
                            display: true,
                            text: s.offerHour || '',
                        },
                    },
                },
            });
        }

        const elOw = document.getElementById('chart-offer-weekday');
        if (elOw) {
            new Chart(elOw, {
                type: 'bar',
                data: {
                    labels: data.weekdayLabels,
                    datasets: [
                        {
                            label: bundlesSold,
                            data: data.offer.byWeekday,
                            backgroundColor: primary,
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        title: {
                            display: true,
                            text: s.offerWeekday || '',
                        },
                    },
                },
            });
        }
    }
});
