@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('actions')
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Reports
    </a>
    <button class="btn btn-info" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">Profit & Loss Report</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.profit-loss') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Generate Report
                    </button>
                    <a href="{{ route('reports.profit-loss') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Profit & Loss Summary</h5>
                        <h6 class="card-subtitle text-muted">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr class="table-light">
                                        <th colspan="2">Revenue</th>
                                    </tr>
                                    <tr>
                                        <td>Gross Sales</td>
                                        <td class="text-end">${{ number_format($totalRevenue, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tax Collected</td>
                                        <td class="text-end">${{ number_format($totalTax, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Discounts</td>
                                        <td class="text-end">-${{ number_format($totalDiscounts, 2) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <th>Net Revenue</th>
                                        <th class="text-end">${{ number_format($netRevenue, 2) }}</th>
                                    </tr>
                                    
                                    <tr class="table-light">
                                        <th colspan="2">Cost of Goods Sold</th>
                                    </tr>
                                    <tr>
                                        <td>Cost of Goods Sold</td>
                                        <td class="text-end">${{ number_format($costOfGoodsSold, 2) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <th>Total COGS</th>
                                        <th class="text-end">${{ number_format($costOfGoodsSold, 2) }}</th>
                                    </tr>
                                    
                                    <tr class="table-success">
                                        <th>Gross Profit</th>
                                        <th class="text-end">${{ number_format($grossProfit, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <td>Gross Profit Margin</td>
                                        <td class="text-end">{{ number_format($grossProfitMargin, 2) }}%</td>
                                    </tr>
                                    
                                    <tr class="table-light">
                                        <th colspan="2">Inventory</th>
                                    </tr>
                                    <tr>
                                        <td>Total Purchases</td>
                                        <td class="text-end">${{ number_format($totalPurchases, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Inventory Change</td>
                                        <td class="text-end">${{ number_format($inventoryChange, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Monthly Profit Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="profitChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Monthly Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Revenue</th>
                                        <th>Cost</th>
                                        <th>Profit</th>
                                        <th>Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monthlyData as $month)
                                        <tr>
                                            <td>{{ $month['label'] }}</td>
                                            <td>${{ number_format($month['revenue'], 2) }}</td>
                                            <td>${{ number_format($month['cost'], 2) }}</td>
                                            <td>${{ number_format($month['profit'], 2) }}</td>
                                            <td>
                                                @if($month['revenue'] > 0)
                                                    {{ number_format(($month['profit'] / $month['revenue']) * 100, 2) }}%
                                                @else
                                                    0.00%
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .navbar, .sidebar, footer, form {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #fff !important;
            color: #000 !important;
        }
        
        body {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .col-md-9, .col-lg-10 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
        }
        
        .table {
            width: 100% !important;
        }
    }
</style>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profit chart
        const profitChartCtx = document.getElementById('profitChart').getContext('2d');
        
        const monthlyData = @json($monthlyData);
        const labels = Object.values(monthlyData).map(item => item.label);
        const revenues = Object.values(monthlyData).map(item => item.revenue);
        const costs = Object.values(monthlyData).map(item => item.cost);
        const profits = Object.values(monthlyData).map(item => item.profit);
        
        new Chart(profitChartCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: revenues,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Cost',
                        data: costs,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Profit',
                        data: profits,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
