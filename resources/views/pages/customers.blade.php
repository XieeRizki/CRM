@extends('layout.main')
@section('title','All Customers')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[80px]">
        
        <x-customers.attribut.kpi />
        <x-customers.action.action />
        

        <!-- Filters and Search -->
        <div class="bg-white rounded-xl shadow-sm border mb-6">
            <div class="p-6">
                <x-customers.attribut.filter />
                <x-customers.action.bulkaction />

                <!-- Customer Table -->
                <div class="bg-white rounded-xl shadow-sm border">
                    <x-customers.table.table />
                    <x-globals.pagination />
@endsection