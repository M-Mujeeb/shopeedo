<div class="modal-body">
    <p>Total Due: {{ number_format($total, 2) }}</p>
    <form action="{{ route('process-payment') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <input type="number" name="amount" class="form-control" placeholder="Enter amount">
        <button type="submit" class="btn btn-success mt-2">Pay Now</button>
    </form>
</div>
