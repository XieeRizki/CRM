@extends('layout.settings')
@section('title','Role-Setting')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <x-settingsm.kpi />
        <!-- Role Table (dengan pagination sudah di dalam) -->
        <x-settingsm.role.rtable :roles="$roles" />
        <x-globals.pagination :paginator="$roles" />
</div>

<x-settingsm.role.rform />
<x-settingsm.role.redit />
<x-settingsm.role.assign-menu :menus="$menus" />

@push('scripts')

<script src="{{ asset('js/global-toast.js') }}"></script>

@endpush
@endsection