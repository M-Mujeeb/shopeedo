@extends('backend.layouts.app')

@section('content')
    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Refund Request All') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="lg">{{ translate('Seller Name') }}</th>
                        <th data-breakpoints="lg">{{ translate('Product') }}</th>
                        <th data-breakpoints="lg">{{ translate('Price') }}</th>
                        <th data-breakpoints="lg">{{ translate('Seller Approval') }}</th>
                        <th>{{ translate('Refund Status') }}</th>
                        <th data-breakpoints="lg" width="15%" class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($refunds as $key => $refund)
                        <tr>
                            <td>{{ $key + 1 + ($refunds->currentPage() - 1) * $refunds->perPage() }}</td>
                            <td>
                                @if ($refund->order != null)
                                    {{ optional($refund->order)->code }}
                                @else
                                    {{ translate('Order deleted') }}
                                @endif
                            </td>
                            <td>
                                @if ($refund->seller != null)
                                    {{ $refund->seller->name }}
                                @endif
                            </td>
                            <td>
                                @if ($refund->orderDetail != null && $refund->orderDetail->product != null)
                                    <a href="{{ route('product', $refund->orderDetail->product->slug) }}" target="_blank"
                                        class="media-block">
                                        <div class="row">
                                            <div class="col-auto">
                                                <img src="{{ uploaded_asset($refund->orderDetail->product->thumbnail_img) }}"
                                                    alt="Image" class="size-50px">
                                            </div>
                                            <div class="col">
                                                <div class="media-body text-truncate-2">
                                                    {{ $refund->orderDetail->product->getTranslation('name') }}</div>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if ($refund->orderDetail != null)
                                    {{ single_price($refund->orderDetail->price) }}
                                @endif
                            </td>
                            <td>
                                @if (
                                    $refund->orderDetail != null &&
                                        $refund->orderDetail->product != null &&
                                        $refund->orderDetail->product->added_by == 'admin')
                                    <span class="badge badge-inline badge-warning">{{ translate('Own Product') }}</span>
                                @else
                                    @if ($refund->seller_approval == 1)
                                        <span class="badge badge-inline badge-success">{{ translate('Approved') }}</span>
                                    @elseif ($refund->seller_approval == 2)
                                        <span class="badge badge-inline badge-danger">{{ translate('Rejected') }}</span>
                                    @else
                                        <span class="badge badge-inline badge-primary">{{ translate('Pending') }}</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($refund->refund_status == 1)
                                    <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                @else
                                    <span class="badge badge-inline badge-warning">{{ translate('Non-Paid') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @can('accept_refund_request')
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                        onclick="refund_request_money('{{ $refund->id }}', {{ (float) ($refund->refund_amount ?? ($refund->orderDetail->price ?? 0) + ($refund->orderDetail->tax ?? 0)) }})"
                                        title="{{ translate('Refund Now') }}">
                                        <i class="las la-backward"></i>
                                    </a>
                                @endcan

                                @can('reject_refund_request')
                                    <a class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                        onclick="reject_refund_request('{{ route('admin.reject_reason_show', $refund->id) }}', '{{ $refund->id }}', '{{ optional($refund->order)->code }}')"
                                        title="{{ translate('Reject Refund Request') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @endcan
                                <a href="{{ route('admin.reason_show', $refund->id) }}"
                                    class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    title="{{ translate('View Reason') }}">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $refunds->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade reject_refund_request" id="modal-basic">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal member-block" action="{{ route('admin.reject_refund_request') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="refund_id" id="refund_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title h6">{{ translate('Reject Refund Request !') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Order Code') }}</label>
                            <div class="col-md-9">
                                <input type="text" value="" id="order_id" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Reject Reason') }}</label>
                            <div class="col-md-9">
                                <textarea type="text" name="reject_reason" id="reject_reason" rows="5" class="form-control"
                                    placeholder="{{ translate('Reject Reason') }}" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ translate('Close') }}</button>
                        <button type="submit" class="btn btn-success">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade approve_refund_request" id="modal-basic">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal member-block" action="{{ route('refund_request_money_by_admin') }}"
                    method="POST" id="approveRefundForm">
                    @csrf
                    <input type="hidden" name="refund_id" id="approve_refund_id" value="">
                    {{-- Keep a hidden max for server-side validation too --}}
                    <input type="hidden" name="max_refund" id="refund_amount_max" value="">

                    <div class="modal-header">
                        <h5 class="modal-title h6">{{ translate('Approve Refund Request !') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">{{ translate('Refund Amount') }}</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="refund_amount_input"
                                        name="refund_amount" step="0.01" min="0" required>
                                    {{-- If you have a currency text helper, you can append it here --}}
                                    {{-- <div class="input-group-append"><span class="input-group-text">{{ currency_symbol() }}</span></div> --}}
                                </div>
                                <small class="text-muted d-block mt-1" id="refund_amount_help"></small>
                            </div>
                        </div>

                        <p class="text-center mb-0">{{ translate('Do you want to approve this refund request?') }}</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-dismiss="modal">{{ translate('Close') }}</button>
                        <button type="submit" class="btn btn-success">{{ translate('Approve') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function update_refund_approval(el) {
            $.post('{{ route('admin.refund_approval') }}', {
                _token: '{{ @csrf_token() }}',
                el: el
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Approval has been done successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function refund_request_money(refund_id) {
            $('.approve_refund_request').modal('show');
            $('#approve_refund_id').val(refund_id);
        }

        function reject_refund_request(url, id, order_id) {
            $.get(url, function(data) {
                $('.reject_refund_request').modal('show');
                $('#refund_id').val(id);
                $('#order_id').val(order_id);
                $('#reject_reason').html(data);
            });
        }
    </script>
    <script>
        function refund_request_money(refund_id, maxAmount) {
            $('.approve_refund_request').modal('show');
            $('#approve_refund_id').val(refund_id);

            // parse and guard
            var max = parseFloat(maxAmount);
            if (isNaN(max) || max < 0) max = 0;

            // set hidden max + input value + input max + helper text
            $('#refund_amount_max').val(max.toFixed(2));
            $('#refund_amount_input')
                .attr('max', max.toFixed(2))
                .attr('min', 0)
                .attr('step', '0.01')
                .val(max.toFixed(2));

            $('#refund_amount_help').text('{{ translate('Maximum refundable amount:') }} ' + max.toFixed(2));
        }

        // Client-side guard: not more than max, not negative
        document.addEventListener('DOMContentLoaded', function() {
            var amountInput = document.getElementById('refund_amount_input');
            var maxInput = document.getElementById('refund_amount_max');
            var form = document.getElementById('approveRefundForm');

            if (amountInput) {
                amountInput.addEventListener('input', function() {
                    var max = parseFloat(maxInput.value || '0');
                    var val = parseFloat(amountInput.value || '0');

                    if (isNaN(val) || val < 0) {
                        amountInput.value = 0;
                        return;
                    }
                    if (val > max) {
                        amountInput.value = max.toFixed(2);
                        if (window.AIZ?.plugins?.notify) {
                            AIZ.plugins.notify('warning',
                                '{{ translate('You cannot refund more than the eligible amount') }}');
                        }
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    var max = parseFloat(maxInput.value || '0');
                    var val = parseFloat(amountInput.value || '0');
                    if (val > max || val < 0 || isNaN(val)) {
                        e.preventDefault();
                        if (window.AIZ?.plugins?.notify) {
                            AIZ.plugins.notify('danger', '{{ translate('Invalid refund amount') }}');
                        } else {
                            alert('{{ translate('Invalid refund amount') }}');
                        }
                    }
                });
            }
        });
    </script>
@endsection
