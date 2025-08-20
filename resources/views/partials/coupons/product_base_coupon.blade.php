<div class="card-header mb-2" style="padding-left:0">
    <h3 class="h6">{{translate('Add Your Product Base Coupon')}}</h3>
</div>
<div class="form-group row">
    <label class="col-lg-3 col-from-label" for="code">{{translate('Coupon Code ')}}</label>
    <div class="col-lg-9">
        <input type="text" placeholder="{{translate('Coupon code')}}" id="code" name="code" class="form-control" required>
    </div>
</div>
<div class="product-choose-list">
    <div class="product-choose">
        <div class="form-group row">
            <label class="col-lg-3 col-from-label" for="name">{{translate('Product')}}</label>
            <div class="col-lg-9">
                <select name="product_ids[]" class="form-control product_id aiz-selectpicker" data-live-search="true" data-selected-text-format="count" required multiple>
                    <option value="">Select All</option>
                    @foreach($products as $product)
                        <option value="{{$product->id}}">{{ $product->getTranslation('name') }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<br>
<div class="form-group row">
    <label class="col-sm-3 control-label" for="start_date">{{translate('Date')}}</label>
    <div class="col-sm-9">
      <input type="text" class="form-control aiz-date-range" name="date_range" placeholder="{{ translate('Select date ') }}">
    </div>
</div>
<div class="form-group row">
   <label class="col-lg-3 col-from-label">{{translate('Discount')}}</label>
   <div class="col-lg-7">
      <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Discount')}}" name="discount" class="form-control" required>
   </div>
   <div class="col-lg-2">
       <select class="form-control aiz-selectpicker" name="discount_type">
           <option value="amount">{{translate('Amount')}}</option>
           <option value="percent">{{translate('Percent')}}</option>
       </select>
   </div>
</div>


<script type="text/javascript">

$(document).ready(function(){
        $('.aiz-date-range').daterangepicker();
        AIZ.plugins.bootstrapSelect('refresh');
    });


$(document).ready(function() {
    const selectElement = $('.product_id');
    const selectAllOptionValue = ''; // Assuming 'Select All' has an empty value

    // Handle select all/deselect all
    selectElement.on('changed.bs.select', function(event, clickedIndex, newValue) {
        const options = selectElement.find('option');
        const selectAllOption = selectElement.find(`option[value="${selectAllOptionValue}"]`);

        if (clickedIndex === 0) { // Check if 'Select All' was clicked
            if (newValue) {
                // If 'Select All' is selected, select all options
                options.prop('selected', true);
            } else {
                // If 'Select All' is deselected, deselect all options
                options.prop('selected', false);
            }
        } else {
            // Handle individual option selection/deselection
            const allOptionsSelected = options.not(`[value="${selectAllOptionValue}"]`).toArray().every(option => $(option).prop('selected'));
            selectAllOption.prop('selected', allOptionsSelected);
        }

        // Refresh the selectpicker to reflect the changes
        AIZ.plugins.bootstrapSelect('refresh');
    });
});




</script>
