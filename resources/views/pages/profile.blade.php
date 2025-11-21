@extends('layout.settings')
@section('title','User Management')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-4">
     <x-settingsm.profile.profile   />
        
</div>



<style>
    /* Hover effects for buttons */
    button:hover, a[href]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Focus styles */
    #searchInput:focus,
    #filterRole:focus,
    #filterStatus:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        button span, a span {
            display: none;
        }
    }
</style>

@endsection