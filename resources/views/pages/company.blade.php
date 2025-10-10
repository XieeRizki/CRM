@extends('layout.main')
@section('title','Company')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px]">
    <!-- KPI Section -->
    <x-company.attribut.kpi
        :totalCompanies="$totalCompanies"
        :jenisCompanies="$jenisCompanies"
        :tierCompanies="$tierCompanies"
        :activeCompanies="$activeCompanies"
    />

    <!-- Company Table -->
    <div class="bg-white rounded-xl shadow-sm border mt-3">
        <div class="p-6">
            <x-company.attribut.filtersearch
            tableId="companyTable"
            :searchFields="[2,3,4,5]"
            :showRoleFilter="false"
            ajaxUrl="{{ route('companies.search') }}"
            />
            <x-company.table.table :companies="$companies" :types="$types"/>
            <x-globals.pagination :paginator="$companies" />
        </div>
    </div>
</div>
@endsection
