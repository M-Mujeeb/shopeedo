@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h3 class="h3">{{ translate('All Delivery Boys Timesheet') }}</h3>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-block d-lg-flex">
            <h5 class="mb-0 h6">{{ translate('Delivery Boys') }}</h5>
            <div class="">
                <form class="" id="sort_delivery_boys" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <div class="" style="min-width: 250px;">
                            <input type="text" class="form-control" id="search"
                                name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                                placeholder="{{ translate('Type email or name & Enter') }}">
                        </div>
                    </div>
                </form>
                <a href="{{ route('delivery-boy.export-delivery-boys') }}" class="text-align:end btn btn-primary"
                    style="float: right; margin-top:5px">Export</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th>{{ translate('Name') }}</th>
                        <th data-breakpoints="lg">{{ translate('Email Address') }}</th>
                        <th data-breakpoints="lg">{{ translate('Phone') }}</th>
                        <th>{{ translate('Unique ID') }}</th>
                        <th width="300">{{ translate('Total Hours per day') }}</th>
                        <th>{{ translate('Acceptance Count') }}</th>
                        <th>{{ translate('Rejection Count') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($delivery_boys as $key => $boy)
                        @php
                            $u = $boy->user;
                            $uid = $boy->user_id;
                            $perDay = $timesheets[$uid] ?? [];
                            $totalSecs = $userTotals[$uid] ?? 0;

                            $fmt = function ($secs) {
                                $h = intdiv($secs, 3600);
                                $m = intdiv($secs % 3600, 60);
                                return sprintf('%02d:%02d', $h, $m);
                            };
                        @endphp
                        <tr>
                            <td>{{ $key + 1 + ($delivery_boys->currentPage() - 1) * $delivery_boys->perPage() }}</td>
                            <td>{{ $u?->name }}</td>
                            <td>{{ $u?->email }}</td>
                            <td>{{ $u?->phone }}</td>
                            <td>{{ $boy->unique_id ?? '—' }}</td>

                            {{-- Improved Total Hours per day display --}}
                            <td>
                                @php
                                    $u = $boy->user;
                                    $uid = $boy->user_id;
                                    $perDay = $timesheets[$uid] ?? [];
                                    $totalSecs = $userTotals[$uid] ?? 0;
                                    $fmt = function ($secs) {
                                        $h = intdiv($secs, 3600);
                                        $m = intdiv($secs % 3600, 60);
                                        return sprintf('%02d:%02d', $h, $m);
                                    };
                                    $modalId = 'tsModal_' . $uid;
                                @endphp

                                {{-- Clickable total that opens modal --}}
                                <button type="button" class="btn btn-link p-0 font-weight-bold" data-toggle="modal"
                                    data-target="#{{ $modalId }}" @if (empty($perDay)) disabled @endif>
                                    {{ $fmt($totalSecs) }}
                                </button>

                                {{-- Inline modal with full breakdown --}}
                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog"
                                    aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="{{ $modalId }}Label">
                                                    Timesheet — {{ $u?->name }}
                                                    <small class="text-muted d-block" style="font-size:12px">
                                                        Period: {{ $fromLocal->format('M d, Y') }} –
                                                        {{ $toLocal->format('M d, Y') }} ({{ $tz }})
                                                    </small>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                @if (empty($perDay))
                                                    <div class="text-center text-muted py-4">No hours logged.</div>
                                                @else
                                                    {{-- Summary --}}
                                                    <div
                                                        class="mb-3 p-3 rounded bg-light d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="text-muted d-block">Period Total</small>
                                                            <div class="h5 mb-0">{{ $fmt($totalSecs) }}</div>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Timezone: {{ $tz }}</small>
                                                        </div>
                                                    </div>

                                                    {{-- Daily breakdown --}}
                                                    @foreach ($perDay as $date => $day)
                                                        <div class="border rounded mb-3">
                                                            <div
                                                                class="px-3 py-2 bg-light d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <strong>{{ \Carbon\Carbon::parse($date, $tz)->format('M d, Y') }}</strong>
                                                                    <small
                                                                        class="text-muted ml-2">{{ \Carbon\Carbon::parse($date, $tz)->format('l') }}</small>
                                                                </div>
                                                                <div class="text-right">
                                                                    <span
                                                                        class="font-weight-bold">{{ $fmt($day['day_seconds']) }}</span>
                                                                    <small class="text-muted ml-2">
                                                                        {{ count($day['intervals']) }}
                                                                        session{{ count($day['intervals']) != 1 ? 's' : '' }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            @if (count($day['intervals']) > 0)
                                                                <div class="px-3 py-2">
                                                                    @foreach ($day['intervals'] as $interval)
                                                                        <div
                                                                            class="d-flex justify-content-between small py-1 border-bottom">
                                                                            <div class="text-muted">
                                                                                {{ $interval['start_time'] }} –
                                                                                {{ $interval['end_time'] }}</div>
                                                                            <div>{{ $fmt($interval['seconds']) }}</div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ translate('Close') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $delivery_boys->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    <style>
        .border-left {
            border-left: 3px solid #007bff !important;
        }

        .font-weight-medium {
            font-weight: 500 !important;
        }

        .no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .no-gutters>.col,
        .no-gutters>[class*="col-"] {
            padding-right: 0;
            padding-left: 0;
        }
    </style>
@endsection
