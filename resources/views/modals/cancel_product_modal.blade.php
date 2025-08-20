<!-- delete Modal -->
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Cancel Confirmation')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1 fs-14" style="white-space: nowrap">{{translate('Are you sure you want to cancel Order')}}
                     {{-- @if(!empty($product->name))
                    <span id="product-name">{{ $product->name }}</span>
                @else
                    <span id="product-name">file</span>
                @endif ? --}}
            </p>
                {{-- <span id="product-name">{{ $product->name }}</span> --}}
                <button type="button" class="btn btn-secondary rounded-0 mt-2" data-dismiss="modal">{{translate('No')}}</button>
                <a href="" id="delete-link" class="btn btn-danger rounded-0 mt-2">{{translate('Yes')}}</a>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
