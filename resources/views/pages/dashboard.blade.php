@extends('layout.main')
@section('title','CRM Dashboard')

@section('content')
<div class="pt-20">  <!-- CUMA pt-20, TANPA px -->
    <!-- Ringkasan Metrik -->
    <div class="px-4 sm:px-6 lg:px-8 mb-6">
        <x-dashboard.kpi />
    </div>

    <!-- Charts Section-->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8 px-4 sm:px-6 lg:px-8">
        <x-dashboard.chart.distribusigeografis />
        <x-dashboard.chart.kategoriindustri :chart-data="$chartCompanyData" />
    </div>

    <!-- Status Proposal & Trend -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8 px-4 sm:px-6 lg:px-8">
        <x-dashboard.chart.statusproposal />
        <x-dashboard.kpi.trendbulanan 
            :current-user="auth()->user()" 
            :sales-list="\App\Models\User::where('role_id', 12)->get()" 
        />

    </div>

    <!-- Communication & Follow-up Reminders -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8 px-4 sm:px-6 lg:px-8">
        <x-dashboard.kpi.folreminder />
        <x-dashboard.kpi.komunikasi />
        <x-dashboard.chart.performawilayah />
    </div>

    <!-- Aktivitas Terbaru & Quick Actions -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 px-4 sm:px-6 lg:px-8">
        <x-dashboard.kpi.aktivitasterbaru />
        <x-dashboard.action.action />
    </div>
</div>

<style>
    /* Hover effects for buttons */
    button:hover, a[href]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Focus styles for inputs */
    #searchInput:focus,
    #filterFollowUp:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        button span, a span {
            display: none;
        }
    }
</style>

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
@endpush

@endsection