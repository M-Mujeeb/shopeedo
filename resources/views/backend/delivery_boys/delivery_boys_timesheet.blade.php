@extends('backend.layouts.app')

@section('css')
    <style>
        .timesheet-container {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: calc(100vh - 100px);
            padding: 20px;
            margin: -20px;
        }

        .timesheet-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .timesheet-header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .timesheet-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .timesheet-filters {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #555;
        }

        .timesheet-form-control {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .timesheet-form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .timesheet-btn-primary {
            padding: 12px 25px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .timesheet-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .timesheet-info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 15px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .timesheet-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #555;
        }

        .timesheet-info-value {
            font-weight: 600;
            color: #333;
        }

        .timesheet-main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .employee-grid {
            display: grid;
            gap: 25px;
            margin-top: 20px;
        }

        .employee-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .employee-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .employee-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .employee-info {
            flex: 1;
        }

        .employee-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .employee-email {
            color: #666;
            font-size: 0.95rem;
        }

        .employee-stats {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .stat-item {
            text-align: center;
            padding: 15px 20px;
            background: #7D9A40;
            color: white;
            border-radius: 12px;
            min-width: 100px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timesheet-details {
            margin-top: 25px;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #888;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .date-row {
            background: #f8f9fb;
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
            border: 1px solid #e8ecf0;
        }

        .date-header {
            padding: 15px 20px;
            background: #7D9A40;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .date-info {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
        }

        .intervals-container {
            padding: 20px;
        }

        .intervals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .interval-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #7D9A40;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .interval-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .interval-time {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .interval-duration {
            color: #666;
            font-size: 0.9rem;
        }

        .timesheet-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timesheet-badge-info {
            background: #17a2b8;
            color: white;
        }

        .timesheet-badge-secondary {
            background: #6c757d;
            color: white;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .timesheet-header-top {
                flex-direction: column;
                align-items: stretch;
            }

            .timesheet-filters {
                flex-direction: column;
                align-items: stretch;
            }

            .employee-stats {
                flex-direction: column;
                gap: 10px;
            }

            .stat-item {
                min-width: unset;
            }

            .intervals-grid {
                grid-template-columns: 1fr;
            }

            .timesheet-title {
                font-size: 2rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="timesheet-container">
        <!-- Header -->
        <div class="timesheet-header">
            <div class="timesheet-header-top">
                <h1 class="timesheet-title">
                    <i class="fas fa-clock"></i>
                    {{ translate('Delivery Boys Timesheet') }}
                </h1>
            </div>

            <div class="timesheet-info-bar">
                <div class="timesheet-info-item">
                    <i class="fas fa-calendar"></i>
                    <span>{{ translate('Range') }}: <span
                            class="timesheet-info-value">{{ optional($fromLocal)->format('M d, Y') }} →
                            {{ optional($toLocal)->format('M d, Y') }}</span></span>
                </div>
                <div class="timesheet-info-item">
                    <i class="fas fa-users"></i>
                    <span>{{ translate('Showing') }}: <span class="timesheet-info-value">{{ $delivery_boys->count() }}
                            {{ translate('of') }} {{ $delivery_boys->total() }}
                            {{ translate('delivery boys') }}</span></span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="timesheet-main-content">
            <div class="employee-grid">
                @foreach ($delivery_boys as $index => $boy)
                    @php
                        $uid = $boy->user_id;
                        $user = $boy->user;
                        $days = $timesheets[$uid] ?? [];
                        $totalSec = $userTotals[$uid] ?? 0;

                        $fmtHM = function ($sec) {
                            $h = intdiv($sec, 3600);
                            $m = intdiv($sec % 3600, 60);
                            return sprintf('%02d:%02d', $h, $m);
                        };
                    @endphp

                    <div class="employee-card">
                        <div class="employee-header">
                            <div class="employee-info">
                                <div class="employee-name">{{ $user->name ?? '—' }}</div>
                                <div class="employee-email">{{ $user->email ?? '' }}</div>
                            </div>
                            <div class="employee-stats">
                                <div class="stat-item">
                                    <span class="stat-value">{{ count($days) }}</span>
                                    <span class="stat-label">{{ translate('Days') }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $fmtHM($totalSec) }}</span>
                                    <span class="stat-label">{{ translate('Hours') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="timesheet-details">
                            @if (empty($days))
                                <div class="no-data">
                                    <i class="fas fa-calendar-times empty-state-icon"></i>
                                    <div>{{ translate('No sessions recorded in this date range') }}</div>
                                </div>
                            @else
                                @foreach ($days as $date => $info)
                                    <div class="date-row">
                                        <div class="date-header">
                                            <span>
                                                <i class="fas fa-calendar-day"></i>
                                                {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                                            </span>
                                            <div class="date-info">
                                                <span>
                                                    <i class="fas fa-clock"></i>
                                                    {{ count($info['intervals']) }} {{ translate('shifts') }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-stopwatch"></i>
                                                    {{ $fmtHM($info['day_seconds']) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="intervals-container">
                                            <div class="intervals-grid">
                                                @foreach ($info['intervals'] as $interval)
                                                    <div class="interval-card">
                                                        <div class="interval-time">
                                                            <i class="fas fa-play-circle" style="color: #28a745;"></i>
                                                            {{ \Carbon\Carbon::parse($interval['start_time'])->format('H:i') }}
                                                            →
                                                            {{ \Carbon\Carbon::parse($interval['end_time'])->format('H:i') }}
                                                        </div>
                                                        <div class="interval-duration">
                                                            {{ translate('Duration') }}:
                                                            {{ $fmtHM($interval['seconds']) }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="aiz-pagination" style="margin-top: 30px;">
                {{ $delivery_boys->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to interval cards
            const intervalCards = document.querySelectorAll('.interval-card');
            intervalCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
                });
            });

            // Add form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const fromDate = document.querySelector('input[name="from"]').value;
                    const toDate = document.querySelector('input[name="to"]').value;

                    if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                        e.preventDefault();
                        alert('{{ translate('From date cannot be later than to date') }}');
                    }
                });
            }

            // Add loading state to filter button
            const filterBtn = document.querySelector('.timesheet-btn-primary');
            if (filterBtn) {
                filterBtn.addEventListener('click', function() {
                    this.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> {{ translate('Loading...') }}';
                    this.disabled = true;
                });
            }
        });
    </script>
@endsection
