// === Import Core ===
import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import CalendarPage from "@/pages/CalendarPage";

// === Import eksternal ===
import "./bootstrap";
import "flowbite";
import Chart from "chart.js/auto";

// =======================
//  REACT COMPONENT
// =======================
function Home() {
    return (
        <div className="p-6">
            <h1 className="text-3xl font-bold">Selamat Datang di CRM 🚀</h1>
            <p className="mt-2 text-gray-600">
                Ini halaman awal React. Akses{" "}
                <a href="/calendar" className="text-blue-500 underline">
                    /calendar
                </a>{" "}
                untuk buka kalender.
            </p>
        </div>
    );
}

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/calendar" element={<CalendarPage />} />
            </Routes>
        </BrowserRouter>
    );
}

// =======================
//  RENDER REACT
// =======================
const rootElement = document.getElementById("app");
if (rootElement) {
    ReactDOM.createRoot(rootElement).render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
}

// =======================
//  SALES PERFORMANCE CHART (NEW)
// =======================
const salesPerformanceCanvas = document.getElementById("salesPerformance");

if (salesPerformanceCanvas) {
    // Show loading indicator
    const loadingDiv = salesPerformanceCanvas.parentElement.querySelector('.loading-indicator');
    if (loadingDiv) loadingDiv.style.display = 'flex';

    // Fetch data from API
    fetch('/api/sales-performance')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(result => {
            if (result.success) {
                const { data, stats } = result;

                // Update statistics in UI
                const totalCompanyEl = document.getElementById('totalCompanyVisited');
                const totalDealEl = document.getElementById('totalDeal');
                const totalFailsEl = document.getElementById('totalFails');
                
                if (totalCompanyEl) totalCompanyEl.textContent = stats.total_company_visited;
                if (totalDealEl) totalDealEl.textContent = stats.total_deal;
                if (totalFailsEl) totalFailsEl.textContent = stats.total_fails;

                // Create chart
                new Chart(salesPerformanceCanvas, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.y + ' contacts';
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { 
                                    font: { size: 11 },
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { 
                                    stepSize: 1,
                                    font: { size: 11 }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const salesName = data.labels[index];
                                const salesData = result.details[index];
                                
                                // Show detail modal
                                showSalesDetailModal(salesData.user_id, salesName);
                            }
                        }
                    }
                });

                // Hide loading
                if (loadingDiv) loadingDiv.style.display = 'none';
            } else {
                throw new Error(result.message || 'Failed to load data');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            
            // Show error message
            const container = salesPerformanceCanvas.parentElement;
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-red-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-semibold">Failed to load sales data</p>
                        <p class="text-sm mt-1">${error.message}</p>
                    </div>
                </div>
            `;
        });
}

/**
 * Show sales detail modal
 */
function showSalesDetailModal(userId, salesName) {
    fetch(`/api/sales-performance/${userId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(result => {
            if (result.success) {
                // Safety check for data structure
                const companyVisited = result.company_visited || {};
                const details = companyVisited.details || [];
                const total = companyVisited.total || 0;
                const deal = companyVisited.deal || 0;
                const fails = companyVisited.fails || 0;
                const totalVisits = companyVisited.total_visits || 0;
                
                const modalHTML = `
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="salesDetailModal" onclick="if(event.target === this) closeSalesModal()">
                        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                            <!-- Header -->
                            <div class="flex justify-between items-center mb-4 border-b pb-3">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">${salesName}</h3>
                                    <p class="text-sm text-gray-500">Performance Detail</p>
                                </div>
                                <button onclick="closeSalesModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Content Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Company Visited Section -->
                                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <h4 class="font-semibold text-lg text-blue-700">
                                            Company Visited
                                        </h4>
                                        <span class="ml-auto bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                            ${total}
                                        </span>
                                    </div>
                                    <div class="space-y-2 max-h-96 overflow-y-auto">
                                        ${details.length > 0
                                            ? details.map(company => `
                                                <div class="p-2 bg-white rounded shadow-sm">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-medium text-gray-800">${company.company_name || 'Unknown'}</span>
                                                        <span class="text-xs ${company.status_color === 'green' ? 'bg-green-100 text-green-700' : company.status_color === 'red' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'} px-2 py-1 rounded">
                                                            ${company.status || 'Pending'}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                        <span>Visits: ${company.visit_count || 0}x</span>
                                                        <span>Tier: ${company.tier || '-'}</span>
                                                    </div>
                                                </div>
                                            `).join('')
                                            : '<p class="text-sm text-gray-500 text-center py-4">No company visited yet</p>'
                                        }
                                    </div>
                                </div>
                                
                                <!-- Statistics Section -->
                                <div class="space-y-4">
                                    <!-- Deal Stats -->
                                    <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h4 class="font-semibold text-lg text-green-700">Deal</h4>
                                            <span class="ml-auto bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                                ${deal}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            ${total > 0 ? Math.round((deal / total) * 100) : 0}% success rate
                                        </p>
                                    </div>
                                    
                                    <!-- Fails Stats -->
                                    <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h4 class="font-semibold text-lg text-red-700">Fails</h4>
                                            <span class="ml-auto bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                                                ${fails}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            ${total > 0 ? Math.round((fails / total) * 100) : 0}% of visits
                                        </p>
                                    </div>
                                    
                                    <!-- Total Visits -->
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            <h4 class="font-semibold text-gray-700">Total Visits</h4>
                                        </div>
                                        <p class="text-2xl font-bold text-gray-800">${totalVisits}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Avg: ${total > 0 ? (totalVisits / total).toFixed(1) : 0} visits/company
                                        </p>
                                    </div>
                                    
                                    <p class="text-xs text-center text-gray-500 italic mt-4">
                                        * Real data from transaksi table
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove old modal if exists
                const oldModal = document.getElementById('salesDetailModal');
                if (oldModal) oldModal.remove();
                
                // Add new modal
                document.body.insertAdjacentHTML('beforeend', modalHTML);
            } else {
                throw new Error(result.message || 'Failed to load data');
            }
        })
        .catch(error => {
            console.error('Error loading detail:', error);
            
            // Show error modal
            const errorModal = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="salesDetailModal" onclick="closeSalesModal()">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Failed to Load Detail</h3>
                            <p class="text-sm text-gray-600 mb-4">${error.message}</p>
                            <button onclick="closeSalesModal()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            const oldModal = document.getElementById('salesDetailModal');
            if (oldModal) oldModal.remove();
            document.body.insertAdjacentHTML('beforeend', errorModal);
        });
}

/**
 * Close sales detail modal
 */
window.closeSalesModal = function() {
    const modal = document.getElementById('salesDetailModal');
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(() => modal.remove(), 200);
    }
}

// =======================
//  CHART: VISIT TREND (FINAL - ROLE_ID aware)
// =======================
const visitTrendCanvas = document.getElementById("visitTrend");
let visitTrendChart = null;
let currentPeriod = 'monthly';

if (visitTrendCanvas) {
    loadVisitTrend('monthly');
    setupTrendFilters();
    setupDateRangePicker();
    setupSalesDropdown(); // dropdown only exists for superadmin (role_id == 1)
}

/**
 * Load visit trend data
 * period: daily | weekly | monthly | yearly | custom
 */
function loadVisitTrend(period, startDate = null, endDate = null, selectedUserId = null) {
    if (!visitTrendCanvas) return;

    currentPeriod = period;
    const loadingDiv = document.getElementById('visitTrendLoading');
    if (loadingDiv) loadingDiv.style.display = 'flex';

    let url = `/api/visit-trend?period=${period}`;

    if (startDate && endDate) {
        url += `&start_date=${startDate}&end_date=${endDate}`;
    }

    // current user info (role_id numeric)
    const currentUserId = document.getElementById('currentUserId')?.value;
    const currentUserRoleId = document.getElementById('currentUserRole')?.value; // role_id as string or number

    // If superadmin (role_id == 1) -> do NOT attach user_id unless selectedUserId provided
    const isSuperadmin = Number(currentUserRoleId) === 1;

    if (selectedUserId) {
        url += `&user_id=${selectedUserId}`;
    } else if (!isSuperadmin) {
        // non-superadmin (sales) -> only their data
        url += `&user_id=${currentUserId}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                updateVisitTrendChart(result);
                updateVisitTrendStats(result.stats);
            } else {
                console.error('Visit trend API returned success=false', result);
            }
            if (loadingDiv) loadingDiv.style.display = 'none';
        })
        .catch(err => {
            console.error('Fetch visit-trend error', err);
            if (loadingDiv) loadingDiv.style.display = 'none';
        });
}

/**
 * Update Chart
 */
function updateVisitTrendChart(result) {
    const { data } = result;

    if (visitTrendChart) visitTrendChart.destroy();

    const currentUserRoleId = Number(document.getElementById('currentUserRole')?.value || 0);

    // Determine color: sales (role_id 12) -> green; else blue
    const isSales = currentUserRoleId === 12;
    const color = isSales ? 'rgba(34,197,94,1)' : 'rgba(59,130,246,1)';
    const fillTop = isSales ? 'rgba(34,197,94,0.28)' : 'rgba(59,130,246,0.28)';

    visitTrendChart = new Chart(visitTrendCanvas, {
        type: 'line',
        data: {
            labels: data.labels || [],
            datasets: [{
                label: 'Kunjungan',
                data: data.visits || [],
                borderColor: color,
                backgroundColor: function(ctx) {
                    const c = ctx.chart.ctx;
                    const g = c.createLinearGradient(0, 0, 0, 400);
                    g.addColorStop(0, fillTop);
                    g.addColorStop(1, 'rgba(0,0,0,0.03)');
                    return g;
                },
                tension: 0.35,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: color
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: ctx => ctx[0]?.label || '',
                        label: ctx => (ctx.parsed.y ?? 0) + ' kunjungan'
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 0 } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
}

/**
 * Setup superadmin dropdown (salesFilter)
 */
function setupSalesDropdown() {
    const dropdown = document.getElementById("salesFilter");
    if (!dropdown) return;

    dropdown.addEventListener("change", function () {
        const selected = this.value;
        if (selected === "") {
            loadVisitTrend(currentPeriod);
        } else {
            loadVisitTrend(currentPeriod, null, null, selected);
        }
    });
}

/**
 * Update statistics
 */
function updateVisitTrendStats(stats) {
    const totalEl = document.getElementById('totalVisits');
    const averageEl = document.getElementById('averageVisits');
    const periodEl = document.getElementById('periodLabel');

    if (totalEl) totalEl.textContent = stats.total_visits ?? 0;
    if (averageEl) {
        const avgVal = stats.average_per_day ?? stats.average_per_week ?? stats.average_per_month ?? stats.average_per_year ?? 0;
        const avgText = currentPeriod === 'daily' ? 'Per Hari' : currentPeriod === 'weekly' ? 'Per Minggu' : currentPeriod === 'monthly' ? 'Per Bulan' : 'Per Tahun';
        averageEl.textContent = avgVal + ' / ' + avgText;
    }
    if (periodEl) periodEl.textContent = stats.period_label || '';
}

/**
 * Setup filter buttons (daily/monthly/yearly)
 */
function setupTrendFilters() {
    const filterButtons = document.querySelectorAll('[data-trend-period]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const period = this.getAttribute('data-trend-period');

            // active state
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-white', 'text-gray-700');
            });
            this.classList.remove('bg-white', 'text-gray-700');
            this.classList.add('bg-blue-500', 'text-white');

            // hide date picker
            const dateRangePicker = document.getElementById('dateRangePicker');
            if (dateRangePicker) dateRangePicker.classList.add('hidden');

            loadVisitTrend(period);
        });
    });
}

/**
 * Setup date range picker
 */
function setupDateRangePicker() {
    const customRangeBtn = document.getElementById('customRangeBtn');
    const dateRangePicker = document.getElementById('dateRangePicker');
    const applyBtn = document.getElementById('applyDateRange');
    const cancelBtn = document.getElementById('cancelDateRange');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    const today = new Date();
    const last30Days = new Date(today);
    last30Days.setDate(today.getDate() - 30);

    if (endDateInput) endDateInput.valueAsDate = today;
    if (startDateInput) startDateInput.valueAsDate = last30Days;

    if (customRangeBtn && dateRangePicker) {
        customRangeBtn.addEventListener('click', function() {
            dateRangePicker.classList.toggle('hidden');
            // remove active buttons
            const filterButtons = document.querySelectorAll('[data-trend-period]');
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-white', 'text-gray-700');
            });
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            if (!startDate || !endDate) { alert('Please select both start and end dates'); return; }
            if (new Date(startDate) > new Date(endDate)) { alert('Start date must be before end date'); return; }

            // For custom range we will not include selectedUserId here (allow dropdown to still filter)
            loadVisitTrend('custom', startDate, endDate);
            dateRangePicker.classList.add('hidden');
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            dateRangePicker.classList.add('hidden');
        });
    }
}

// =======================
//  CHART 3: TREND (EXISTING - KEEP FOR BACKWARD COMPATIBILITY)
// =======================
const trend = document.getElementById("trend");
if (trend) {
    new Chart(trend, {
        type: "line",
        data: {
            labels: ["Apr", "Mei", "Jun", "Jul", "Agu", "Sep"],
            datasets: [
                {
                    label: "Proposal",
                    data: [50, 44, 52, 40, 60, 62],
                    borderColor: "rgba(34, 197, 94, 1)",
                    backgroundColor: "rgba(34, 197, 94, 0.2)",
                    tension: 0.3,
                    fill: true,
                },
                {
                    label: "Customer",
                    data: [300, 350, 420, 450, 500, 490],
                    borderColor: "rgba(59, 130, 246, 1)",
                    backgroundColor: "rgba(59, 130, 246, 0.2)",
                    tension: 0.3,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: "bottom" } },
            scales: { y: { beginAtZero: true } },
        },
    });
}
