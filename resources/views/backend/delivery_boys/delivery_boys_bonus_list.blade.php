@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h3 class="h3">{{ translate('All Bonuses List') }}</h3>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-block d-lg-flex">
        <h5 class="mb-0 h6">{{ translate('Bonuses List') }}</h5>
    </div>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Delivery Boy') }}</th>
                    <th class="text-center">{{ translate('Bonus Type') }}</th>
                    <th class="text-right">{{ translate('Bonus Amount') }}</th>
                    <th class="text-right">{{ translate('Remarks') }}</th>
                    <th class="text-right">{{ translate('Bonus Date Range') }}</th>
                    <th class="text-right">{{ translate('Created At') }}</th>

                </tr>
            </thead>
            <tbody>
                @foreach($delivery_boy_collections as $key => $delivery_boy_collection)
                    <tr>
                        <td>{{ ($key + 1) + ($delivery_boy_collections->currentPage() - 1) * $delivery_boy_collections->perPage() }}</td>
                        <td>{{ $delivery_boy_collection->user->name }}</td>
                        <td class="text-center">{{ $delivery_boy_collection->bonus_type }}</td>
                        <td class="text-right">{{ $delivery_boy_collection->bonus_amount }}</td>
                        <td class="text-right">{{ $delivery_boy_collection->remarks}}</td>
                        <td class="text-right">{{ $delivery_boy_collection->start_date }} - {{ $delivery_boy_collection->end_date }}</td>
                        <td class="text-right">{{ $delivery_boy_collection->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="aiz-pagination">
            {{ $delivery_boy_collections->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection
