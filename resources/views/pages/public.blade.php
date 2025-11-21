@extends('layout.public')
@section('title','public')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-4">
    <x-publics.aboutus :profile="$profile"/>
</div>



@endsection