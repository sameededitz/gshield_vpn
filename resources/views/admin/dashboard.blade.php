@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <livewire:widgets.stats />
    <livewire:widgets.users-count />
    <livewire:widgets.sales-analytic />
@endsection
@section('scripts')
    <script src="{{ asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
@endsection
