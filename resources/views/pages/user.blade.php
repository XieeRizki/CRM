@extends('layout.settings')
@section('title','User-Setting')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    <!-- Filters and Search -->
    <x-globals.filtersearch
        tableId="userTable"
        :searchFields="[2,3]"
        :showRoleFilter="true"
        :roles="$roles"
        ajaxUrl="{{ route('users.search') }}"
    />

    <!-- User Management Table (dengan pagination sudah di dalam) -->
    <x-settingsm.user.utable :users="$users" :roles="$roles" :provinces="$provinces" />

</div>

<!-- User Form -->
<x-settingsm.user.uform :roles="$roles" :provinces="$provinces" />

<script>
    window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
</script>
@endsection