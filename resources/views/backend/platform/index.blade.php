@extends('backend.layouts.app')

@section('content')
<h1>PlatForm Fees</h1>
<form action="{{ route('platform.store') }}" method="POST">
    @csrf
    <label for="">Enter Platform Fees:</label>
    <input type="text" name="platform_rate" value="{{ old('platform_rate', optional($platform_fee)->value) }}" required>
    <button class="btn-primary">Save</button>

</form>
@endsection
