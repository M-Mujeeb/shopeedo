<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
  @csrf
  <input type="hidden" name="payment_method" value="card_payment">
</form>
