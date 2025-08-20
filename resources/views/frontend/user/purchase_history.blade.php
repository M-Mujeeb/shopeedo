@extends('frontend.layouts.user_panel')

@section('panel-style')
<style>
    .card  .card-body {
    padding-left:0 !important;
    padding-right:0 !important;
   }
   /* tbody {
    padding-left:19px !important;
    padding-right:19px !important;} */
</style>
@endsection

@section('panel_content')
    <div class="card shadow-none rounded-0 border">
        <div class="card-header border-bottom-0">
            <h5 class="mb-0 fs-20 fw-700 text-dark p-2">{{ translate('Purchase History') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead class="text-gray fs-12">
                    <tr>
                        <th class="pl-2">{{ translate('Code')}}</th>
                        <th data-breakpoints="md">{{ translate('Date')}}</th>
                        <th>{{ translate('Amount')}}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status')}}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status')}}</th>
                        <th class="text-right pr-2">{{ translate('Options')}}</th>
                    </tr>
                </thead>

                @php
                $delivery_boy = "";
                $delivery_boy_id = "";
                foreach ($orders as $order){
                    $delivery_boy = $order->delivery_boy->name ??  '';
                    $delivery_boy_id = $order->delivery_boy->id ?? '';
                    break;
                }

                @endphp


                <tbody class="fs-14">
                    @foreach ($orders as $key => $order)
                        @if (count($order->orderDetails) > 0)
                            <tr >
                                <!-- Code -->
                                <td class="pl-2">
                                    <a href="{{route('purchase_history.details', encrypt($order->id))}}">{{ $order->code }}</a>
                                </td>
                                <!-- Date -->
                                <td class="text-secondary">{{ date('d-m-Y', $order->date) }}</td>
                                <!-- Amount -->
                                <td class="fw-700">
                                    {{ single_price($order->grand_total) }}
                                </td>
                                <!-- Delivery Status -->
                                <td class="fw-700">
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}
                                    @if($order->delivery_viewed == 0)
                                        <span class="ml-2" style="color:green"><strong>*</strong></span>
                                    @endif
                                </td>
                                <!-- Payment Status -->
                                <td>
                                    @if ($order->payment_status == 'paid')
                                        <span class="badge badge-inline badge-success p-3 fs-12" style="border-radius: 25px; min-width: 80px !important;">{{translate('Paid')}}</span>
                                    @else
                                        <span class="badge badge-inline badge-danger p-3 fs-12" style="border-radius: 25px; min-width: 80px !important;">{{translate('Unpaid')}}</span>
                                    @endif
                                    @if($order->payment_status_viewed == 0)
                                        <span class="ml-2" style="color:green"><strong>*</strong></span>
                                    @endif
                                </td>
                                <!-- Options -->
                                <td class="text-right pr-2">
                                    {{-- @php
                                        $deliveryBoyName = $order->delivery_boy->name;
                                    @endphp --}}

                                    @if($order->delivery_boy !=null && $order->delivery_status != 'delivered')
                                    <a href="javascript:void(0)" class="btn-icon btn-circle btn-sm " onclick="show_delivery_chat_modal()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                        class="mr-2 has-transition ">
                                        <g id="Group_23918" data-name="Group 23918" transform="translate(1053.151 256.688)">
                                            <path id="Path_3012" data-name="Path 3012"
                                                d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z"
                                                transform="translate(-1178 -341)" fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                                            <path id="Path_3013" data-name="Path 3013"
                                                d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1"
                                                transform="translate(-1182 -337)" fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                                            <path id="Path_3014" data-name="Path 3014"
                                                d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1"
                                                transform="translate(-1181 -343.5)" fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                                            <path id="Path_3015" data-name="Path 3015"
                                                d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1"
                                                transform="translate(-1181 -346.5)" fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                                        </g>
                                    </svg>
                                </a>
                                @endif
                                    <!-- Re-order -->
                                    <a class="btn-soft-white rounded-3 btn-sm mr-1" href="{{ route('re_order', encrypt($order->id)) }}">
                                        {{ translate('Reorder') }}
                                    </a>



                                    <!-- Cancel -->
                                    @if ($order->delivery_status == 'confirmed' && $order->type =="Taiz" && $order->payment_status == 'unpaid')
                                        <a href="javascript:void(0)" class="btn btn-soft-danger rounded-3 btn-sm  mt-2 mt-sm-0 confirm-delete" data-href="{{route('purchase_history.destroy', $order->id)}}" title="{{ translate('Cancel') }}">
                                            Cancel
                                            {{-- <svg xmlns="http://www.w3.org/2000/svg" width="9.202" height="12" viewBox="0 0 9.202 12">
                                                <path id="Path_28714" data-name="Path 28714" d="M15.041,7.608l-.193,5.85a1.927,1.927,0,0,1-1.933,1.864H9.243A1.927,1.927,0,0,1,7.31,13.46L7.117,7.608a.483.483,0,0,1,.966-.032l.193,5.851a.966.966,0,0,0,.966.929h3.672a.966.966,0,0,0,.966-.931l.193-5.849a.483.483,0,1,1,.966.032Zm.639-1.947a.483.483,0,0,1-.483.483H6.961a.483.483,0,1,1,0-.966h1.5a.617.617,0,0,0,.615-.555,1.445,1.445,0,0,1,1.442-1.3h1.126a1.445,1.445,0,0,1,1.442,1.3.617.617,0,0,0,.615.555h1.5a.483.483,0,0,1,.483.483ZM9.913,5.178h2.333a1.6,1.6,0,0,1-.123-.456.483.483,0,0,0-.48-.435H10.516a.483.483,0,0,0-.48.435,1.6,1.6,0,0,1-.124.456ZM10.4,12.5V8.385a.483.483,0,0,0-.966,0V12.5a.483.483,0,1,0,.966,0Zm2.326,0V8.385a.483.483,0,0,0-.966,0V12.5a.483.483,0,1,0,.966,0Z" transform="translate(-6.478 -3.322)" fill="#d43533"/>
                                            </svg> --}}
                                        </a>
                                    @endif
                                    <!-- Details -->
                                    <a href="{{route('purchase_history.details', encrypt($order->id))}}" class="btn btn-soft-info btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" title="{{ translate('Order Details') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10">
                                            <g id="Group_24807" data-name="Group 24807" transform="translate(-1339 -422)">
                                                <rect id="Rectangle_18658" data-name="Rectangle 18658" width="12" height="1" transform="translate(1339 422)" fill="#3490f3"/>
                                                <rect id="Rectangle_18659" data-name="Rectangle 18659" width="12" height="1" transform="translate(1339 425)" fill="#3490f3"/>
                                                <rect id="Rectangle_18660" data-name="Rectangle 18660" width="12" height="1" transform="translate(1339 428)" fill="#3490f3"/>
                                                <rect id="Rectangle_18661" data-name="Rectangle 18661" width="12" height="1" transform="translate(1339 431)" fill="#3490f3"/>
                                            </g>
                                        </svg>
                                    </a>
                                    <!-- Invoice -->
                                    <a class="btn btn-soft-secondary-base btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12.001" viewBox="0 0 12 12.001">
                                            <g id="Group_24807" data-name="Group 24807" transform="translate(-1341 -424.999)">
                                              <path id="Union_17" data-name="Union 17" d="M13936.389,851.5l.707-.707,2.355,2.355V846h1v7.1l2.306-2.306.707.707-3.538,3.538Z" transform="translate(-12592.95 -421)" fill="#f3af3d"/>
                                              <rect id="Rectangle_18661" data-name="Rectangle 18661" width="12" height="1" transform="translate(1341 436)" fill="#f3af3d"/>
                                            </g>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                </tbody>

            </table>
            <!-- Pagination -->
            <div class="aiz-pagination mt-2">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <!-- Delete modal -->
    {{-- @include('modals.delete_modal') --}}
    @include('modals.cancel_product_modal')


      <!-- Chat Modal -->
      <div class="modal fade" id="chat_delivery_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
          <div class="modal-content position-relative">
              <div class="modal-header">
                  <h5 class="modal-title fw-600 h5">{{ translate('Chat with Assigned Delivery Boy') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <form class="" action="{{ route('conversations.store') }}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="delivery_boy_id" value="{{$delivery_boy_id!=null && $delivery_boy_id  }}">
                  <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                  <input type="hidden" name="title" value="User & delivery Boy Chat">

                  <div class="modal-body gry-bg px-3 pt-3">
                      <div class="form-group">
                          <input type="text" class="form-control mb-3 rounded-0" name="delivery_name"
                              value="{{ $delivery_boy }}" placeholder="{{ $delivery_boy!=null && $delivery_boy  }}"
                               disabled>
                      </div>
                      <div class="form-group">
                          <textarea class="form-control rounded-0" rows="8" name="message" required
                              placeholder="{{ translate('Your Question') }}"></textarea>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-outline-primary fw-600 rounded-0"
                          data-dismiss="modal">{{ translate('Cancel') }}</button>
                      <button type="submit" class="btn btn-primary fw-600 rounded-0 w-100px">{{ translate('Send') }}</button>
                  </div>
              </form>
          </div>
      </div>
  </div>

@endsection

@section('panel-script')
<script>
      function show_delivery_chat_modal() {


           $('#chat_delivery_modal').modal('show');

        }
</script>
@endsection
