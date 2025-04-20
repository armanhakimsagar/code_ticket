@extends('layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('breadcrumb')
    {{-- <li class="breadcrumb-item">{{ __('Home') }}</li> --}}
@endsection
{{-- @section('action-button')
        @if(\Auth::user()->parent == 0)
            <a href="#" class="btn btn-sm btn-primary btn-icon cp_link" data-link="" data-toggle="tooltip" data-original-title="{{__('Click to copy invoice link')}}" title="{{__('Click To Copy Support Ticket Url')}}" data-bs-toggle="tooltip" data-bs-placement="top">
            <i class="ti ti-copy"></i>
            </a>
    @endif
@endsection --}}
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-7">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="card">
                                <div class="card-body dash_card_height">
                                    <div class="theme-avtar bg-primary">
                                        <i class="fas fa-list-alt"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Total') }}</p>
                                    <h6 class="mb-3">{{ __('Categories') }}</h6>
                                    <h3 class="mb-0">{{ $categories }}</h3>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="card">
                                <div class="card-body dash_card_height">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Open') }}</p>
                                    <h6 class="mb-3">{{ __('Tickets') }}</h6>
                                    <h3 class="mb-0">{{ $open_ticket }} </h3>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="card">
                                <div class="card-body dash_card_height">
                                    <div class="theme-avtar bg-warning">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Closed') }}</p>
                                    <h6 class="mb-3">{{ __('Tickets') }}</h6>
                                    <h3 class="mb-0">{{ $close_ticket }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="card">
                                <div class="card-body dash_card_height">
                                    <div class="theme-avtar bg-danger">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Total') }}</p>
                                    <h6 class="mb-3">{{ __('Agents') }}</h6>
                                    <h3 class="mb-0">{{ $agents }}</h3>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-5">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Tickets by Category') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div id="categoryPie"></div>
                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('This Year Tickets') }}</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartBar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

    <script>
        (function() {
            var chartBarOptions = {
                series: [{
                    name: '{{ __("Tickets") }}',
                    // data: [40, 20, 60, 15, 50, 65, 20, 40, 20, 60, 15, 50]
                    data: {!! json_encode(array_values($monthData)) !!}
                }, ],

                chart: {
                    height: 150,
                    type: 'area',
                    // type: 'line',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode(array_keys($monthData)) !!},
                    title: {
                        text: '{{ __('Months') }}'
                    }
                },
                colors: ['#ffa21d', '#FF3A6E'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                markers: {
                    size: 4,
                    colors: ['#ffa21d', '#FF3A6E'],
                    opacity: 0.9,
                    strokeWidth: 2,
                    hover: {
                        size: 7,
                    }
                },
                yaxis: {
                    title: {
                        text: '{{ __('Tickets') }}'
                    },
                    tickAmount: 3,
                    min: 10,
                    max: 70,
                }
            };
            var arChart = new ApexCharts(document.querySelector("#chartBar"), chartBarOptions);
            arChart.render();
        })();
        (function() {
            var categoryPieOptions = {
                chart: {
                    height: 140,
                    type: 'donut',
                },
                dataLabels: {
                    enabled: false,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                        }
                    }
                },
                series: {!! json_encode($chartData['value']) !!},
                colors: {!! json_encode($chartData['color']) !!},
                labels: {!! json_encode($chartData['name']) !!},
                legend: {
                    show: true
                }
            };
            var categoryPieChart = new ApexCharts(document.querySelector("#categoryPie"), categoryPieOptions);
            categoryPieChart.render();
        })();
    </script>
@endpush
